#include "controller_etat.h"


etat_t next_etat(etat_t etat){
    srand(time(NULL));
    switch (etat) {
        case ETAT1 : return ETAT2;
        case ETAT2 : return ETAT3;
        case ETAT3 : return ETAT4;
        case ETAT4 : return ETAT5;
        case ETAT5 : return ETAT6;
        case ETAT6 : return ETAT7;
        case ETAT7 : return ETAT8;
        case ETAT8 : 
            int ale = rand() % 3;
            if (ale == 0) return LVR;
            if (ale == 1) return LVRAB;
            if (ale == 2) return REFU;
        case LVR : return LVR;
        case LVRAB : return LVRAB;
        case REFU : return REFU;
        default    : return INCONNU;
        
    }
}

// Refus possible
char raisonRefus[5][128] = {
    "Le colis est trop abimé",
    "Le colis a été ouvert",
    "Le colis n'a pas été commandé",
    "Le colis est arrivé trop tard",
    "Le colis bouge"
};



static const char *etat_to_str(etat_t etat){
    switch (etat) {
        case ETAT1 : return "TRTC";  // TRaitemenT de la Commande
        case ETAT2 : return "ACHTR"; // ACHeminement vers TRansporteur
        case ETAT3 : return "ARRTR"; // ARRivé chez le TRansporteur
        case ETAT4 : return "ACHPR"; // ACHeminement vers Plateforme Régionale
        case ETAT5 : return "ARRPR"; // Arrivé à la Plateforme Régionale
        case ETAT6 : return "ACHCL"; // ACHeminement vers Centre Local
        case ETAT7 : return "ARRCL"; // ARRivé au Centre Local
        case ETAT8 : return "LVRSN"; // En cours de LiVRaiSoN
        case LVR   : return "LVR";   // LiVRé
        case LVRAB : return "LVRAB"; // LiVRé ABscent
        case REFU  : return "REFU";  // REFUdé
        default    : return "INCO";  // INCOnnu
    }
}

static int parse_eta_request(const char *buffer, char *id_suivi) {
    char temp[16];

    int matched = sscanf(buffer,
        "%15s %254s",
        temp,
        id_suivi
    );

    if (matched != 2) {
        LOG_SERV(LOG_WARN, "ETA: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "ETA: données extraites avec succès");
    return 1;
}

void get_etat(PGconn *conn, int fd, char buffer[TAILLEB]){
    int etat;
    char str_etat[6];
    char id_suivi[16];
    char message[255];

    if (!parse_eta_request(buffer, id_suivi)) {
        LOG_SERV(LOG_ERROR, "ETA ERR FORMAT");
        return;
    }

    if (!db_get_etat(conn, id_suivi, &etat)){
        LOG_SERV(LOG_ERROR, "ETA ERR SELECT");
        return;
    }

    snprintf(str_etat, sizeof(str_etat), "%s", etat_to_str(etat));

    if (etat == REFU){
        if (!db_get_raison(conn, id_suivi, message)){
            LOG_SERV(LOG_ERROR, "ETA ERR SELECT RAISON");
            return;
        }

        if (!send_etat_msg(fd, str_etat, id_suivi, message)){
            LOG_SERV(LOG_ERROR, "ETA ERR SEND");
            return;
        }
    }else{
        
        if (!send_etat(fd, str_etat, id_suivi)){
            LOG_SERV(LOG_ERROR, "ETA ERR SEND");
            return;
        }
    }

    
}

void avance(PGconn *conn) {
    srand(time(NULL));
    Bordereaux *list = NULL;
    int count = 0;
    char message[255];
    LOG_SERV(LOG_DEBUG, "AVANCE ARR");
    if (!db_get_all_etat(conn, &list, &count)) {
        LOG_SERV(LOG_ERROR, "AVANCE SELECT");
        return;
    }
    LOG_SERV(LOG_INFO, "GET ALL ETAT success");

    LOG_SERV(LOG_DEBUG, "count value : %d", count);
    for (int i = 0; i < count; i++) {
        int next = next_etat(list[i].etat);
        LOG_SERV(LOG_DEBUG, "id=%s, etat=%d -> next=%d",
            list[i].id_suivi, list[i].etat, next);
            
            if (!db_update_etat(conn, list[i].id_suivi, next)) {
                LOG_SERV(LOG_ERROR, "AVANCE UPDATE pour %s", list[i].id_suivi);
            }
            if (next == REFU){
                int ale = rand() % 5;
                snprintf(message, sizeof(message), "%s", raisonRefus[ale]);
                if (!db_update_raison(conn, list[i].id_suivi, message)){
                    LOG_SERV(LOG_ERROR, "AVANCE UPDATE pour %s", list[i].id_suivi);
                }
            }
    }
    LOG_SERV(LOG_INFO, "AVANCE success");
    free(list);
}
