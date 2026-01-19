#include "model_etat.h"

int db_get_etat(PGconn *conn, const char *id_suivi, int *etat){
    PGresult *res;
    const char *params[1];
    params[0] = id_suivi;
    res = PQexecParams(conn, 
        "SELECT etat FROM _delivraptor WHERE id_suivi = $1", 
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        PQclear(res);
        return 0;
    }
    int n = PQntuples(res);
    if (n>0){
        *etat = atoi(PQgetvalue(res, 0, 0));
    }

    PQclear(res);
    return 1;
}

int db_get_raison(PGconn *conn, const char *id_suivi, char *raison){
    const char *params[1];
    params[0] = id_suivi;
    PGresult *res = PQexecParams(conn, 
        "SELECT raison_refus FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    LOG_SERV(LOG_INFO, "RAISON GET : %s etat %d", id_suivi, raison);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    if (n>0){
        strcpy(raison, PQgetvalue(res, 0, 0));
    }

    PQclear(res);
    return 1;
}

int db_get_all_etat(PGconn *conn, Bordereaux **list, int *count) {
    LOG_SERV(LOG_DEBUG, "GET ALL ETA ARR");
    PGresult *res = PQexec(conn,
        "SELECT id_suivi, etat FROM _delivraptor WHERE etat < 9");
    
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        PQclear(res);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "GET ALL ETA DB success");

    int n = PQntuples(res);
    *count = n;

    *list = malloc(sizeof(Bordereaux) * n);

    LOG_SERV(LOG_DEBUG, "GET ALL ETA DB create bord...");
    for (int i = 0; i < n; i++) {
        strncpy((*list)[i].id_suivi, PQgetvalue(res, i, 0), 63);
        (*list)[i].etat = atoi(PQgetvalue(res, i, 1));
    }
    LOG_SERV(LOG_DEBUG, "GET ALL ETA DB create bord fin");
    PQclear(res);
    return 1;
}

int db_update_etat(PGconn *conn, const char *id_suivi, int nouvel_etat) {
    char etat_str[8];
    snprintf(etat_str, sizeof(etat_str), "%d", nouvel_etat);

    const char *params[2] = { etat_str, id_suivi };

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET etat = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    LOG_SERV(LOG_INFO, "ETA UPDATE : %s etat %d", id_suivi, nouvel_etat);
    int ok = PQresultStatus(res) == PGRES_COMMAND_OK;
    PQclear(res);
    return ok ? 1 : 0;
}

int db_update_raison(PGconn *conn, const char *id_suivi, char *message){
    const char *params[2];
    params[0] = message;
    params[1] = id_suivi;
    LOG_SERV(LOG_DEBUG, "raison : %s id_suivi : %s", message, id_suivi);
    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET raison_refus = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "err update raison %s",PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "RAISON UPDATE : %s raison %d", id_suivi, message);
    int ok = PQresultStatus(res) == PGRES_COMMAND_OK;
    PQclear(res);
    return ok ? 1 : 0;
}