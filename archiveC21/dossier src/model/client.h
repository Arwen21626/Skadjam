#ifndef CLIENT_H
#define CLIEN_H

#include "../log/logger.h"
#include <postgresql/libpq-fe.h>
#include <stdlib.h>
#include <netinet/in.h>
#include <string.h>
#include <ctype.h>
#include <errno.h>

#define TAILLEB 1024
extern int opt_f;
extern char *arg_f;

typedef struct{
    char nom[255];
    char prenom[255];
    char adresse[255];
    int codePostal;
}destinataire;

typedef struct{
    char entreprise[255];
    char adresse[255];
    int codePostal;
}expediteur;

typedef struct {
    char numCommande[255];
    char numSuivi[512];
    expediteur exp;
    destinataire dest;
}bordereaux;

extern char cIp[INET_ADDRSTRLEN];
extern int cPort;
extern PGconn *conn;

void add_bord(int fd, char buffer[TAILLEB], bordereaux *bord, time_t horo);
void handle_conn(int fd, const char *line);
int auth_user(const char *user, const char *pwd);


#endif