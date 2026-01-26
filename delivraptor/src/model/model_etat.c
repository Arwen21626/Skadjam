#include "model_etat.h"

/* Récupère l’état d’un bordereau à partir de son id_suivi.
   Paramètres :
     - conn : connexion PostgreSQL active
     - id_suivi : identifiant du bordereau
     - etat : pointeur vers un int où stocker l’état récupéré
   Logique :
     - SELECT etat FROM _delivraptor WHERE id_suivi = $1
     - Si une ligne existe → convertit la valeur en int et la stocke dans *etat
   Retour :
     - 1 si la requête SQL s’est exécutée correctement
     - 0 si erreur SQL
   Effets :
     - écrit dans les logs
     - modifie *etat si trouvé
*/
int db_get_etat(PGconn *conn, const char *id_suivi, int *etat) {
    LOG_SERV(LOG_DEBUG, "db_get_etat: début (id_suivi=%s)", id_suivi);

    const char *params[1] = { id_suivi };

    PGresult *res = PQexecParams(conn,
        "SELECT etat FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    /* Vérification du succès de la requête */
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_get_etat: erreur SELECT (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    /* Vérifie si un résultat existe */
    int n = PQntuples(res);
    if (n > 0) {
        *etat = atoi(PQgetvalue(res, 0, 0));
        LOG_SERV(LOG_INFO,
                 "db_get_etat: état récupéré (%d)", *etat);
    } else {
        LOG_SERV(LOG_WARN,
                 "db_get_etat: aucun résultat pour %s", id_suivi);
    }

    PQclear(res);
    return 1;
}

/* Récupère la raison de refus d’un bordereau.
   Paramètres :
     - conn : connexion PostgreSQL
     - id_suivi : identifiant du bordereau
     - raison : buffer de sortie (chaîne récupérée)
   Logique :
     - SELECT raison_refus FROM _delivraptor WHERE id_suivi = $1
     - Copie la chaîne dans raison si trouvée
   Retour :
     - 1 si requête OK
     - 0 si erreur SQL
   Effets :
     - écrit dans les logs
     - remplit raison si trouvée
*/
int db_get_raison(PGconn *conn, const char *id_suivi, char *raison) {
    LOG_SERV(LOG_DEBUG, "db_get_raison: début (id_suivi=%s)", id_suivi);

    const char *params[1] = { id_suivi };

    PGresult *res = PQexecParams(conn,
        "SELECT raison_refus FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_get_raison: erreur SELECT (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    if (n > 0) {
        strcpy(raison, PQgetvalue(res, 0, 0));
        LOG_SERV(LOG_INFO,
                 "db_get_raison: raison récupérée (%s)", raison);
    } else {
        LOG_SERV(LOG_WARN,
                 "db_get_raison: aucune raison trouvée pour %s", id_suivi);
    }

    PQclear(res);
    return 1;
}

/* Récupère tous les bordereaux ayant un état donné.
   Paramètres :
     - conn : connexion PostgreSQL
     - list : pointeur vers un tableau dynamique de Bordereaux* (rempli par malloc)
     - count : nombre d’entrées trouvées
     - etat : état recherché
   Logique :
     - SELECT id_suivi, etat FROM _delivraptor WHERE etat = $1
     - Alloue un tableau de Bordereaux de taille n
     - Remplit chaque entrée avec id_suivi + etat
   Retour :
     - 1 si OK
     - 0 si erreur SQL ou malloc
   Effets :
     - allocation dynamique
     - écrit dans les logs
*/
int db_get_all_etat(PGconn *conn, Bordereaux **list, int *count, int etat) {
    LOG_SERV(LOG_DEBUG, "db_get_all_etat: début (etat=%d)", etat);

    char etat_str[8];
    snprintf(etat_str, sizeof(etat_str), "%d", etat);

    const char *params[1] = { etat_str };

    PGresult *res = PQexecParams(conn,
        "SELECT id_suivi, etat FROM _delivraptor WHERE etat = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_get_all_etat: erreur SELECT (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    *count = n;

    LOG_SERV(LOG_INFO,
             "db_get_all_etat: %d entrées trouvées", n);

    /* Allocation du tableau de résultats */
    *list = malloc(sizeof(Bordereaux) * n);
    if (!*list) {
        LOG_SERV(LOG_ERROR, "db_get_all_etat: échec malloc");
        PQclear(res);
        return 0;
    }

    /* Remplissage du tableau */
    for (int i = 0; i < n; i++) {
        strncpy((*list)[i].id_suivi, PQgetvalue(res, i, 0), 63);
        (*list)[i].etat = atoi(PQgetvalue(res, i, 1));
    }

    LOG_SERV(LOG_DEBUG, "db_get_all_etat: liste chargée en mémoire");
    PQclear(res);
    return 1;
}

/* Met à jour l’état d’un bordereau.
   Paramètres :
     - conn : connexion PostgreSQL
     - id_suivi : identifiant du bordereau
     - nouvel_etat : nouvel état à appliquer
   Logique :
     - UPDATE _delivraptor SET etat = $1 WHERE id_suivi = $2
   Retour :
     - 1 si OK
     - 0 si erreur SQL
   Effets :
     - écrit dans les logs
*/
int db_update_etat(PGconn *conn, const char *id_suivi, int nouvel_etat) {
    LOG_SERV(LOG_DEBUG,
             "db_update_etat: début (id_suivi=%s, nouvel_etat=%d)",
             id_suivi, nouvel_etat);

    char etat_str[8];
    snprintf(etat_str, sizeof(etat_str), "%d", nouvel_etat);

    const char *params[2] = { etat_str, id_suivi };

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET etat = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_update_etat: erreur UPDATE (%s)",
                 PQresultErrorMessage(res));
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "db_update_etat: mise à jour OK (%s -> %d)",
             id_suivi, nouvel_etat);

    PQclear(res);
    return 1;
}

/* Met à jour la raison de refus d’un bordereau.
   Paramètres :
     - conn : connexion PostgreSQL
     - id_suivi : identifiant du bordereau
     - message : raison du refus
   Logique :
     - UPDATE _delivraptor SET raison_refus = $1 WHERE id_suivi = $2
   Retour :
     - 1 si OK
     - 0 si erreur SQL
   Effets :
     - écrit dans les logs
*/
int db_update_raison(PGconn *conn, const char *id_suivi, char *message) {
    LOG_SERV(LOG_DEBUG,
             "db_update_raison: début (id_suivi=%s, message=%s)",
             id_suivi, message);

    const char *params[2] = { message, id_suivi };

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET raison_refus = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_update_raison: erreur UPDATE (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "db_update_raison: raison mise à jour (%s -> %s)",
             id_suivi, message);

    PQclear(res);
    return 1;
}
