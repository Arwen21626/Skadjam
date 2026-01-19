#include "sender_bord.h"
/*
static void send_bordereau_response(int fd, const Bordereaux *bord) {
    char message[1024];
    snprintf(message, sizeof(message), "BORD %s com%s\n",
             bord->numSuivi, bord->numCommande);

    if (send(fd, message, strlen(message), 0) <= 0) {
        LOG_SERV(LOG_WARN, "Client déconnecté avant réception du bordereau");
        return;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Réponse envoyée : %s", message);
}*/

int send_bord(int fd, char *id_suivi){
    char cmd[6];
    snprintf(cmd,6,"%s", cmd_to_str(CMD_BORD));

    if (!push(fd, id_suivi, cmd)) {
        LOG_SERV(LOG_ERROR, "Erreur d'envoi %s : %s", cmd, id_suivi);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "PARAM d'envoi %s : %s", cmd, id_suivi);

    return 1;
}