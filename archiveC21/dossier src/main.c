#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include <time.h>
#include <regex.h>
#include <ctype.h>
#include <errno.h>
#include <postgresql/libpq-fe.h>
#include "log/logger.h"
#include "commande/cmd.h"
#include "etat/etat.h"

#define TAILLEB 1024

typedef struct{
    char nom[255];
    char prenom[255];
    char adresse[255];
    int codePostal;
}destinataire;

typedef struct{
    char entreprise[255];
    char adresse[255];
    int codePostal;
}expediteur;

typedef struct {
    char numCommande[255];
    char numSuivi[512];
    expediteur exp;
    destinataire dest;
}bordereaux;


// Déclaration
void add_bord(int fd, char buffer[TAILLEB], bordereaux *bord, time_t horo);
time_t getHoro();
int connexion(int fd, char mdp[128], char user[128]);
int connecxionBd();
void getEtat(int fd, char buffer[TAILLEB]);
void avance();
void chomp(char *s);

time_t horo;
char cIp[INET_ADDRSTRLEN];
int cPort;
PGconn *conn;

int main(int argc, char *argv[]) {
    int sock;
    int ret;
    int size;
    int cnx;
    char user[128], mdp[128];
    char commande[20];
    char buffer[TAILLEB];
    char message[1024];
    cmd_t cmd;
    char *line;
    log_init();

    
    LOG_SERV(LOG_INFO ,"Démarrage du service Delivraptor");

    sock = socket(AF_INET, SOCK_STREAM, 0);
    printf("SOCK = %d\n", sock);

    int opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt))) {
        LOG_SERV(LOG_ERROR, "setsockopt failed: %s", strerror(errno));
        perror("setsockopt");
        exit(EXIT_FAILURE);
    }
    LOG_SERV(LOG_INFO, "Socket configuré");

    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;

    addr.sin_addr.s_addr = inet_addr("127.0.0.1"); //Adresse ip
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080); //Choix du port d'ecoute
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    printf("BIND = %d\n",ret);
    
    if (ret!=0){
        LOG_SERV(LOG_ERROR, "Erreur socket : %s", strerror(errno));
        exit(EXIT_FAILURE);
    }
    LOG_SERV(LOG_INFO, "Adresse ip et port configurées");

    ret = listen(sock, 10); //Taille de la liste d'attente
    printf("LISTEN = %d\n",ret);
    if (ret==0){
        LOG_SERV(LOG_INFO, "Socket en écoute");
    }else{
        LOG_SERV(LOG_ERROR, "Erreur socket : %s", strerror(errno));
    }

    size = sizeof(conn_addr);
    while (1){
        cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
        if (cnx < 0) {
            LOG_SERV(LOG_ERROR, "accept failed: %s", strerror(errno));
            continue;
        }
        LOG_SERV(LOG_INFO, "Connexion acceptée (fd=%d)", cnx);

        pid_t pid = fork();
        if (pid < 0) {
            LOG_SERV(LOG_ERROR, "fork failed: %s", strerror(errno));
            close(cnx);
            continue;
        } else if (pid == 0) {
            /* enfant */
            LOG_SERV(LOG_INFO, "Process enfant démarré (pid=%d) pour fd=%d", getpid(), cnx);

            LOG_SERV(LOG_INFO, "Connexion à la BDD...");
            connecxionBd();

            if (cnx >= 0) {
                close(sock);
            } else {
                LOG_SERV(LOG_ERROR, "socket invalide après accept: %s", strerror(errno));
                close(cnx);
            }
            
            inet_ntop(AF_INET, &conn_addr.sin_addr, cIp, sizeof(cIp));
            cPort = ntohs(conn_addr.sin_port);
            LOG_CLIENT(LOG_INFO, cIp, cPort, "Client connecté");

            while (1) {
                size = read(cnx, buffer, TAILLEB-1);
                if (size <= 0) {
                    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client déconnecté");
                    close(cnx);
                    break;
                }
                buffer[size] = '\0';

                line = strtok(buffer, "\n");
                while (line) {
                    if (line) chomp(line);
                    /* extraire et logger la commande */
                    char cmdstr[16] = {0};
                    if (sscanf(line, "%15s", cmdstr) != 1) {
                        LOG_CLIENT(LOG_WARN, cIp, cPort, "Ligne mal formée reçue");
                        line = strtok(NULL, "\n");
                        continue;
                    }
                    cmd = get_commande(cmdstr);
                    horo = getHoro();
                    LOG_CLIENT(LOG_INFO, cIp, cPort, "Requête reçue: %s", cmdstr);

                    switch (cmd) {
                        case CMD_CONN: {
                            /* format: CONN user pwd */
                            if (sscanf(line, "%19s %127s %127s", commande, user, mdp) == 3) {
                                if (connexion(cnx, mdp, user) == 0) {
                                    LOG_CLIENT(LOG_INFO, cIp, cPort, "Authentification OK pour %s", user);
                                } else {
                                    LOG_CLIENT(LOG_WARN, cIp, cPort, "Authentification échouée pour %s", user);
                                }
                            } else {
                                LOG_CLIENT(LOG_WARN, cIp, cPort, "CONN: mauvais format");
                            }
                        } break;
                        case CMD_ADD: {
                            bordereaux bord;
                            add_bord(cnx, line, &bord, horo);
                        } break;
                        case CMD_ETA: {
                            getEtat(cnx, line);
                        } break;
                        case CMD_NEXT: {
                            avance();
                            send(cnx, "next success\n", strlen("next success\n"), 0);
                        } break;
                        default: {
                            LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande non reconnue: %s", cmdstr);
                            snprintf(message, sizeof(message), "CMD ERR NOT_EXIST %s", cmdstr);
                            cmd = CMD_UNKNOWN;
                            if (send(cnx, message, strlen(message), 0) <= 0) {
                                LOG_SERV(LOG_WARN, "client déconnecté");
                            }
                        } break;
                    }

                    line = strtok(NULL, "\n");
                }
            }

            close(cnx);
            _exit(0);
        }else{
            LOG_SERV(LOG_DEBUG, " <<Parent pid enfant : %d", pid);
            close(cnx);
            LOG_SERV(LOG_DEBUG, " <<Parent connexion ferme");
        }
    }
    log_close();
}

