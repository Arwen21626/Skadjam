#include "controller_img.h"

static int parse_img_request(const char *buffer, char *id_suivi) {
    LOG_SERV(LOG_DEBUG, "parse_img_request: début du parsing");

    char temp[16];
    int matched = sscanf(buffer, "%15s %254s", temp, id_suivi);

    if (matched != 2) {
        LOG_SERV(LOG_WARN, "parse_img_request: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "IMG: données extraites avec succès (id_suivi=%s)", id_suivi);
    return 1;
}

void get_img(PGconn *conn, int fd, char buffer[TAILLEB]) {
    LOG_SERV(LOG_DEBUG, "get_img: début traitement");

    FILE *image = NULL;
    long size;
    char *buff_img = NULL;
    char id_suivi[16];

    if (!parse_img_request(buffer, id_suivi)) {
        LOG_SERV(LOG_ERROR, "get_img: erreur parsing requête");
        return;
    }

    LOG_SERV(LOG_INFO, "get_img: récupération image pour id_suivi=%s", id_suivi);

    if (!db_get_image(conn, id_suivi, &image)) {
        LOG_SERV(LOG_ERROR, "get_img: image introuvable en BDD pour %s", id_suivi);
        return;
    }

    LOG_SERV(LOG_DEBUG, "get_img: calcul taille image");

    if (fseek(image, 0, SEEK_END) != 0) {
        LOG_SERV(LOG_ERROR, "get_img: erreur fseek (%s)", strerror(errno));
        fclose(image);
        return;
    }

    size = ftell(image);
    if (size <= 0) {
        LOG_SERV(LOG_ERROR, "get_img: taille image invalide (%ld)", size);
        fclose(image);
        return;
    }

    buff_img = malloc(size);
    if (!buff_img) {
        LOG_SERV(LOG_ERROR, "get_img: échec malloc (%ld octets)", size);
        fclose(image);
        return;
    }

    fseek(image, 0, SEEK_SET);

    LOG_SERV(LOG_DEBUG, "get_img: lecture image (%ld octets)", size);

    size_t read = fread(buff_img, 1, size, image);
    if (read != (size_t)size) {
        LOG_SERV(LOG_ERROR, "get_img: erreur fread (lu=%zu attendu=%ld)", read, size);
        fclose(image);
        free(buff_img);
        return;
    }

    LOG_SERV(LOG_INFO, "get_img: image chargée (taille=%zu)", read);

    LOG_SERV(LOG_DEBUG, "get_img: envoi image au client (id_suivi=%s)", id_suivi);

    if (!send_img(fd, buff_img, read)) {
        LOG_SERV(LOG_ERROR, "get_img: échec envoi image pour %s", id_suivi);
        fclose(image);
        free(buff_img);
        return;
    }

    LOG_SERV(LOG_INFO, "get_img: image envoyée avec succès (id_suivi=%s)", id_suivi);

    fclose(image);
    free(buff_img);

    LOG_SERV(LOG_DEBUG, "get_img: fin traitement pour id_suivi=%s", id_suivi);
}
