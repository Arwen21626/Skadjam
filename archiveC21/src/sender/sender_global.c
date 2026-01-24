#include "sender_global.h"

/* Envoie *exactement* len octets sur la socket fd.
   Objectif :
     - Garantir que tout le buffer est envoyé, même si send() n’envoie qu’une partie.
   Paramètres :
     - fd : socket client
     - buf : données à envoyer
     - len : taille totale à envoyer
   Logique :
     - Boucle jusqu’à ce que total == len
     - Gère les interruptions EINTR
     - Logue chaque envoi partiel
   Retour :
     - 1 si tout a été envoyé
     - 0 si send() échoue définitivement
   Effets :
     - écrit dans les logs
     - effectue plusieurs appels send() si nécessaire
*/
static int send_all(int fd, const void *buf, size_t len) {
    LOG_SERV(LOG_DEBUG, "send_all: début (len=%zu)", len);

    size_t total = 0;
    const char *p = buf;

    while (total < len) {
        ssize_t sent = send(fd, p + total, len - total, 0);

        /* Gestion des erreurs */
        if (sent <= 0) {
            if (errno == EINTR) {
                LOG_SERV(LOG_DEBUG, "send_all: interruption EINTR, reprise");
                continue;
            }

            LOG_SERV(LOG_ERROR,
                     "send_all: échec send() (%s)",
                     strerror(errno));
            return 0;
        }

        total += sent;
        LOG_SERV(LOG_DEBUG,
                 "send_all: %zd octets envoyés (%zu/%zu)",
                 sent, total, len);
    }

    LOG_SERV(LOG_DEBUG, "send_all: envoi terminé");
    return 1;
}

/* Envoie un message texte formaté selon le protocole :
       CMD <cmd>
       SIZE <taille>
       <message>
       END
   Paramètres :
     - fd : socket client
     - msg : contenu textuel à envoyer
     - cmd : code de commande (ex : "ETA", "BORD")
   Logique :
     1. Construit un header avec la commande et la taille du message
     2. Envoie le header
     3. Envoie le message brut
     4. Envoie la séquence de fin "\nEND\n"
   Retour :
     - 1 si tout est envoyé correctement
     - 0 si une étape échoue
   Effets :
     - écrit dans les logs
     - utilise send_all() pour garantir l’envoi complet
*/
int push(int fd, const char *msg, const char *cmd) {
    LOG_SERV(LOG_DEBUG,
             "push: préparation envoi texte (cmd=%s, taille=%zu)",
             cmd, strlen(msg));

    char header[128];
    size_t len = strlen(msg);

    /* Construction du header protocolaire */
    snprintf(header, sizeof(header),
             "CMD %s\nSIZE %zu\n",
             cmd, len);

    /* Envoi du header */
    if (!send_all(fd, header, strlen(header))) {
        LOG_SERV(LOG_ERROR,
                 "push: échec envoi header (cmd=%s)",
                 cmd);
        return 0;
    }

    /* Envoi du message */
    if (!send_all(fd, msg, len)) {
        LOG_SERV(LOG_ERROR,
                 "push: échec envoi message (cmd=%s)",
                 cmd);
        return 0;
    }

    /* Envoi du marqueur de fin */
    if (!send_all(fd, "\nEND\n", 5)) {
        LOG_SERV(LOG_ERROR,
                 "push: échec envoi fin (cmd=%s)",
                 cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "push: envoi réussi (cmd=%s)",
             cmd);
    return 1;
}

/* Envoie des données binaires selon le même protocole que push(),
   mais sans interpréter le contenu (images, fichiers, etc.).
   Paramètres :
     - fd : socket client
     - data : buffer binaire
     - size : taille du buffer
     - cmd : code de commande
   Logique :
     1. Envoie header (CMD + SIZE)
     2. Envoie les données binaires telles quelles
     3. Envoie "\nEND\n"
   Retour :
     - 1 si tout est envoyé
     - 0 si une étape échoue
   Effets :
     - écrit dans les logs
     - utilise send_all() pour garantir l’envoi complet
*/
int push_binary(int fd, const void *data, size_t size, char *cmd) {
    LOG_SERV(LOG_DEBUG,
             "push_binary: préparation envoi binaire (cmd=%s, taille=%zu)",
             cmd, size);

    char header[128];
    snprintf(header, sizeof(header),
             "CMD %s\nSIZE %zu\n",
             cmd, size);

    /* Envoi du header */
    if (!send_all(fd, header, strlen(header))) {
        LOG_SERV(LOG_ERROR,
                 "push_binary: échec envoi header (cmd=%s)",
                 cmd);
        return 0;
    }

    /* Envoi des données binaires */
    if (!send_all(fd, data, size)) {
        LOG_SERV(LOG_ERROR,
                 "push_binary: échec envoi données (cmd=%s)",
                 cmd);
        return 0;
    }

    /* Envoi du marqueur de fin */
    if (!send_all(fd, "\nEND\n", 5)) {
        LOG_SERV(LOG_ERROR,
                 "push_binary: échec envoi fin (cmd=%s)",
                 cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "push_binary: envoi binaire réussi (cmd=%s)",
             cmd);
    return 1;
}
