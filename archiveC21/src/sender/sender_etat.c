#include "sender_etat.h"

int send_etat(int fd, char etat[6], char id_suivi[16]){

    char cmd[6];
    snprintf(cmd,6,"%s", cmd_to_str(CMD_ETA));
    char msg[255];
    snprintf(msg, sizeof(msg), "%s %s", etat, id_suivi);

    if (!push(fd, msg, cmd)){
        LOG_SERV(LOG_ERROR, "Erreur d'envoi %s : %s", cmd, id_suivi);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "PARAM d'envoi %s : %s", cmd, id_suivi);

    return 1;
}

int send_etat_msg(int fd, char etat[6], char id_suivi[16], char message[255]){
    char cmd[6];
    snprintf(cmd,6,"%s", cmd_to_str(CMD_ETA));
    char msg[255];
    snprintf(msg, sizeof(msg), "%s %s msg:%s", etat, id_suivi, message);

    if (!push(fd, msg, cmd)){
        LOG_SERV(LOG_ERROR, "Erreur d'envoi %s : %s", cmd, id_suivi);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "PARAM d'envoi %s : %s", cmd, id_suivi);

    return 1;
}