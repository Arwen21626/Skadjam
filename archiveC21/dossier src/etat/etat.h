#ifndef ETAT_H
#define ETAT_H

#include <stdio.h>

typedef enum {
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

etat_t int_to_etat(const char *buffer);
char msg_etat(etat_t etat);



#endif