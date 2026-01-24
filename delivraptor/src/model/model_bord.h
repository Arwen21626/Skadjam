#ifndef MODEL_BORD_H
#define MODEL_BORD_H

#include "../logger/logger.h"
#include "../serveur/utils.h"
#include "../bdd/bdd.h"
#include <postgresql/libpq-fe.h>

int db_find_bord(PGconn *conn, char *id_commande, char *id_suivi, int size);
int db_add_bord(PGconn *conn, char *id_suivi, char *id_commande);

#endif