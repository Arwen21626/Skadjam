#include "sender_global.h"

static int send_all(int fd, const void *buf, size_t len) {
    LOG_SERV(LOG_DEBUG, "send_all: début (len=%zu)", len);

    size_t total = 0;
    const char *p = buf;

    while (total < len) {
        ssize_t sent = send(fd, p + total, len - total, 0);

        if (sent <= 0) {
            if (errno == EINTR) {
                LOG_SERV(LOG_DEBUG, "send_all: interruption EINTR, reprise");
                continue;
            }

            LOG_SERV(LOG_ERROR, "send_all: échec send() (%s)", strerror(errno));
            return 0;
        }

        total += sent;
        LOG_SERV(LOG_DEBUG, "send_all: %zd octets envoyés (%zu/%zu)", sent, total, len);
    }

    LOG_SERV(LOG_DEBUG, "send_all: envoi terminé");
    return 1;
}

int push(int fd, const char *msg, const char *cmd) {
    LOG_SERV(LOG_DEBUG, "push: préparation envoi texte (cmd=%s, taille=%zu)", cmd, strlen(msg));

    char header[128];
    size_t len = strlen(msg);

    snprintf(header, sizeof(header), "CMD %s\nSIZE %zu\n", cmd, len);

    if (!send_all(fd, header, strlen(header))) {
        LOG_SERV(LOG_ERROR, "push: échec envoi header (cmd=%s)", cmd);
        return 0;
    }

    if (!send_all(fd, msg, len)) {
        LOG_SERV(LOG_ERROR, "push: échec envoi message (cmd=%s)", cmd);
        return 0;
    }

    if (!send_all(fd, "\nEND\n", 5)) {
        LOG_SERV(LOG_ERROR, "push: échec envoi fin (cmd=%s)", cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO, "push: envoi réussi (cmd=%s)", cmd);
    return 1;
}

int push_binary(int fd, const void *data, size_t size, char *cmd) {
    LOG_SERV(LOG_DEBUG, "push_binary: préparation envoi binaire (cmd=%s, taille=%zu)", cmd, size);

    char header[128];
    snprintf(header, sizeof(header), "CMD %s\nSIZE %zu\n", cmd, size);

    if (!send_all(fd, header, strlen(header))) {
        LOG_SERV(LOG_ERROR, "push_binary: échec envoi header (cmd=%s)", cmd);
        return 0;
    }

    if (!send_all(fd, data, size)) {
        LOG_SERV(LOG_ERROR, "push_binary: échec envoi données (cmd=%s)", cmd);
        return 0;
    }

    if (!send_all(fd, "\nEND\n", 5)) {
        LOG_SERV(LOG_ERROR, "push_binary: échec envoi fin (cmd=%s)", cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO, "push_binary: envoi binaire réussi (cmd=%s)", cmd);
    return 1;
}
