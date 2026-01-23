#include "controller_bord.h"

static int parse_add_request(const char *buffer, char *id_commande, char *ent) {
    LOG_SERV(LOG_DEBUG, "parse_add_request: début du parsing");

    char temp[16];
    int matched = sscanf(buffer, "%15s %16s %16s", temp, ent, id_commande);

    if (matched != 3) {
        LOG_SERV(LOG_WARN, "parse_add_request: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_SERV(LOG_INFO, "parse_add_request: extraction OK (ent=%s, id_commande=%s)", ent, id_commande);
    LOG_CLIENT(LOG_INFO, cIp, cPort, "Requête ADD reçue et analysée");
    return 1;
}

static int create_bord(char *ent, const char *id_commande, char *id_suivi, size_t size) {
    LOG_SERV(LOG_DEBUG, "create_bord: début (ent=%s, id_commande=%s)", ent, id_commande);

    char pref[4] = {0};
    for (int i = 0; i < 3 && ent[i] != '\0'; i++) {
        pref[i] = toupper((unsigned char)ent[i]);
    }

    long value = horo + atoi(id_commande);
    if (snprintf(id_suivi, size, "%s%ld", pref, value) <= 0) {
        LOG_SERV(LOG_ERROR, "create_bord: échec création id_suivi");
        return 0;
    }

    LOG_SERV(LOG_INFO, "create_bord: id_suivi généré = %s", id_suivi);
    return 1;
}

void add_bord(PGconn *conn, int fd, char buffer[TAILLEB]) {
    LOG_SERV(LOG_DEBUG, "add_bord: début traitement");

    char id_commande[16];
    char ent[16];
    char id_suivi[16] = {0};

    if (!parse_add_request(buffer, id_commande, ent)) {
        LOG_SERV(LOG_ERROR, "add_bord: erreur parsing requête");
        return;
    }

    LOG_SERV(LOG_DEBUG, "add_bord: recherche bord existant pour id_commande=%s", id_commande);

    if (!db_find_bord(conn, id_commande, id_suivi, sizeof(id_suivi))) {
        LOG_SERV(LOG_ERROR, "add_bord: erreur DB lors du find");
        return;
    }

    if (strlen(id_suivi) == 0) {
        LOG_SERV(LOG_INFO, "add_bord: aucun bord existant, création nécessaire");

        if (!create_bord(ent, id_commande, id_suivi, sizeof(id_suivi))) {
            LOG_SERV(LOG_ERROR, "add_bord: échec création bord");
            return;
        }

        if (!db_add_bord(conn, id_suivi, id_commande)) {
            LOG_SERV(LOG_ERROR, "add_bord: échec insertion DB");
            return;
        }

        LOG_SERV(LOG_INFO, "add_bord: bord ajouté en DB (%s)", id_suivi);

    } else {
        LOG_SERV(LOG_INFO, "add_bord: bord déjà existant (%s)", id_suivi);
    }

    LOG_SERV(LOG_DEBUG, "add_bord: envoi du bord au client (%s)", id_suivi);

    if (!send_bord(fd, id_suivi)) {
        LOG_SERV(LOG_ERROR, "add_bord: échec envoi au client");
        return;
    }

    LOG_SERV(LOG_INFO, "add_bord: traitement terminé avec succès");
}
