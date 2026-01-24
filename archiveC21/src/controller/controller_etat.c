#include "controller_etat.h"

/* Détermine le prochain état d’un bordereau.
   Logique :
     - ETAT1 → ETAT2 → ... → ETAT8
     - À partir de ETAT8, tirage aléatoire entre :
         LVR (livré), LVRAB (livraison avec anomalie), REFU (refus)
     - Les états finaux (LVR, LVRAB, REFU) sont stables.
   Paramètres :
     - etat : état actuel
   Retour :
     - prochain état calculé
   Effets :
     - écrit dans les logs
*/
etat_t next_etat(etat_t etat) {
    LOG_SERV(LOG_DEBUG, "next_etat: calcul du prochain état (etat=%d)", etat);

    switch (etat) {
        case ETAT1: return ETAT2;
        case ETAT2: return ETAT3;
        case ETAT3: return ETAT4;
        case ETAT4: return ETAT5;
        case ETAT5: return ETAT6;
        case ETAT6: return ETAT7;
        case ETAT7: return ETAT8;

        /* ETAT8 → tirage aléatoire */
        case ETAT8: {
            int ale = rand() % 3;
            LOG_SERV(LOG_DEBUG, "next_etat: ETAT8 -> tirage aléatoire=%d", ale);

            if (ale == 0) return LVR;
            if (ale == 1) return LVRAB;
            return REFU;
        }

        /* États terminaux : restent identiques */
        case LVR:   return LVR;
        case LVRAB: return LVRAB;
        case REFU:  return REFU;

        default:
            LOG_SERV(LOG_WARN, "next_etat: état inconnu (%d)", etat);
            return INCONNU;
    }
}

/* Tableau global contenant les raisons possibles d’un refus */
char raisonRefus[5][128] = {
    "Le colis est trop abimé",
    "Le colis a été ouvert",
    "Le colis n'a pas été commandé",
    "Le colis est arrivé trop tard",
    "Le colis bouge"
};

/* Convertit un état interne (enum) en code protocolaire (string).
   Utilisé pour envoyer l’état au client.
*/
static const char *etat_to_str(etat_t etat) {
    switch (etat) {
        case ETAT1: return "TRTC";
        case ETAT2: return "ACHTR";
        case ETAT3: return "ARRTR";
        case ETAT4: return "ACHPR";
        case ETAT5: return "ARRPR";
        case ETAT6: return "ACHCL";
        case ETAT7: return "ARRCL";
        case ETAT8: return "LVRSN";
        case LVR:   return "LVR";
        case LVRAB: return "LVRAB";
        case REFU:  return "REFU";
        default:    return "INCO";
    }
}

