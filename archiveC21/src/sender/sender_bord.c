#include "sender_bord.h"

int send_bord(int fd, char *id_suivi) {
    LOG_SERV(LOG_DEBUG, "send_bord: préparation de l'envoi (id_suivi=%s)", id_suivi);

    char cmd[6];
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_BORD));

    if (!push(fd, id_suivi, cmd)) {
        LOG_SERV(LOG_ERROR, "send_bord: échec d'envoi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO, "send_bord: envoi réussi (cmd=%s, id_suivi=%s)", cmd, id_suivi);
    return 1;
}
