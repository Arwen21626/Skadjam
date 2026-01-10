#include "cmd.h"
#include <string.h>
#include <stdio.h>

cmd_t str_to_cmd(const char *cmd){
    if (strcmp(cmd, "ADD") == 0) return CMD_ADD;
    if (strcmp(cmd, "ETA") == 0) return CMD_ETA;
    if (strcmp(cmd, "NEXT") == 0) return CMD_NEXT;
    return CMD_UNKNOWN;
}

cmd_t get_commande(const char *buffer){
    char commande[5];
    cmd_t cmd;
    sscanf(buffer, "%s", commande);
    cmd = str_to_cmd(commande);
    return cmd; 
}