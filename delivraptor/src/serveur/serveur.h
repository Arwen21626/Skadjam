#ifndef SERV_H
#define SERV_H


#include <string.h>
#include <stdio.h>
#include <sys/types.h>
#include <sys/socket.h>
#include <stdlib.h>
#include <unistd.h>
#include <fcntl.h>
#include <errno.h>
#include <getopt.h>
#include <openssl/md5.h>
#include "utils.h"
#include "../logger/logger.h"
#include "../bdd/bdd.h"
#include "../controller/controller_bord.h"
#include "../controller/controller_etat.h"
#include "../controller/controller_img.h"

extern int opt_f;
extern int opt_p;
extern char *arg_f;
extern char cIp[INET_ADDRSTRLEN];
extern int cPort;
extern int sPort;
extern int sock;
extern time_t horo;
extern PGconn *conn;

void print_help();
void manage_opt(int argc, char *argv[]);
int init_server();
void run_server_loop();
void handle_client(int fd, struct sockaddr_in conn_addr);
void process_commands(int fd, char *buffer);
void handle_conn(int fd, const char *line);
int auth_user(const char *user, const char *pwd);
void md5_hash(const char *password, char *output);

#endif