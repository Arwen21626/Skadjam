#include "utils.h"

char cIp[INET_ADDRSTRLEN];
int cPort;
int sPort = -1;
int sock;
int opt_f = 0;
char *arg_f;
int opt_p = 0;
time_t horo;

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

void chomp(char *s) {
    LOG_SERV(LOG_DEBUG, "chomp: nettoyage fin de ligne");

    size_t len = strlen(s);
    size_t original_len = len;

    while (len > 0 && (s[len - 1] == '\n' || s[len - 1] == '\r')) {
        s[--len] = '\0';
    }

    if (len != original_len) {
        LOG_SERV(LOG_DEBUG, "chomp: caractères supprimés (%zu -> %zu)", original_len, len);
    }
}
