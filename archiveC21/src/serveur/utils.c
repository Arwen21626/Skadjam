#include "utils.h"

/* Variables globales utilisées dans tout le serveur :
   - cIp / cPort : IP et port du client actuellement traité
   - sPort : port d’écoute du serveur (défini via options)
   - sock : socket serveur
   - opt_f / arg_f : activation et chemin du fichier d’authentification
   - opt_p : indique si un port a été fourni
   - horo : timestamp utilisé pour générer des identifiants uniques
*/
char cIp[INET_ADDRSTRLEN];
int cPort;
int sPort = -1;
int sock;
int opt_f = 0;
char *arg_f;
int opt_p = 0;
time_t horo;

/* Convertit une commande interne (enum cmd_t)
   en chaîne protocolaire envoyée au client.
   Paramètres :
     - cmd : valeur de l’énumération CMD_*
   Retour :
     - chaîne constante ("ADD", "ETA", etc.)
   Effets :
     - écrit dans les logs
*/
const char *cmd_to_str(cmd_t cmd) {
    LOG_SERV(LOG_DEBUG, "cmd_to_str: conversion commande (%d)", cmd);

    switch (cmd) {
        case CMD_BORD: return "BORD";
        case CMD_ADD:  return "ADD";
        case CMD_ETA:  return "ETA";
        case CMD_NEXT: return "NEXT";
        case CMD_CONN: return "CONN";
        case CMD_IMG:  return "IMG";
        default:
            LOG_SERV(LOG_WARN, "cmd_to_str: commande inconnue (%d)", cmd);
            return "UNKNOWN";
    }
}

/* Convertit une chaîne reçue du client en commande interne (enum cmd_t).
   Paramètres :
     - cmd : chaîne brute ("ADD", "ETA", etc.)
   Retour :
     - valeur de l’énumération correspondante
     - CMD_UNKNOWN si non reconnu
   Effets :
     - écrit dans les logs
*/
cmd_t str_to_cmd(const char *cmd) {
    LOG_SERV(LOG_DEBUG, "str_to_cmd: analyse chaîne (%s)", cmd);

    if (strcmp(cmd, "ADD")  == 0) return CMD_ADD;
    if (strcmp(cmd, "ETA")  == 0) return CMD_ETA;
    if (strcmp(cmd, "NEXT") == 0) return CMD_NEXT;
    if (strcmp(cmd, "CONN") == 0) return CMD_CONN;
    if (strcmp(cmd, "IMG")  == 0) return CMD_IMG;

    LOG_SERV(LOG_WARN, "str_to_cmd: commande inconnue (%s)", cmd);
    return CMD_UNKNOWN;
}

/* Extrait la commande depuis une ligne brute reçue du client.
   Paramètres :
     - buffer : ligne complète (ex : "ADD AMAZON 123")
   Logique :
     - lit le premier mot
     - le convertit via str_to_cmd()
   Retour :
     - valeur enum cmd_t
   Effets :
     - écrit dans les logs
*/
cmd_t get_commande(const char *buffer) {
    LOG_SERV(LOG_DEBUG, "get_commande: extraction depuis buffer");

    char commande[16];
    if (sscanf(buffer, "%15s", commande) != 1) {
        LOG_SERV(LOG_WARN, "get_commande: impossible d'extraire la commande");
        return CMD_UNKNOWN;
    }

    cmd_t cmd = str_to_cmd(commande);
    LOG_SERV(LOG_INFO, "get_commande: commande détectée (%s)", commande);

    return cmd;
}

/* Supprime les caractères de fin de ligne (\n, \r) d’une chaîne.
   Paramètres :
     - s : chaîne modifiable
   Logique :
     - recule depuis la fin tant que le dernier caractère est \n ou \r
   Effets :
     - modifie la chaîne en place
     - écrit dans les logs si des caractères ont été retirés
*/
void chomp(char *s) {
    LOG_SERV(LOG_DEBUG, "chomp: nettoyage fin de ligne");

    size_t len = strlen(s);
    size_t original_len = len;

    while (len > 0 && (s[len - 1] == '\n' || s[len - 1] == '\r')) {
        s[--len] = '\0';
    }

    if (len != original_len) {
        LOG_SERV(LOG_DEBUG,
                 "chomp: caractères supprimés (%zu -> %zu)",
                 original_len, len);
    }
}
