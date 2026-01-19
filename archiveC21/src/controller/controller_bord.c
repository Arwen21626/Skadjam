#include "controller_bord.h"

static int parse_add_request(const char *buffer, char *id_commande, char *ent) {
    char temp[16];

    int matched = sscanf(buffer,
        "%15s %16s %16s",
        temp,
        ent,
        id_commande
    );
    LOG_SERV(LOG_DEBUG, "CMD %s value %s", temp, id_commande);

    if (matched != 3) {
        LOG_SERV(LOG_WARN, "ADD: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "ADD: données extraites avec succès");
    return 1;
}

static int create_bord(char *ent, const char *id_commande, char *id_suivi, size_t size){
    LOG_SERV(LOG_DEBUG, "BORD CREATE NEW : id_commande %s horo %d", id_commande, horo);
    char pref[4] = {0};
    for (int i = 0; i < 3 && ent[i] != '\0'; i++) {
        pref[i] = toupper((unsigned char)ent[i]);
    }

    long value = horo + atoi(id_commande);
    if (snprintf(id_suivi, size, "%s%ld", pref, value)<=0){
        LOG_SERV(LOG_ERROR, "CREAT: erreur de creation id_suivi");
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "BORD CREATED : id_suivi %s", id_suivi);
    return 1;

}

void add_bord(PGconn *conn, int fd, char buffer[TAILLEB]) {
    char id_commande[16];
    char ent[16];
    char id_suivi[16] = {0};
    if (!parse_add_request(buffer, id_commande, ent)) {
        LOG_SERV(LOG_ERROR, "BORD ERR FORMAT");
        return;
    }
    LOG_SERV(LOG_DEBUG, "BORD AV FIND %s", id_suivi);

    if (!db_find_bord(conn, id_commande, id_suivi, sizeof(id_suivi))) {
        LOG_SERV(LOG_ERROR, "BORD ERR DB find");
        return;
    }
    LOG_SERV(LOG_DEBUG, "BORD IF EXIST %s", id_suivi);

    if (strlen(id_suivi) <= 0){
        if (!create_bord(ent, id_commande, id_suivi, sizeof(id_suivi))){
            LOG_SERV(LOG_ERROR, "BORD ERR CREATE");
            return ;
        }
        LOG_SERV(LOG_DEBUG, "BORD CREATE %s", id_suivi);

        if (!db_add_bord(conn, id_suivi, id_commande)){
            LOG_SERV(LOG_ERROR, "BORD ERR DB add");
            return;
        }
        LOG_SERV(LOG_DEBUG, "BORD ADD %s", id_suivi);
    }else {
        LOG_SERV(LOG_DEBUG, "BORD EXIST %s", id_suivi);
    }


    if (!send_bord(fd,id_suivi)){
        LOG_SERV(LOG_ERROR, "BORD ERR SEND");
        return;
    }
}