// Etape 1
// Etat livraison : Chez Alizon
// ADD numCommande entrepriseExp adresseExp cpExp  nomDest prenomDest adresseDest cpDest
void add_bord(int fd, char buffer[TAILLEB], bordereaux *bord, time_t horo){
    char message[1024];
    char err[8] = "BORD ERR";
    PGresult *res;
    const char *params[2];
    char temp[16];

    // récupération des informations de la requête
    // use %[ˆ|] to read fields that are wrapped between |...| and width limits to avoid overflow
    sscanf(buffer, "%15s %254s %254s |%254[^|]| %d %254s %254s |%254[^|]| %d",
        temp,
        bord->numCommande,
        bord->exp.entreprise,
        bord->exp.adresse,
        &bord->exp.codePostal,
        bord->dest.prenom,
        bord->dest.nom,
        bord->dest.adresse,
        &bord->dest.codePostal);

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Informaions récupérées avec succès");
    
    //verifier si la commande a deja un bordereau
    params[0] = bord->numCommande;
    res = PQexecParams(conn,
                        "SELECT id_suivi FROM _delivraptor WHERE id_commande = $1",
                        1,
                        NULL,
                        params,
                        NULL,
                        NULL,
                        0);

        if (PQresultStatus(res) != PGRES_TUPLES_OK) {
        LOG_SERV(LOG_ERROR, "Erreur SELECT: %s\n", PQresultErrorMessage(res));
        snprintf(message, sizeof(message), "%s NOT_FOUND\n", err);
        if (send(fd, message, strlen(message), 0) <= 0){
            LOG_SERV(LOG_WARN, "client déconnecté");
        }
        PQclear(res);
        return;
    }
    int nrows = PQntuples(res);

    if (nrows>0){
        LOG_SERV(LOG_INFO, "La commande existe deja");
        strncpy(bord->numSuivi, PQgetvalue(res,0,0), sizeof(bord->numSuivi)-1);
        bord->numSuivi[sizeof(bord->numSuivi)-1] = '\0';
        LOG_SERV(LOG_INFO, "Renvoi du bordereau");
    }else{
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Création bordereau pour la commande %s %s", bord->numCommande, bord->exp.entreprise);
        //creation numéro de suivi
        for(int i = 0; i < 3 && bord->exp.entreprise[i] != '\0'; i++) {
            bord->numSuivi[i] = toupper((unsigned char)bord->exp.entreprise[i]);
        }
        bord->numSuivi[3] = '\0';
        /* build tracking id directly into bord->numSuivi using a small prefix buffer to avoid large intermediate copies */
    char prefix[8];
    /* copy first 3 chars (we set 3-letter prefix above) to avoid large-source snprintf warnings */
    prefix[0] = bord->numSuivi[0];
    prefix[1] = bord->numSuivi[1];
    prefix[2] = bord->numSuivi[2];
    prefix[3] = '\0';
        long cmdnum = strtol(bord->numCommande, NULL, 10);
        if (cmdnum == 0 && bord->numCommande[0] != '0') cmdnum = 0; /* fallback if not numeric */
        snprintf(bord->numSuivi, sizeof(bord->numSuivi), "%s%ld", prefix, cmdnum + horo);
    
        //enregistrement en bdd
        LOG_SERV(LOG_INFO, "INSERT recuperation des parametres...");
        params[0] = bord->numSuivi;
        params[1] = bord->numCommande;
        // clear previous SELECT result before reusing 'res'
        PQclear(res);

        LOG_SERV(LOG_INFO, "INSERT enregistrement en BDD...");
        res = PQexecParams(conn,
                    "INSERT INTO _delivraptor (id_suivi, id_commande) values ($1,$2)",
                    2,
                    NULL,
                    params,
                    NULL,
                    NULL,
                    0);
        
        if (PQresultStatus(res) == PGRES_COMMAND_OK) {
            LOG_SERV(LOG_INFO, "INSERT exécuté avec succès");
        } else {
            LOG_SERV(LOG_ERROR, "Erreur INSERT : %s", PQresultErrorMessage(res));
        }
    }

    // clear the PGresult from either SELECT or INSERT
    PQclear(res);
    
    //envoi du numéro de suivi
    snprintf(message, sizeof(message), "BORD %s com%s\n",bord->numSuivi, bord->numCommande);
    if (send(fd, message, strlen(message), 0) <= 0){
        LOG_SERV(LOG_WARN, "client déconnecté");
        return;
    }

    //ecriture de log
    LOG_CLIENT(LOG_INFO, cIp, cPort, "%s",message);

}
// Etape 2
// Etat livraison : En cours d'acheminement vers le transporteur

