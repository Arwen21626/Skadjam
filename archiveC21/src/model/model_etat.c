#include "model_etat.h"

int db_get_etat(PGconn *conn, const char *id_suivi, int *etat) {
    LOG_SERV(LOG_DEBUG, "db_get_etat: début (id_suivi=%s)", id_suivi);

    const char *params[1] = { id_suivi };

    PGresult *res = PQexecParams(conn,
        "SELECT etat FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "db_get_etat: erreur SELECT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    if (n > 0) {
        *etat = atoi(PQgetvalue(res, 0, 0));
        LOG_SERV(LOG_INFO, "db_get_etat: état récupéré (%d)", *etat);
    } else {
        LOG_SERV(LOG_WARN, "db_get_etat: aucun résultat pour %s", id_suivi);
    }

    PQclear(res);
    return 1;
}

int db_get_raison(PGconn *conn, const char *id_suivi, char *raison) {
    LOG_SERV(LOG_DEBUG, "db_get_raison: début (id_suivi=%s)", id_suivi);

    const char *params[1] = { id_suivi };

    PGresult *res = PQexecParams(conn,
        "SELECT raison_refus FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "db_get_raison: erreur SELECT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    if (n > 0) {
        strcpy(raison, PQgetvalue(res, 0, 0));
        LOG_SERV(LOG_INFO, "db_get_raison: raison récupérée (%s)", raison);
    } else {
        LOG_SERV(LOG_WARN, "db_get_raison: aucune raison trouvée pour %s", id_suivi);
    }

    PQclear(res);
    return 1;
}

int db_get_all_etat(PGconn *conn, Bordereaux **list, int *count, int etat) {
    LOG_SERV(LOG_DEBUG, "db_get_all_etat: début (etat=%d)", etat);

    char etat_str[8];
    snprintf(etat_str, sizeof(etat_str), "%d", etat);

    const char *params[1] = { etat_str };

    PGresult *res = PQexecParams(conn,
        "SELECT id_suivi, etat FROM _delivraptor WHERE etat = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "db_get_all_etat: erreur SELECT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    *count = n;

    LOG_SERV(LOG_INFO, "db_get_all_etat: %d entrées trouvées", n);

    *list = malloc(sizeof(Bordereaux) * n);
    if (!*list) {
        LOG_SERV(LOG_ERROR, "db_get_all_etat: échec malloc");
        PQclear(res);
        return 0;
    }

    for (int i = 0; i < n; i++) {
        strncpy((*list)[i].id_suivi, PQgetvalue(res, i, 0), 63);
        (*list)[i].etat = atoi(PQgetvalue(res, i, 1));
    }

    LOG_SERV(LOG_DEBUG, "db_get_all_etat: liste chargée en mémoire");
    PQclear(res);
    return 1;
}

int db_update_etat(PGconn *conn, const char *id_suivi, int nouvel_etat) {
    LOG_SERV(LOG_DEBUG, "db_update_etat: début (id_suivi=%s, nouvel_etat=%d)", id_suivi, nouvel_etat);

    char etat_str[8];
    snprintf(etat_str, sizeof(etat_str), "%d", nouvel_etat);

    const char *params[2] = { etat_str, id_suivi };

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET etat = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    int ok = PQresultStatus(res) == PGRES_COMMAND_OK;

    if (ok) {
        LOG_SERV(LOG_INFO, "db_update_etat: mise à jour OK (%s -> %d)", id_suivi, nouvel_etat);
    } else {
        LOG_SERV(LOG_ERROR, "db_update_etat: erreur UPDATE (%s)", PQresultErrorMessage(res));
    }

    PQclear(res);
    return ok ? 1 : 0;
}

int db_update_raison(PGconn *conn, const char *id_suivi, char *message) {
    LOG_SERV(LOG_DEBUG, "db_update_raison: début (id_suivi=%s, message=%s)", id_suivi, message);

    const char *params[2] = { message, id_suivi };

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET raison_refus = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "db_update_raison: erreur UPDATE (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "db_update_raison: raison mise à jour (%s -> %s)", id_suivi, message);

    PQclear(res);
    return 1;
}
