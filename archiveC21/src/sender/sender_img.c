#include "sender_img.h"

int send_img(int fd, const char *image, size_t size) {
    LOG_SERV(LOG_DEBUG, "send_img: début (taille=%zu)", size);

    char cmd[6];
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_IMG));

    if (!push_binary(fd, image, size, cmd)) {
        LOG_SERV(LOG_ERROR, "send_img: échec d'envoi (cmd=%s)", cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO, "send_img: envoi réussi (cmd=%s, taille=%zu)", cmd, size);
    return 1;
}
