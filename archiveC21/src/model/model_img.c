#include "model_img.h"

int db_add_image(PGconn *conn, const char *id_suivi) {
    LOG_SERV(LOG_DEBUG, "db_add_image: début (id_suivi=%s)", id_suivi);

    const char *params[2];
    const char *adresse = "image/livraison.jpg";

    LOG_SERV(LOG_INFO, "db_add_image: mise à jour image (id_suivi=%s, fichier=%s)", id_suivi, adresse);

    params[0] = adresse;
    params[1] = id_suivi;

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET image_lvr = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "db_add_image: erreur UPDATE (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "db_add_image: mise à jour réussie (id_suivi=%s)", id_suivi);

    PQclear(res);
    return 1;
}

int db_get_image(PGconn *conn, const char *id_suivi, FILE **image) {
    LOG_SERV(LOG_DEBUG, "db_get_image: début (id_suivi=%s)", id_suivi);

    const char *params[1];
    char adresse[255];

    params[0] = id_suivi;

    PGresult *res = PQexecParams(conn,
        "SELECT image_lvr FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "db_get_image: erreur SELECT (%s)", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    if (PQntuples(res) < 1) {
        LOG_SERV(LOG_WARN, "db_get_image: aucune image trouvée pour %s", id_suivi);
        PQclear(res);
        return 0;
    }

    snprintf(adresse, sizeof(adresse), "%s", PQgetvalue(res, 0, 0));
    PQclear(res);

    LOG_SERV(LOG_INFO, "db_get_image: chemin image récupéré (id_suivi=%s, fichier=%s)", id_suivi, adresse);

    *image = fopen(adresse, "rb");
    if (!*image) {
        LOG_SERV(LOG_ERROR, "db_get_image: erreur ouverture fichier (%s)", strerror(errno));
        return 0;
    }

    LOG_SERV(LOG_INFO, "db_get_image: fichier image ouvert (id_suivi=%s)", id_suivi);
    return 1;
}
