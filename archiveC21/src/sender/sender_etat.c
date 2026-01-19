#include "sender_etat.h"

int send_etat(int fd, char etat[6], char id_suivi[16]){

    char cmd[6];
    snprintf(cmd,6,"%s", cmd_to_str(CMD_ETA));

    if (!push(fd, etat, cmd)){
        LOG_SERV(LOG_ERROR, "Erreur d'envoi %s : %s", cmd, id_suivi);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "PARAM d'envoi %s : %s", cmd, id_suivi);

    return 1;
}