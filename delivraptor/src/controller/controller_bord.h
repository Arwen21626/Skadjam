#ifndef CONTROLLER_BORD_H
#define CONTROLLER_BORD_H

#include <ctype.h>
#include "../model/model_bord.h"
#include "../sender/sender_bord.h"
#include "../serveur/utils.h"
#include"../serveur/utils.h"

extern char cIp[INET_ADDRSTRLEN];
extern int cPort;
extern time_t horo;

void add_bord(PGconn *conn, int fd, char buffer[TAILLEB]);


#endif