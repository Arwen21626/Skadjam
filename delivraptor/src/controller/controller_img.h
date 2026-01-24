#ifndef CONTROLLER_IMG_H
#define CONTROLLER_IMG_H

#include "../model/model_img.h"
#include "../sender/sender_img.h"

extern char cIp[INET_ADDRSTRLEN];
extern int cPort;

void get_img(PGconn *conn, int fd, char buffer[TAILLEB]);

#endif