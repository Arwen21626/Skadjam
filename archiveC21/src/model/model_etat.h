#ifndef MODEL_ETAT_H
#define MODEL_ETAT_H

#include <string.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <postgresql/libpq-fe.h>
#include <errno.h>
#include "../bdd/bdd.h"
#include "../logger/logger.h"
#include "../serveur/utils.h"

int db_get_etat(PGconn *conn, const char *id_suivi, int *etat);
int db_get_all_etat(PGconn *conn, Bordereaux **list, int *count);
int db_update_etat(PGconn *conn, const char *id_suivi, int nouvel_etat);
int db_update_raison(PGconn *conn, const char *id_suivi, char *message);
int db_get_raison(PGconn *conn, const char *id_suivi, char *raison);

#endif