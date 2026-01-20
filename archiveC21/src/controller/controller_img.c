#include "controller_img.h"

static int parse_img_request(const char *buffer, char *id_suivi) {
    char temp[16];

    int matched = sscanf(buffer,
        "%15s %254s",
        temp,
        id_suivi
    );

    if (matched != 2) {
        LOG_SERV(LOG_WARN, "ETA: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "ETA: données extraites avec succès");
    return 1;
}

void get_img(PGconn *conn, int fd, char buffer[TAILLEB]){
    FILE *image;
    long size;
    char *buff_img;
    char id_suivi[16];

    if (!parse_img_request(buffer, id_suivi)) {
        LOG_SERV(LOG_ERROR, "ETA ERR FORMAT");
        return;
    }

    LOG_SERV(LOG_INFO, "ETAT LVRAB id %s process...", id_suivi);
    if (!db_get_image(conn, id_suivi, &image)){
        LOG_SERV(LOG_ERROR, "ETA ERR IMAGE");
        return;
    }

    LOG_SERV(LOG_INFO, "ETAT LVRAB id %s size...", id_suivi);

    if (fseek(image, 0, SEEK_END)!=0){
        LOG_SERV(LOG_ERROR, "READ fseek : %s", strerror(errno));
        fclose(image);
        return;
    }

    size = ftell(image);
    buff_img = malloc(size);

    fseek(image, 0, SEEK_SET);

    LOG_SERV(LOG_INFO, "ETAT LVRAB id %s read...", id_suivi);
    size_t read = fread(buff_img, 1, size, image);
    if (size != read){
        LOG_SERV(LOG_ERROR, "READ size : read %d size %d", read, size);
        fclose(image);
        free(buff_img);
        return;
    }

    LOG_SERV(LOG_INFO, "IMG SIZE %d content %02X", read, buff_img);

    LOG_SERV(LOG_INFO, "IMG id %s send...", id_suivi);
    if (!send_img(fd, buff_img, read)){
        LOG_SERV(LOG_ERROR,"IMG SEND IMAGE");
        fclose(image);
        free(buff_img);
        return;
    }

    LOG_SERV(LOG_INFO, "ETAT LVRAB id %s close...", id_suivi);
    fclose(image);
    free(buff_img);
    LOG_SERV(LOG_INFO, "ETAT LVRAB id %s close", id_suivi);

}