#include "bdd.h"

PGconn *conn;


int connexionBd(){
    char *host = getenv("DB_HOST");
    char *dbname = getenv("DB_NAME");
    char *user = getenv("DB_USER");
    char *password = getenv("DB_PASSWORD");
    char connInfo[512];
    LOG_SERV(LOG_DEBUG, "variable env : DB_HOST %s DB_NAME %s DB_USER %s DB_PASS %s", host, dbname, user, password);
    snprintf(connInfo, sizeof(connInfo), "host=%s dbname=%s user=%s password=%s", host, dbname, user, password);
    conn = PQconnectdb(connInfo);

    if (PQstatus(conn) != CONNECTION_OK){
        LOG_SERV(LOG_ERROR, "Erreur connexion BDD : %s", PQerrorMessage(conn));
        PQfinish(conn);
        return EXIT_FAILURE;
    }
    LOG_SERV(LOG_INFO, "Connecté a la BDD");
    LOG_SERV(LOG_INFO, "SET search_path...");
    PGresult *res = PQexec(conn, "SET search_path TO sae3_delivraptor");
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "Erreur SET search_path: %s\n", PQresultErrorMessage(res));
    }
    LOG_SERV(LOG_INFO, "SET search_path success");
    PQclear(res);

    res = PQexec(conn, "SELECT current_database()");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Base de données actuelle : %s", PQgetvalue(res, 0, 0));
    }
    PQclear(res);

    res = PQexec(conn, "SHOW search_path");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Schéma courant (search_path) : %s", PQgetvalue(res, 0, 0));
    }
    PQclear(res);
    return EXIT_SUCCESS;
}