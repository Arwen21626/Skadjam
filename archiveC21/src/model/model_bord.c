#include "model_bord.h"


int db_find_bord(PGconn *conn, char *id_commande, char *id_suivi, int size){
    PGresult *res;
    const char *params[1];
    params[0] = id_commande;
    res = PQexecParams(conn, 
        "SELECT id_suivi FROM _delivraptor WHERE id_commande = $1", 
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        PQclear(res);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "BORD BD AV %s", id_suivi);

    int n = PQntuples(res);
    if (n > 0){
        snprintf(id_suivi, size, "%s", PQgetvalue(res,0,0));
    }
    LOG_SERV(LOG_DEBUG, "BORD BD AP %s", id_suivi);
    PQclear(res);
    return 1;
}

int db_add_bord(PGconn *conn, char *id_suivi, char *id_commande){
    const char *params[2];
    PGresult *res;

    params[0] = id_suivi;
    params[1] = id_commande;

    res = PQexecParams(conn,
        "INSERT INTO _delivraptor (id_suivi, id_commande) VALUES ($1,$2)",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "Erreur INSERT : %s", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    PQclear(res);
    return 1;
}