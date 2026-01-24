#include "model_img.h"

/* Ajoute une image de livraison à un bordereau.
   Paramètres :
     - conn : connexion PostgreSQL active
     - id_suivi : identifiant du bordereau à mettre à jour
   Logique :
     - L’image n’est pas stockée en BDD : seule son *chemin local* est enregistré.
     - UPDATE _delivraptor SET image_lvr = 'image/livraison.jpg'
   Retour :
     - 1 si la mise à jour SQL est réussie
     - 0 si erreur SQL
   Effets :
     - écrit dans les logs
     - met à jour la colonne image_lvr
*/
int db_add_image(PGconn *conn, const char *id_suivi) {
    LOG_SERV(LOG_DEBUG, "db_add_image: début (id_suivi=%s)", id_suivi);

    const char *params[2];
    const char *adresse = "image/livraison.jpg";  /* chemin statique de l’image */

    LOG_SERV(LOG_INFO,
             "db_add_image: mise à jour image (id_suivi=%s, fichier=%s)",
             id_suivi, adresse);

    params[0] = adresse;
    params[1] = id_suivi;

    PGresult *res = PQexecParams(conn,
        "UPDATE _delivraptor SET image_lvr = $1 WHERE id_suivi = $2",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_add_image: erreur UPDATE (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "db_add_image: mise à jour réussie (id_suivi=%s)",
             id_suivi);

    PQclear(res);
    return 1;
}

/* Récupère le fichier image associé à un bordereau.
   Paramètres :
     - conn : connexion PostgreSQL active
     - id_suivi : identifiant du bordereau
     - image : pointeur vers FILE* (sortie)
   Logique :
     1. SELECT image_lvr FROM _delivraptor WHERE id_suivi = $1
     2. Récupère le chemin du fichier image
     3. Ouvre le fichier en mode binaire ("rb")
   Retour :
     - 1 si l’image est trouvée et le fichier ouvert
     - 0 si erreur SQL, pas d’image, ou fichier introuvable
   Effets :
     - écrit dans les logs
     - ouvre un fichier 
*/
int db_get_image(PGconn *conn, const char *id_suivi, FILE **image) {
    LOG_SERV(LOG_DEBUG, "db_get_image: début (id_suivi=%s)", id_suivi);

    const char *params[1];
    char adresse[255];  /* chemin du fichier image */

    params[0] = id_suivi;

    PGresult *res = PQexecParams(conn,
        "SELECT image_lvr FROM _delivraptor WHERE id_suivi = $1",
        1, NULL, params, NULL, NULL, 0);

    /* Vérification SQL */
    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR,
                 "db_get_image: erreur SELECT (%s)",
                 PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    /* Vérifie qu’une image est associée */
    if (PQntuples(res) < 1) {
        LOG_SERV(LOG_WARN,
                 "db_get_image: aucune image trouvée pour %s",
                 id_suivi);
        PQclear(res);
        return 0;
    }

    /* Copie du chemin du fichier */
    snprintf(adresse, sizeof(adresse), "%s", PQgetvalue(res, 0, 0));
    PQclear(res);

    LOG_SERV(LOG_INFO,
             "db_get_image: chemin image récupéré (id_suivi=%s, fichier=%s)",
             id_suivi, adresse);

    /* Ouverture du fichier image */
    *image = fopen(adresse, "rb");
    if (!*image) {
        LOG_SERV(LOG_ERROR,
                 "db_get_image: erreur ouverture fichier (%s)",
                 strerror(errno));
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "db_get_image: fichier image ouvert (id_suivi=%s)",
             id_suivi);

    return 1;
}
