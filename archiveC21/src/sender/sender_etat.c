#include "sender_etat.h"

int send_etat(int fd, char etat[6], char id_suivi[16]) {
    LOG_SERV(LOG_DEBUG, "send_etat: préparation de l'envoi (etat=%s, id_suivi=%s)", etat, id_suivi);

    char cmd[6];
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_ETA));

    char msg[255];
    snprintf(msg, sizeof(msg), "%s %s", etat, id_suivi);

    if (!push(fd, msg, cmd)) {
        LOG_SERV(LOG_ERROR, "send_etat: échec d'envoi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO, "send_etat: envoi réussi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
    return 1;
}

int send_etat_msg(int fd, char etat[6], char id_suivi[16], char message[255]) {
    LOG_SERV(LOG_DEBUG, "send_etat_msg: préparation de l'envoi (etat=%s, id_suivi=%s, msg=%s)", etat, id_suivi, message);

    char cmd[6];
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_ETA));

    char msg[255];
    snprintf(msg, sizeof(msg), "%s %s msg:%s", etat, id_suivi, message);

    if (!push(fd, msg, cmd)) {
        LOG_SERV(LOG_ERROR, "send_etat_msg: échec d'envoi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO, "send_etat_msg: envoi réussi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
    return 1;
}
