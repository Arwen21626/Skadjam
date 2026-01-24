#include "sender_img.h"

/* Envoie une image binaire au client selon le protocole défini.
   Paramètres :
     - fd : descripteur de socket du client
     - image : buffer contenant les données binaires de l’image
     - size : taille du buffer image en octets
   Logique :
     1. Convertit la commande CMD_IMG en chaîne protocolaire (cmd_to_str)
     2. Utilise push_binary() pour envoyer :
          - un header (CMD + SIZE)
          - les données binaires
          - le marqueur de fin "END"
   Retour :
     - 1 si l’envoi est réussi
     - 0 si push_binary() échoue
   Effets :
     - écrit dans les logs
     - envoie un flux binaire complet via la socket
*/
int send_img(int fd, const char *image, size_t size) {
    LOG_SERV(LOG_DEBUG, "send_img: début (taille=%zu)", size);

    char cmd[6];
    /* Conversion de la commande en chaîne protocolaire */
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_IMG));

    /* Envoi binaire via la fonction générique push_binary() */
    if (!push_binary(fd, image, size, cmd)) {
        LOG_SERV(LOG_ERROR,
                 "send_img: échec d'envoi (cmd=%s)",
                 cmd);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "send_img: envoi réussi (cmd=%s, taille=%zu)",
             cmd, size);

    return 1;
}
