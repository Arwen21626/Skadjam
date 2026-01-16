#include "cmd.h"
#include <string.h>
#include <stdio.h>

cmd_t str_to_cmd(const char *cmd){
    if (strcmp(cmd, "ADD") == 0) return CMD_ADD;
    if (strcmp(cmd, "ETA") == 0) return CMD_ETA;
    if (strcmp(cmd, "NEXT") == 0) return CMD_NEXT;
    if (strcmp(cmd, "CONN") == 0) return CMD_CONN;
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