/* Analyse une requête ETA envoyée par le client.
   Format attendu : "ETA <id_suivi>"
   Paramètres :
     - buffer : message brut reçu
     - id_suivi : buffer de sortie
   Retour :
     - 1 si parsing OK
     - 0 si format invalide
   Effets :
     - écrit dans les logs serveur et client
*/
static int parse_eta_request(const char *buffer, char *id_suivi) {
    LOG_SERV(LOG_DEBUG, "parse_eta_request: début du parsing");

    char temp[16];  /* reçoit le mot-clé "ETA" */
    int matched = sscanf(buffer, "%15s %254s", temp, id_suivi);

    if (matched != 2) {
        LOG_SERV(LOG_WARN, "parse_eta_request: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_SERV(LOG_INFO, "parse_eta_request: extraction OK (id_suivi=%s)", id_suivi);
    LOG_CLIENT(LOG_INFO, cIp, cPort, "Requête ETA analysée avec succès");
    return 1;
}

/* Traite une requête ETA :
   Étapes :
     1. Parser la requête
     2. Récupérer l’état en DB
     3. Convertir l’état en code protocolaire
     4. Si REFU → récupérer la raison et envoyer message complet
     5. Sinon → envoyer l’état simple
   Paramètres :
     - conn : connexion PostgreSQL
     - fd : socket client
     - buffer : message reçu
*/
void get_etat(PGconn *conn, int fd, char buffer[TAILLEB]) {
    LOG_SERV(LOG_DEBUG, "get_etat: début traitement");

    int etat;
    char str_etat[6];
    char id_suivi[16];
    char message[255];

    if (!parse_eta_request(buffer, id_suivi)) {
        LOG_SERV(LOG_ERROR, "get_etat: erreur parsing requête");
        return;
    }

    LOG_SERV(LOG_DEBUG, "get_etat: récupération état pour %s", id_suivi);

    if (!db_get_etat(conn, id_suivi, &etat)) {
        LOG_SERV(LOG_ERROR, "get_etat: erreur DB SELECT état");
        return;
    }

    snprintf(str_etat, sizeof(str_etat), "%s", etat_to_str(etat));
    LOG_SERV(LOG_INFO, "get_etat: état actuel=%s", str_etat);

    /* Cas particulier : REFU → message complet */
    if (etat == REFU) {
        LOG_SERV(LOG_DEBUG, "get_etat: récupération raison refus");

        if (!db_get_raison(conn, id_suivi, message)) {
            LOG_SERV(LOG_ERROR, "get_etat: erreur DB SELECT raison");
            return;
        }

        if (!send_etat_msg(fd, str_etat, id_suivi, message)) {
            LOG_SERV(LOG_ERROR, "get_etat: erreur envoi message refus");
            return;
        }

        LOG_SERV(LOG_INFO, "get_etat: message refus envoyé");
    } else {
        /* Envoi simple */
        if (!send_etat(fd, str_etat, id_suivi)) {
            LOG_SERV(LOG_ERROR, "get_etat: erreur envoi état");
            return;
        }

        LOG_SERV(LOG_INFO, "get_etat: état envoyé");
    }
}

/* Fait avancer automatiquement l’état de tous les bordereaux.
   Logique :
     - Parcourt les états de ETAT8 → ETAT1
     - Récupère tous les bordereaux dans cet état
     - Pour chacun :
         * calcule le prochain état
         * met à jour en DB
         * si REFU → génère une raison aléatoire
         * si LVRAB → ajoute une image en DB
     - cap/cap_max : mécanisme limitant le nombre de transitions
   Paramètres :
     - conn : connexion PostgreSQL
   Effets :
     - mises à jour en DB
     - logs détaillés
     - libère la liste retournée par la DB
*/
void avance(PGconn *conn) {
    LOG_SERV(LOG_DEBUG, "avance: début traitement");

    srand(time(NULL));

    Bordereaux *list = NULL;
    int count = 0;
    int cap_max = 3;  /* limite dynamique */
    int cap = 0;
    char message[255];

    /* Parcours des états du plus avancé au plus ancien */
    for (int etat = ETAT8; etat >= ETAT1; etat--) {
        LOG_SERV(LOG_DEBUG, "avance: traitement état=%d", etat);

        int nb_modif = 0;

        if (!db_get_all_etat(conn, &list, &count, etat)) {
            LOG_SERV(LOG_ERROR, "avance: erreur DB SELECT all état=%d", etat);
            return;
        }

        LOG_SERV(LOG_INFO, "avance: %d bordereaux trouvés pour état=%d", count, etat);

        for (int i = 0; i < count; i++) {

            /* Condition de limitation des transitions */
            if ((etat > ETAT4 || etat == ETAT1) || cap > nb_modif) {

                int next = next_etat(list[i].etat);
                LOG_SERV(LOG_DEBUG, "avance: %s passe de %d à %d",
                         list[i].id_suivi, list[i].etat, next);

                if (!db_update_etat(conn, list[i].id_suivi, next)) {
                    LOG_SERV(LOG_ERROR, "avance: erreur UPDATE état pour %s", list[i].id_suivi);
                }

                /* Si refus → choisir une raison */
                if (next == REFU) {
                    int ale = rand() % 5;
                    snprintf(message, sizeof(message), "%s", raisonRefus[ale]);

                    LOG_SERV(LOG_DEBUG, "avance: refus pour %s (raison=%s)",
                             list[i].id_suivi, message);

                    db_update_raison(conn, list[i].id_suivi, message);
                }

                /* Si anomalie → ajouter une image */
                if (next == LVRAB) {
                    LOG_SERV(LOG_DEBUG, "avance: ajout image pour %s", list[i].id_suivi);
                    db_add_image(conn, list[i].id_suivi);
                }

                nb_modif++;
            }
        }

        /* Mise à jour du cap pour limiter les transitions */
        cap = cap_max - count + nb_modif;
        LOG_SERV(LOG_DEBUG, "avance: cap recalculé=%d", cap);
    }

    LOG_SERV(LOG_INFO, "avance: traitement terminé");
    free(list);
}
