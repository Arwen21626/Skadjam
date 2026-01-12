#include "etat.h"
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <stdarg.h>
#include <sys/socket.h>
#include <unistd.h>
#include "../log/logger.h"

static const char *etat_to_str(etat_t etat){
    switch (etat) {
        case ETAT1 : return "TRTC"; // TRaitemenT de la Commande
        case ETAT2 : return "ACHTR"; // ACHeminement vers TRansporteur
        case ETAT3 : return "ARRTR"; // ARRivé chez le TRansporteur
        case ETAT4 : return "ACHPR"; // ACHeminement vers Plateforme Régionale
        case ETAT5 : return "ARRPR"; // Arrivé à la Plateforme Régionale
        case ETAT6 : return "ACHCL"; // ACHeminement vers Centre Local
        case ETAT7 : return "ARRCL"; // ARRivé au Centre Local
        case ETAT8 : return "LVRSN"; // En cours de LiVRaiSoN
        case ETAT9 : return "LVR"; // LIVré
        default    : return "INCO"; //INCOnnu
    }
}


void msg_etat(int fd, etat_t etat, const char *num_suivi){
    LOG_SERV(LOG_DEBUG, " >> arrive fn msg_etat av test: %d", etat);
    if (etat == INCONNU){
        LOG_SERV(LOG_DEBUG, " >> INCONNU test: %d", etat);
    }else{
        LOG_SERV(LOG_DEBUG, " >> CONNU test: %d", etat);
    }
    LOG_SERV(LOG_DEBUG, " >> arrive fn msg_etat ap test: %d", etat);
    char message[1024];
    message[0]='\0';
     LOG_SERV(LOG_DEBUG, " >> arrive fn msg_etat av vrai: %d", etat);

     /* Do not initialize or close the global logger here. Logger should be
         managed by the process lifecycle (e.g. main). Re-initializing or
         closing it here could lead to double-free / corruption. */
     if (etat == INCONNU){
        LOG_SERV(LOG_DEBUG, " >> Message etat inconnu : %d",etat);
        snprintf(message, sizeof(message), "ETA ERR %s %s", 
                 etat_to_str(etat),
                 num_suivi);
    }else{
        LOG_SERV(LOG_DEBUG, " >> Message etat connu : %d",etat);
        snprintf(message, sizeof(message), "ETA %s %s",
                 etat_to_str(etat),
                 num_suivi);
    }
    LOG_SERV(LOG_DEBUG, "ETAT %d message : %s", etat, message);
    int size = strlen(message);
    send(fd, message, size, 0);
    LOG_SERV(LOG_DEBUG, "ETAT %d message : %s envoyé", etat, message);
    LOG_SERV(LOG_INFO, "ETAT %d message : %s envoyé", etat, message);
}

etat_t next_etat(etat_t etat){
    switch (etat) {
        case ETAT1 : return ETAT2;
        case ETAT2 : return ETAT3;
        case ETAT3 : return ETAT4;
        case ETAT4 : return ETAT5;
        case ETAT5 : return ETAT6;
        case ETAT6 : return ETAT7;
        case ETAT7 : return ETAT8;
        case ETAT8 : return ETAT9;
        case ETAT9 : return ETAT9;
        default    : return INCONNU;
        
    }
}

