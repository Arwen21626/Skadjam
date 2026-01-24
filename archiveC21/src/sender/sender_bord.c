#include "sender_bord.h"

/* Envoie au client un message contenant un identifiant de suivi (id_suivi)
   associé à la commande CMD_BORD.
   Paramètres :
     - fd : descripteur de socket du client
     - id_suivi : identifiant de suivi à transmettre
   Logique :
     1. Convertit la commande CMD_BORD en chaîne protocolaire (cmd_to_str)
     2. Appelle push() pour envoyer (cmd, id_suivi) au client
   Retour :
     - 1 si l’envoi est réussi
     - 0 si push() échoue
   Effets :
     - écrit dans les logs
     - envoie un message formaté au client via la socket
*/
int send_bord(int fd, char *id_suivi) {
    LOG_SERV(LOG_DEBUG,
             "send_bord: préparation de l'envoi (id_suivi=%s)",
             id_suivi);

    char cmd[6];
    /* Conversion de la commande en chaîne (ex : "BORD") */
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_BORD));

    /* Envoi via la fonction générique push() */
    if (!push(fd, id_suivi, cmd)) {
        LOG_SERV(LOG_ERROR,
                 "send_bord: échec d'envoi (cmd=%s, id_suivi=%s)",
                 cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "send_bord: envoi réussi (cmd=%s, id_suivi=%s)",
             cmd, id_suivi);

    return 1;
}
