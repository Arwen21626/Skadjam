#include "model_bord.h"

int db_find_bord(PGconn *conn, char *id_commande, char *id_suivi, int size) {
    LOG_SERV(LOG_DEBUG, "db_find_bord: début (id_commande=%s)", id_commande);

    const char *params[1] = { id_commande };

    PGresult *res = PQexecParams(conn,
        "SELECT id_suivi FROM _delivraptor WHERE id_commande = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "db_find_bord: erreur SELECT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int n = PQntuples(res);
    if (n > 0) {
        snprintf(id_suivi, size, "%s", PQgetvalue(res, 0, 0));
        LOG_SERV(LOG_INFO, "db_find_bord: id_suivi trouvé (%s)", id_suivi);
    } else {
        LOG_SERV(LOG_INFO, "db_find_bord: aucun bord trouvé pour id_commande=%s", id_commande);
    }

    PQclear(res);
    return 1;
}

int db_add_bord(PGconn *conn, char *id_suivi, char *id_commande) {
    LOG_SERV(LOG_DEBUG, "db_add_bord: début (id_suivi=%s, id_commande=%s)", id_suivi, id_commande);

    const char *params[2] = { id_suivi, id_commande };

    PGresult *res = PQexecParams(conn,
        "INSERT INTO _delivraptor (id_suivi, id_commande) VALUES ($1, $2)",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "db_add_bord: erreur INSERT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "db_add_bord: insertion réussie (%s -> %s)", id_suivi, id_commande);

    PQclear(res);
    return 1;
}
