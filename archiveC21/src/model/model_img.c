#include "model_img.h"

int db_add_image(PGconn *conn, const char *id_suivi){
    const char *params[2];
    char *adresse = "image/livraison.jpg";

    LOG_SERV(LOG_INFO, "IMAGE UPDATE REQUEST : id %s add %s...", id_suivi, adresse);

    params[0] = adresse;
    params[1] = id_suivi;
    PGresult *res = PQexecParams(conn, 
        "UPDATE _delivraptor SET image_lvr = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK){

        LOG_SERV(LOG_ERROR, "UPDATE image : %s", PQresultErrorMessage(res));

        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "UPDATE image");

    PQclear(res);
    return 1;
}

int db_get_image(PGconn *conn, const char *id_suivi, FILE **image){
    const char*params[1];
    char adresse[255];

    LOG_SERV(LOG_INFO, "IMAGE GET REQUEST : id %s", id_suivi);

    params[0] = id_suivi;
    PGresult *res = PQexecParams(conn,
        "SELECT image_lvr FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK){
        LOG_SERV(LOG_ERROR, "SELECT image : %s", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }
    if (PQntuples(res)<1){
        LOG_SERV(LOG_ERROR, "SELECT RES : resultat inferieur a 1");
        PQclear(res);
        return 0;
    }

    strcpy(adresse, PQgetvalue(res, 0, 0));
    PQclear(res);
    
    LOG_SERV(LOG_INFO, "IMAGE GET REQUEST SUCCESS : id %s adresse %s", id_suivi, adresse);

    *image = fopen(adresse, "rb");
    if (!*image){
        LOG_SERV(LOG_ERROR, "READ FILE : %s", strerror(errno));
        return 0;
    }
    LOG_SERV(LOG_INFO, "IMAGE OPEN : id %s", id_suivi);

    return 1;
}