// Etape 3
// Etat livraison : Arrivé chez le transporteur

// Etape 4
// Etat livraison : En cours d'acheminement vers la plateforme regionale

// Etape 5
// Etat livraison : Arrivé à la plateforme regionale

// Etape 6
// Etat livraison : En cours d'acheminement vers le centre local

// Etape 7
// Etat livraison : Arrivé au centre local

// Etape 8
// Etat livraison : En cours de livraison

// Etape 9
time_t getHoro(){
    return time(NULL);
}

int connexion(int fd, char mdp[128], char user[128]){
    char line[256];
    char us[128], pswd[128];
    char message[512];
    int ret = 2;
    FILE *connexionFile;
    connexionFile = fopen("lst_client.data", "r");
    if (connexionFile == NULL) {
        return errno;
    }

    while (fgets(line, sizeof(line), connexionFile)){
        if (sscanf(line, "%127s %127s",us, pswd) == 2){
            if (strcmp(us, user) == 0 && strcmp(mdp, pswd) == 0){
                ret = 0;
                break;
            }
        }
    }
    fclose(connexionFile);
    //format commande CONN user pwd (use width limits)
        
        if ( ret == 0){
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Authentification réussie");
        snprintf(message, sizeof(message), "CONNEXION SUCCESS\n");
    }else{
        if (ret == 2){
            LOG_CLIENT(LOG_ERROR, cIp, cPort, "Authentification échoué : Identifiants incorrect");
            snprintf(message, sizeof(message), "CONNEXION DENIED %s %s\n", user, mdp);
        }else{
            LOG_CLIENT(LOG_ERROR, cIp, cPort, "Authentification échoué : %s", strerror(errno));
            snprintf(message, sizeof(message), "ERRER SERVER\n");   
        }
        send(fd, "CONNEXION DENIED\n", 17, 0);
        close(fd);
        return -1;
    }
    if (send(fd, message, strlen(message), 0) <= 0){
        LOG_SERV(LOG_WARN, "client déconnecté");
    }
    return ret;
}

