#ifndef UTILS_H
#define UTILS_H

#include <string.h>
#include <stdio.h>
#include <netinet/in.h>
#include <arpa/inet.h>

#define TAILLEB 1024

typedef struct {
    char id_suivi[64];
    int etat;
} Bordereaux;

typedef enum{
    CMD_UNKNOWN = 0,
    CMD_BORD,
    CMD_ADD,
    CMD_ETA,
    CMD_NEXT,
    CMD_CONN,
    CMD_IMG,
} cmd_t;

const char *cmd_to_str(cmd_t cmd);

cmd_t str_to_cmd(const char *cmd);
cmd_t get_commande(const char *buffer);
void chomp(char *s);
#endif