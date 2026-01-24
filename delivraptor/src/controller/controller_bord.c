#include "controller_bord.h"

/* Analyse une requête ADD envoyée par le client.
   Format attendu : "ADD <ent> <id_commande>"
   Paramètres :
     - buffer : message brut reçu du client
     - id_commande : buffer de sortie (id de commande extrait)
     - ent : buffer de sortie (entreprise)
   Retour :
     - 1 si parsing OK
     - 0 si format invalide
   Effets :
     - écrit dans les logs serveur et client
*/
static int parse_add_request(const char *buffer, char *id_commande, char *ent) {
    LOG_SERV(LOG_DEBUG, "parse_add_request: début du parsing");

    char temp[16];  /* reçoit le mot-clé "ADD" */
    int matched = sscanf(buffer, "%15s %16s %16s", temp, ent, id_commande);

    if (matched != 3) {
        LOG_SERV(LOG_WARN,
                 "parse_add_request: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "parse_add_request: extraction OK (ent=%s, id_commande=%s)",
             ent, id_commande);

    /* cIp et cPort sont des variables globales représentant le client courant */
    LOG_CLIENT(LOG_INFO, cIp, cPort,
               "Requête ADD reçue et analysée");

    return 1;
}

/* Génère un identifiant de suivi (id_suivi) pour un bord.
   Logique :
     - prend les 3 premières lettres de 'ent' en majuscules
     - calcule un nombre basé sur horo + id_commande
     - concatène les deux pour former l'id_suivi
   Paramètres :
     - ent : nom de l'entité (ex : "alizon")
     - id_commande : identifiant de commande
     - id_suivi : buffer de sortie
     - size : taille du buffer id_suivi
   Variables externes :
     - horo : variable globale (timestamp)
   Retour :
     - 1 si OK
     - 0 si erreur
*/
static int create_bord(char *ent,
                       const char *id_commande,
                       char *id_suivi,
                       size_t size) {

    LOG_SERV(LOG_DEBUG,
             "create_bord: début (ent=%s, id_commande=%s)",
             ent, id_commande);

    /* Préfixe = 3 premières lettres de ent en majuscules */
    char pref[4] = {0};
    for (int i = 0; i < 3 && ent[i] != '\0'; i++) {
        pref[i] = toupper((unsigned char)ent[i]);
    }

    /* Génération numérique basée sur horo + id_commande */
    long value = horo + atoi(id_commande);

    /* Construction finale de l'id_suivi */
    if (snprintf(id_suivi, size, "%s%ld", pref, value) <= 0) {
        LOG_SERV(LOG_ERROR, "create_bord: échec création id_suivi");
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "create_bord: id_suivi généré = %s",
             id_suivi);

    return 1;
}

/* Traite une requête ADD reçue d’un client.
   Étapes :
     1. Parser la requête pour extraire ent + id_commande
     2. Vérifier si un bord existe déjà en base
     3. Si non, en créer un et l’insérer en DB
     4. Envoyer l'id_suivi au client
   Paramètres :
     - conn : connexion PostgreSQL
     - fd : socket client
     - buffer : message brut reçu
   Dépendances externes :
     - db_find_bord() : recherche en DB
     - db_add_bord() : insertion en DB
     - send_bord() : envoi au client
*/
void add_bord(PGconn *conn, int fd, char buffer[TAILLEB]) {
    LOG_SERV(LOG_DEBUG, "add_bord: début traitement");

    char id_commande[16];
    char ent[16];
    char id_suivi[16] = {0};  /* vide = aucun bord trouvé */

    /* 1. Parsing */
    if (!parse_add_request(buffer, id_commande, ent)) {
        LOG_SERV(LOG_ERROR, "add_bord: erreur parsing requête");
        return;
    }

    LOG_SERV(LOG_DEBUG,
             "add_bord: recherche bord existant pour id_commande=%s",
             id_commande);

    /* 2. Recherche en DB */
    if (!db_find_bord(conn, id_commande, id_suivi, sizeof(id_suivi))) {
        LOG_SERV(LOG_ERROR, "add_bord: erreur DB lors du find");
        return;
    }

    /* 3. Création si inexistant */
    if (strlen(id_suivi) == 0) {
        LOG_SERV(LOG_INFO,
                 "add_bord: aucun bord existant, création nécessaire");

        if (!create_bord(ent, id_commande, id_suivi, sizeof(id_suivi))) {
            LOG_SERV(LOG_ERROR, "add_bord: échec création bord");
            return;
        }

        if (!db_add_bord(conn, id_suivi, id_commande)) {
            LOG_SERV(LOG_ERROR, "add_bord: échec insertion DB");
            return;
        }

        LOG_SERV(LOG_INFO,
                 "add_bord: bord ajouté en DB (%s)",
                 id_suivi);

    } else {
        LOG_SERV(LOG_INFO,
                 "add_bord: bord déjà existant (%s)",
                 id_suivi);
    }

    /* 4. Envoi au client */
    LOG_SERV(LOG_DEBUG,
             "add_bord: envoi du bord au client (%s)",
             id_suivi);

    if (!send_bord(fd, id_suivi)) {
        LOG_SERV(LOG_ERROR, "add_bord: échec envoi au client");
        return;
    }

    LOG_SERV(LOG_INFO,
             "add_bord: traitement terminé avec succès");
}