int connecxionBd(){
    char *host = getenv("DB_HOST");
    char *dbname = getenv("DB_NAME");
    char *user = getenv("DB_USER");
    char *password = getenv("DB_PASSWORD");
    char connInfo[512];
    snprintf(connInfo, sizeof(connInfo), "host=%s dbname=%s user=%s password=%s", host, dbname, user, password);
    conn = PQconnectdb(connInfo);

    if (PQstatus(conn) != CONNECTION_OK){
        LOG_SERV(LOG_ERROR, "Erreur connexion BDD : %s", PQerrorMessage(conn));
        PQfinish(conn);
        return EXIT_FAILURE;
    }
    LOG_SERV(LOG_INFO, "Connecté a la BDD");
    LOG_SERV(LOG_INFO, "SET search_path...");
    PGresult *res = PQexec(conn, "SET search_path TO sae3_delivraptor");
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "Erreur SET search_path: %s\n", PQresultErrorMessage(res));
    }
    LOG_SERV(LOG_INFO, "SET search_path success");
    PQclear(res);

    res = PQexec(conn, "SELECT current_database()");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Base de données actuelle : %s", PQgetvalue(res, 0, 0));
    }
    PQclear(res);

    res = PQexec(conn, "SHOW search_path");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Schéma courant (search_path) : %s", PQgetvalue(res, 0, 0));
    }
    PQclear(res);
    return EXIT_SUCCESS;
}


// Etat livraison : 
// ETA ALI1245214522
void getEtat(int fd, char buffer[TAILLEB]){
    char temp[16];
    char id_suivi[256];
    char etat[16];
    int  nrows;

    /* Use width limits to avoid overflowing id_suivi */
    sscanf(buffer, "%15s %255s", temp, id_suivi);
    LOG_CLIENT(LOG_INFO, cIp, cPort, "numéro de suivie récupéré");

    PGresult *res;
    const char *params[1];
    params[0] = id_suivi;
    res = PQexecParams(conn,
                        "SELECT etat FROM _delivraptor WHERE id_suivi = $1",
                        1,
                        NULL,
                        params,
                        NULL,
                        NULL,
                        0);
    nrows = PQntuples(res);
    if (nrows>0){
        LOG_SERV(LOG_INFO, "Récuperation de l'etat");
        strncpy(etat, PQgetvalue(res,0,0), sizeof(etat)-1);
        etat[sizeof(etat) - 1] = '\0';
        LOG_SERV(LOG_DEBUG, "Etat commande char : %s", etat);
        LOG_SERV(LOG_DEBUG, "Etat commande int : %d", (int)strtol(etat, NULL, 10));
    }else{
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Id_suivi %s n'existe pas", id_suivi);
    snprintf(etat, sizeof(etat), "0");
    }
    LOG_SERV(LOG_DEBUG, "Envoi msg etat");
    msg_etat(fd, atoi(etat), id_suivi);
    LOG_SERV(LOG_DEBUG, "Envoi msg etat FIN");
    PQclear(res);
}

void avance(){
    PGresult *res;
    PGresult *update;
    const char *params[2];
    int  nrows;
    char *id_suivi;
    char *etat;
    char next_str[16];
    int next;

    res = PQexec(conn, "SELECT id_suivi, etat FROM _delivraptor WHERE etat <> 9");
    nrows = PQntuples(res);
    if (nrows>0){
        LOG_SERV(LOG_INFO, "SELECT retourne des éléments");
        for (int i=0; i<nrows; i++){
            id_suivi = PQgetvalue(res, i, 0);
            etat = PQgetvalue(res, i, 1);
            params[1] = id_suivi;
            LOG_SERV(LOG_DEBUG, "id suivi : %s, etat %s", id_suivi, etat);
            next = next_etat(atoi(etat));
            snprintf(next_str, sizeof(next_str), "%2d", next);
            params[0] = next_str;
            
            LOG_SERV(LOG_DEBUG, "prochain etat : %d", next);
            update = PQexecParams(conn,
                                  "UPDATE _delivraptor SET etat = $1 WHERE id_suivi = $2",
                                  2,
                                  NULL,
                                  params,
                                  NULL,
                                  NULL,
                                  0);
            if (PQresultStatus(update) != PGRES_COMMAND_OK) {
                LOG_SERV(LOG_DEBUG, "Erreur UPDATE: %s", PQerrorMessage(conn));
            }
            PQclear(update);
        }

    }
    PQclear(res);
}

void chomp(char *s) {
    size_t len = strlen(s);
    while (len > 0 && (s[len - 1] == '\n' || s[len - 1] == '\r')) {
        s[--len] = '\0';
    }
}
