#ifndef SENDER_ETAT_H
#define SENDER_ETAT_H

#include <sys/types.h>
#include <sys/socket.h>
#include "../logger/logger.h"
#include "../controller/controller_etat.h"
#include "sender_global.h"
#include "../serveur/utils.h"

int send_etat(int fd, char etat[6], char id_suivi[16]);
int send_etat_msg(int fd, char etat[6], char id_suivi[16], char message[255]);

#endif