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

#define TAILLEB 1024

typedef struct destinataire{
    char nom[255];
    char prenom[255];
    char adresse[255];
    int codePostal;
}destinataire;

typedef struct expediteur{
    char entreprise[255];
    char adresse[255];
    int codePostal;
}expediteur;

typedef struct bordereaux{
    char numCommande[255];
    char numSuivi[255];
    expediteur exp;
    destinataire dest;
}bordereaux;

// Déclaration
void addCommande(int cnx, char commande[20], char buffer[TAILLEB], bordereaux *bord, time_t horo);
time_t getHoro();
int connexion(char mdp[128], char user[128]);
int connecxionBd();

time_t horo;
char cIp[INET_ADDRSTRLEN];
int cPort;
PGconn *conn;

int main() {
    int sock;
    int ret;
    int size;
    int cnx;
    char user[128], mdp[128];
    char commande[20];
    char buffer[TAILLEB];
    char message[1024];
    log_init();

    
    LOG_SERV(LOG_INFO ,"Démarrage du service Delivraptor");
    sock = socket(AF_INET, SOCK_STREAM, 0);
    printf("SOCK = %d\n",sock);

    int opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt))) {
        LOG_SERV(LOG_ERROR, "Erreur socket : %s",strerror(errno));
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
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    if (cnx>=0){
        LOG_SERV(LOG_INFO, "Connexion accepté");
    }else{
        LOG_SERV(LOG_ERROR, "Erreur socket : %s", strerror(errno));
    }

    inet_ntop(AF_INET, &conn_addr.sin_addr, cIp, sizeof(cIp));
    cPort = ntohs(conn_addr.sin_port);
    size = read(cnx, buffer, TAILLEB);
    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client connecté au service avec succès");
    
    //format commande CONN user pwd
    sscanf(buffer, "%s %s %s", commande, user, mdp);
    int connect = connexion(mdp, user);
    if ( connect == 0){
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Authentification réussie");
        snprintf(message, sizeof(message), "CONNEXION SUCCESS");
        send(cnx, message, strlen(message), 0);
    }else{
        if (connect == 2){
            LOG_CLIENT(LOG_ERROR, cIp, cPort, "Authentification échoué : Identifiants incorrect");
            snprintf(message, sizeof(message), "CONNEXION DENIED %s %s", user, mdp);
            send(cnx, message, strlen(message), 0);
        }else{
            LOG_CLIENT(LOG_ERROR, cIp, cPort, "Authentification échoué : %s", strerror(errno));
            snprintf(message, sizeof(message), "ERRER SERVER");
            send(cnx, message, strlen(message), 0);
        }
        exit(EXIT_FAILURE);
    }

    LOG_SERV(LOG_INFO, "Connexion a la BDD...");
    connecxionBd();

    printf("ACCEPT = %d\n",ret);
    while (1==1){
        size = read(cnx, buffer, TAILLEB-1);
        buffer[size] = '\0';
        sscanf(buffer, "%s", commande);
        horo = getHoro();
        
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Requete %s", commande);

        if (strncmp(commande, "ADD", 3) == 0){
            bordereaux bord;
            addCommande(cnx, commande, buffer, &bord, horo);
        }else{
            LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande non reconnu : %s", commande);
        }

    }
    log_close();
}

// Etape 1
// Etat livraison : Chez Alizon
// ADD numCommande entrepriseExp adresseExp cpExp  nomDest prenomDest adresseDest cpDest adresse syntaxe ex : 6_rue_camelia
void addCommande(int cnx, char commande[20], char buffer[TAILLEB], bordereaux *bord, time_t horo){
    char chaine[1024];

    //recuperation des information de la requete
    sscanf(buffer, "%s %s %s |%s| %d %s %s |%s| %d", commande, bord->numCommande, bord->exp.entreprise, bord->exp.adresse, &bord->exp.codePostal, bord->dest.prenom, bord->dest.nom, bord->dest.adresse, &bord->dest.codePostal);
    LOG_CLIENT(LOG_INFO, cIp, cPort, "Création bordereau pour la commande %s %s", bord->numCommande, bord->exp.entreprise);
    //creation numéro de suivi
    for(int i = 0; i < 3 && bord->exp.entreprise[i] != '\0'; i++) {
        bord->numSuivi[i] = toupper((unsigned char)bord->exp.entreprise[i]);
    }
    bord->numSuivi[3] = '\0';
    snprintf(chaine, sizeof(chaine), "%s%ld",bord->numSuivi, atoi(bord->numCommande)+horo);
    strcpy(bord->numSuivi, chaine);

    //enregistrement en bdd
    LOG_SERV(LOG_INFO, "INSERT recuperation des parametres...");
    const char *params[1];
    params[0] = bord->numSuivi;

    LOG_SERV(LOG_INFO, "INSERT enregistrement en BDD...");
    PGresult *res = PQexecParams(conn,
                                 "INSERT INTO _delivraptor (id_suivi) values ($1)",
                                 1,
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

    PQclear(res);
    
    //envoi du numéro de suivi
    snprintf(chaine, sizeof(chaine), "BORD %s com%s ts%ld",bord->numSuivi, bord->numCommande, horo);
    send(cnx, chaine, strlen(chaine), 0);

    //ecriture de log
    LOG_CLIENT(LOG_INFO, cIp, cPort, "%s",chaine);

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

int connexion(char mdp[128], char user[128]){
    char line[256];
    char us[128], pswd[128];
    char message[128];
    int ret = 2;
    FILE *connexionFile;
    connexionFile = fopen("lst_client.data", "a+");
    if (connexionFile == NULL) {
        return errno;
    }

    while (fgets(line, sizeof(line), connexionFile)){
        if (sscanf(line, "%128s %128s",us, pswd)){
            if (strcmp(us, user) == 0 && strcmp(mdp, pswd) == 0){
                ret = 0;
                break;
            }
        }
    }
    fclose(connexionFile);
    return ret;
}

int connecxionBd(){
    conn = PQconnectdb("host=127.0.0.1 dbname=postgres user=postgres password=1969:USA");

    if (PQstatus(conn) != CONNECTION_OK){
        LOG_SERV(LOG_ERROR, "Erreur connexion BDD : %s", PQerrorMessage(conn));
        PQfinish(conn);
        exit(EXIT_FAILURE);
    }
    LOG_SERV(LOG_INFO, "Connecté a la BDD");
    LOG_SERV(LOG_ERROR, "SET search_path...");
    PGresult *res = PQexec(conn, "SET search_path TO sae3_delivraptor");
    if (PQresultStatus(res) != PGRES_COMMAND_OK) {
        LOG_SERV(LOG_ERROR, "Erreur SET search_path: %s\n", PQresultErrorMessage(res));
    }
    LOG_SERV(LOG_INFO, "SET search_path success");
    PQclear(res);

    res = PQexec(conn, "SELECT current_database()");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Base de données actuelle : %s\n", PQgetvalue(res, 0, 0));
    }
    PQclear(res);

    res = PQexec(conn, "SHOW search_path");
    if (PQresultStatus(res) == PGRES_TUPLES_OK) {
        LOG_SERV(LOG_INFO, "Schéma courant (search_path) : %s\n", PQgetvalue(res, 0, 0));
    }
    PQclear(res);

}
// Etat livraison : 
