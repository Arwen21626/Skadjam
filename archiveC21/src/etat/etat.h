#ifndef ETAT_H
#define ETAT_H

#include <stdio.h>

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
    ETAT9
} etat_t;

void msg_etat(int fd,
              etat_t etat,
              const char *num_suivi);

etat_t next_etat(etat_t etat);

#endif