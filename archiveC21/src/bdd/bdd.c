#include "bdd.h"

PGconn *conn;

int connexionBd() {
    LOG_SERV(LOG_DEBUG, "connexionBd: début initialisation connexion BDD");

    char *host = getenv("DB_HOST");
    char *dbname = getenv("DB_NAME");
    char *user = getenv("DB_USER");
    char *password = getenv("DB_PASSWORD");

    LOG_SERV(LOG_DEBUG, "connexionBd: variables env (host=%s, dbname=%s, user=%s)", 
             host, dbname, user);

    char connInfo[512];
    snprintf(connInfo, sizeof(connInfo),
             "host=%s dbname=%s user=%s password=%s",
             host, dbname, user, password);

    conn = PQconnectdb(connInfo);

    if (PQstatus(conn) != CONNECTION_OK) {
        LOG_SERV(LOG_ERROR, "connexionBd: échec connexion (%s)", PQerrorMessage(conn));
        PQfinish(conn);
        return EXIT_FAILURE;
    }

    LOG_SERV(LOG_INFO, "connexionBd: connexion établie avec succès");

    LOG_SERV(LOG_DEBUG, "connexionBd: configuration search_path");
    PGresult *res = PQexec(conn, "SET search_path TO sae3_delivraptor");

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "connexionBd: erreur SET search_path (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return EXIT_FAILURE;
    }

    LOG_SERV(LOG_INFO, "connexionBd: search_path configuré");
    PQclear(res);

    res = PQexec(conn, "SELECT current_database()");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "connexionBd: base courante = %s", PQgetvalue(res, 0, 0));
    } else {
        LOG_SERV(LOG_WARN, "connexionBd: impossible de récupérer current_database()");
    }
    PQclear(res);

    res = PQexec(conn, "SHOW search_path");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "connexionBd: search_path actuel = %s", PQgetvalue(res, 0, 0));
    } else {
        LOG_SERV(LOG_WARN, "connexionBd: impossible de récupérer search_path");
    }
    PQclear(res);

    LOG_SERV(LOG_DEBUG, "connexionBd: initialisation terminée");
    return EXIT_SUCCESS;
}
