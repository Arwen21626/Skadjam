#include "sender_etat.h"

/* Envoie au client un état simple (sans message de refus).
   Paramètres :
     - fd : descripteur de socket du client
     - etat : code d’état (ex : "LVR", "REFU", "ACHPR")
     - id_suivi : identifiant du bordereau concerné
   Logique :
     1. Convertit la commande CMD_ETA en chaîne protocolaire (cmd_to_str)
     2. Construit le message "<etat> <id_suivi>"
     3. Envoie le tout via push()
   Retour :
     - 1 si l’envoi est réussi
     - 0 si push() échoue
   Effets :
     - écrit dans les logs
     - envoie un message formaté au client via la socket
*/
int send_etat(int fd, char etat[6], char id_suivi[16]) {
    LOG_SERV(LOG_DEBUG,
             "send_etat: préparation de l'envoi (etat=%s, id_suivi=%s)",
             etat, id_suivi);

    char cmd[6];
    /* Conversion de la commande en chaîne protocolaire */
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_ETA));

    char msg[255];
    /* Construction du message envoyé au client */
    snprintf(msg, sizeof(msg), "%s %s", etat, id_suivi);

    /* Envoi via la fonction générique push() */
    if (!push(fd, msg, cmd)) {
        LOG_SERV(LOG_ERROR,
                 "send_etat: échec d'envoi (cmd=%s, id_suivi=%s)",
                 cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "send_etat: envoi réussi (cmd=%s, id_suivi=%s)",
             cmd, id_suivi);

    return 1;
}

/* Envoie au client un état accompagné d’un message explicatif.
   Utilisé principalement pour les refus (REFU).
   Paramètres :
     - fd : socket client
     - etat : code d’état (ex : "REFU")
     - id_suivi : identifiant du bordereau
     - message : texte explicatif (raison du refus)
   Logique :
     1. Convertit CMD_ETA en chaîne protocolaire
     2. Construit le message "<etat> <id_suivi> msg:<raison>"
     3. Envoie via push()
   Retour :
     - 1 si l’envoi est réussi
     - 0 si push() échoue
   Effets :
     - écrit dans les logs
     - envoie un message enrichi au client
*/
int send_etat_msg(int fd, char etat[6], char id_suivi[16], char message[255]) {
    LOG_SERV(LOG_DEBUG,
             "send_etat_msg: préparation de l'envoi (etat=%s, id_suivi=%s, msg=%s)",
             etat, id_suivi, message);

    char cmd[6];
    snprintf(cmd, sizeof(cmd), "%s", cmd_to_str(CMD_ETA));

    char msg[255];
    /* Format enrichi avec message explicatif */
    snprintf(msg, sizeof(msg), "%s %s msg:%s", etat, id_suivi, message);

    if (!push(fd, msg, cmd)) {
        LOG_SERV(LOG_ERROR,
                 "send_etat_msg: échec d'envoi (cmd=%s, id_suivi=%s)",
                 cmd, id_suivi);
        return 0;
    }

    LOG_SERV(LOG_INFO,
             "send_etat_msg: envoi réussi (cmd=%s, id_suivi=%s)",
             cmd, id_suivi);

    return 1;
}
