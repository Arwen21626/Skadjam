#ifndef MODEL_ETAT_H
#define MODEL_ETAT_H

#include <string.h>
#include <postgresql/libpq-fe.h>
#include "../bdd/bdd.h"
#include "../logger/logger.h"
#include "../serveur/utils.h"

int db_get_etat(PGconn *conn, const char *id_suivi, int *etat);
int db_get_all_etat(PGconn *conn, Bordereaux **list, int *count);
int db_update_etat(PGconn *conn, const char *id_suivi, int nouvel_etat);


#endif