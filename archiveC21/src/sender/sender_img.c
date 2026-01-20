#include "sender_img.h"

int send_img(int fd, const char *image, size_t size){
    LOG_SERV(LOG_INFO, "SEND IMG start...");

    char cmd[6];
    snprintf(cmd,6,"%s", cmd_to_str(CMD_IMG));

    if (!push_binary(fd, image, size, cmd)){
        LOG_SERV(LOG_ERROR, "Erreur d'envoi %s",cmd);
        return 0;
    }
    LOG_SERV(LOG_DEBUG, "PARAM d'envoi img success");
    return 1;
}