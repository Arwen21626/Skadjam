#include "bdd.h"

/* Connexion PostgreSQL globale.
   Utilisée par toutes les fonctions accédant à la BDD. */
PGconn *conn;

/* Initialise la connexion à la base PostgreSQL.
   - Lit les variables d’environnement (DB_HOST, DB_NAME, DB_USER, DB_PASSWORD)
   - Construit la chaîne de connexion
   - Ouvre la connexion via libpq
   - Configure le search_path
   - Logue chaque étape
   Retour :
   - EXIT_SUCCESS si tout est OK
   - EXIT_FAILURE en cas d’erreur
*/
int connexionBd() {
    LOG_SERV(LOG_DEBUG, "connexionBd: début initialisation connexion BDD");

    /* Récupération des paramètres de connexion via variables d’environnement */
    char *host = getenv("DB_HOST");
    char *dbname = getenv("DB_NAME");
    char *user = getenv("DB_USER");
    char *password = getenv("DB_PASSWORD");

    LOG_SERV(LOG_DEBUG,
             "connexionBd: variables env (host=%s, dbname=%s, user=%s)",
             host, dbname, user);

    /* Construction de la chaîne de connexion PostgreSQL */
    char connInfo[512];
    snprintf(connInfo, sizeof(connInfo),
             "host=%s dbname=%s user=%s password=%s",
             host, dbname, user, password);

    /* Ouverture de la connexion */
    conn = PQconnectdb(connInfo);

    /* Vérification de l’état de la connexion */
    if (PQstatus(conn) != CONNECTION_OK) {
        LOG_SERV(LOG_ERROR,
                 "connexionBd: échec connexion (%s)",
                 PQerrorMessage(conn));
        PQfinish(conn);
        return EXIT_FAILURE;
    }

    LOG_SERV(LOG_INFO, "connexionBd: connexion établie avec succès");

    /* Configuration du search_path pour pointer vers le schéma de travail */
    LOG_SERV(LOG_DEBUG, "connexionBd: configuration search_path");
    PGresult *res = PQexec(conn, "SET search_path TO sae3_delivraptor");

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR,
                 "connexionBd: erreur SET search_path (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return EXIT_FAILURE;
    }

    LOG_SERV(LOG_INFO, "connexionBd: search_path configuré");
    PQclear(res);

    /* Vérification de la base courante */
    res = PQexec(conn, "SELECT current_database()");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO,
                 "connexionBd: base courante = %s",
                 PQgetvalue(res, 0, 0));
    } else {
        LOG_SERV(LOG_WARN,
                 "connexionBd: impossible de récupérer current_database()");
    }
    PQclear(res);

    /* Vérification du search_path réellement appliqué */
    res = PQexec(conn, "SHOW search_path");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO,
                 "connexionBd: search_path actuel = %s",
                 PQgetvalue(res, 0, 0));
    } else {
        LOG_SERV(LOG_WARN,
                 "connexionBd: impossible de récupérer search_path");
    }
    PQclear(res);

    LOG_SERV(LOG_DEBUG, "connexionBd: initialisation terminée");
    return EXIT_SUCCESS;
}
