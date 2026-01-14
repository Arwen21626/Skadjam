#include "client.h"


static int parse_add_request(const char *buffer, bordereaux *bord);
static int db_find_or_create_bordereau(bordereaux *bord, time_t horo);
static void send_bordereau_response(int fd, const bordereaux *bord);

static int parse_add_request(const char *buffer, bordereaux *bord) {
    char temp[16];

    int matched = sscanf(buffer,
        "%15s %254s %254s |%254[^|]| %d %254s %254s |%254[^|]| %d",
        temp,
        bord->numCommande,
        bord->exp.entreprise,
        bord->exp.adresse,
        &bord->exp.codePostal,
        bord->dest.prenom,
        bord->dest.nom,
        bord->dest.adresse,
        &bord->dest.codePostal
    );

    if (matched != 9) {
        LOG_SERV(LOG_WARN, "ADD: format invalide (%d champs lus)", matched);
        return 0;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "ADD: données extraites avec succès");
    return 1;
}

static int db_find_or_create_bordereau(bordereaux *bord, time_t horo) {
    PGresult *res;
    const char *params[2];

    // Vérifier si déjà existant
    params[0] = bord->numCommande;
    res = PQexecParams(conn,
        "SELECT id_suivi FROM _delivraptor WHERE id_commande = $1",
        1, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "Erreur SELECT: %s", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    int nrows = PQntuples(res);

    if (nrows > 0) {
        // Déjà existant
        strncpy(bord->numSuivi, PQgetvalue(res, 0, 0), sizeof(bord->numSuivi)-1);
        bord->numSuivi[sizeof(bord->numSuivi)-1] = '\0';
        LOG_SERV(LOG_INFO, "Bordereau existant trouvé : %s", bord->numSuivi);
        PQclear(res);
        return 1;
    }

    PQclear(res);

    // Création d’un nouveau numéro de suivi
    LOG_SERV(LOG_INFO, "Création d'un nouveau bordereau");

    // Préfixe entreprise
    for (int i = 0; i < 3 && bord->exp.entreprise[i] != '\0'; i++) {
        bord->numSuivi[i] = toupper((unsigned char)bord->exp.entreprise[i]);
    }
    bord->numSuivi[3] = '\0';

    char prefix[4];
    strncpy(prefix, bord->numSuivi, 4);

    long cmdnum = strtol(bord->numCommande, NULL, 10);
    snprintf(bord->numSuivi, sizeof(bord->numSuivi), "%s%ld", prefix, cmdnum + horo);

    LOG_SERV(LOG_INFO, "Numéro de suivi généré : %s", bord->numSuivi);

    // Insertion en BDD
    params[0] = bord->numSuivi;
    params[1] = bord->numCommande;

    res = PQexecParams(conn,
        "INSERT INTO _delivraptor (id_suivi, id_commande) VALUES ($1,$2)",
        2, NULL, params, NULL, NULL, 0);

    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "Erreur INSERT : %s", PQresultErrorMessage(res));
        PQclear(res);
        return 0;
    }

    LOG_SERV(LOG_INFO, "INSERT réussi");
    PQclear(res);
    return 1;
}

static void send_bordereau_response(int fd, const bordereaux *bord) {
    char message[1024];
    snprintf(message, sizeof(message), "BORD %s com%s\n",
             bord->numSuivi, bord->numCommande);

    if (send(fd, message, strlen(message), 0) <= 0) {
        LOG_SERV(LOG_WARN, "Client déconnecté avant réception du bordereau");
        return;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Réponse envoyée : %s", message);
}

void add_bord(int fd, char buffer[TAILLEB], bordereaux *bord, time_t horo) {

    if (!parse_add_request(buffer, bord)) {
        send(fd, "BORD ERR FORMAT\n", 16, 0);
        return;
    }

    if (!db_find_or_create_bordereau(bord, horo)) {
        send(fd, "BORD ERR DB\n", 12, 0);
        return;
    }

    send_bordereau_response(fd, bord);
}

