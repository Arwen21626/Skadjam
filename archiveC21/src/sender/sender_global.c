#include "sender_global.h"


static int send_all(int fd, const void *buf, size_t len) {
    size_t total = 0;
    const char *p = buf;

    while (total < len) {
        ssize_t sent = send(fd, p + total, len - total, 0);
        if (sent <= 0) {
            if (errno == EINTR) continue;
            return 0;
        }
        LOG_SERV(LOG_DEBUG, " >> %s",p);
        total += sent;
    }
    return 1;
}

int push(int fd, const char *msg, const char *cmd) {
    char header[128];
    size_t len = strlen(msg);

    // 1) Ligne commande
    snprintf(header, sizeof(header), "CMD %s\nSIZE %zu\n", cmd, len);

    // Envoi header
    if (send_all(fd, header, strlen(header)) < 0)
        return 0;

    // 2) Envoi du message
    if (send_all(fd, msg, len) < 0)
        return 0;

    // 3) Ligne de fin
    if (send_all(fd, "\nEND\n", 5) < 0)
        return 0;

    return 1;
}

int push_binary(int fd, const void *data, size_t size, char cmd) {
    char header[128];

    snprintf(header, sizeof(header), "CMD %d\nSIZE %zu\n", cmd, size);

    if (send_all(fd, header, strlen(header)) < 0)
        return 0;

    if (send_all(fd, data, size) < 0)
        return 0;

    if (send_all(fd, "\nEND\n", 5) < 0)
        return 0;

    return 1;
}
