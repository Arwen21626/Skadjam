#include "utils.h"

char cIp[INET_ADDRSTRLEN];
int cPort;
int sPort = -1;
int sock;
int opt_f = 0;
char *arg_f;
int opt_p = 0;
time_t horo;

const char *cmd_to_str(cmd_t cmd){
    switch (cmd) {
        case CMD_BORD : return "BORD"; 
        case CMD_ADD : return "ADD"; 
        case CMD_ETA : return "ETA"; 
        case CMD_NEXT : return "NEXT"; 
        case CMD_CONN : return "CONN"; 
        case CMD_IMG : return "IMG";
        default : return "UNKNOWN";
    }
}

cmd_t str_to_cmd(const char *cmd){
    if (strcmp(cmd, "ADD") == 0) return CMD_ADD;
    if (strcmp(cmd, "ETA") == 0) return CMD_ETA;
    if (strcmp(cmd, "NEXT") == 0) return CMD_NEXT;
    if (strcmp(cmd, "CONN") == 0) return CMD_CONN;
    if (strcmp(cmd, "IMG") == 0) return CMD_IMG;
    return CMD_UNKNOWN;
}

cmd_t get_commande(const char *buffer){
    char commande[16];
    cmd_t cmd;
    /* Use width limit to avoid overflow */
    sscanf(buffer, "%15s", commande);
    cmd = str_to_cmd(commande);
    return cmd;
}

void chomp(char *s) {
    size_t len = strlen(s);
    while (len > 0 && (s[len - 1] == '\n' || s[len - 1] == '\r')) {
        s[--len] = '\0';
    }
}