#ifndef CONTROLLER_ETAT_H
#define CONTROLLER_ETAT_H

#include "../model/model_etat.h"
#include "../sender/sender_etat.h"
#include "../model/model_img.h"

typedef enum {
    INCONNU = 0,
    ETAT1,
    ETAT2,
    ETAT3,
    ETAT4,
    ETAT5,
    ETAT6,
    ETAT7,
    ETAT8,
    LVR,
    LVRAB,
    REFU
} etat_t;

extern char cIp[INET_ADDRSTRLEN];
extern int cPort;

void get_etat(PGconn *conn, int fd, char buffer[TAILLEB]);
void avance(PGconn *conn);



#endif