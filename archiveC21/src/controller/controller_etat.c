#include "controller_etat.h"


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

    if (!parse_eta_request(buffer, id_suivi)) {
        LOG_SERV(LOG_ERROR, "ETA ERR FORMAT");
        return;
    }

    if (!db_get_etat(conn, id_suivi, &etat)){
        LOG_SERV(LOG_ERROR, "ETA ERR SELECT");
        return;
    }

    snprintf(str_etat, sizeof(str_etat), "%s", etat_to_str(etat));

    if (!send_etat(fd, str_etat, id_suivi)){
        LOG_SERV(LOG_ERROR, "ETA ERR SEND");
        return;
    }
    
}

void avance(PGconn *conn) {
    Bordereaux *list = NULL;
    int count = 0;
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

        if (db_update_etat(conn, list[i].id_suivi, next) != 0) {
            LOG_SERV(LOG_ERROR, "AVANCE UPDATE pour %s", list[i].id_suivi);
        }
    }
    LOG_SERV(LOG_INFO, "AVANCE success");
    free(list);
}
