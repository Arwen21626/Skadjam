#ifndef CMD_H
#define CMD_H

typedef enum{
    CMD_UNKNOWN = 0,
    CMD_ADD,
    CMD_ETA,
    CMD_NEXT,
    CMD_CONN,
} cmd_t;

cmd_t str_to_cmd(const char *cmd);
cmd_t get_commande(const char *buffer);

#endif