#ifndef MODEL_IMG_H
#define MODEL_IMG_H

#include <stdio.h>
#include <postgresql/libpq-fe.h>
#include <errno.h>
#include "../bdd/bdd.h"
#include "../serveur/utils.h"
#include "../logger/logger.h"

int db_add_image(PGconn *conn, const char *id_suivi);
int db_get_image(PGconn *conn, const char *id_suivi, FILE **image);

#endif