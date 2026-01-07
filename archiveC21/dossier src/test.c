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
int setLog(char message[512], int origin);
void getHoroLocal(char *buffer, size_t size);
time_t getHoro();

time_t horo;
char cIp[INET_ADDRSTRLEN];
int cPort;

int main() {
    int sock;
    int ret;
    int size;
    int cnx;
    char commande[20];
    char buffer[TAILLEB];

    sock = socket(AF_INET, SOCK_STREAM, 0);
    printf("SOCK = %d\n",sock);

    int opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt))) {
        perror("setsockopt");
        exit(EXIT_FAILURE);
    }

    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;

    addr.sin_addr.s_addr = inet_addr("127.0.0.1"); //Adresse ip
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080); //Choix du cPort d'ecoute
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    printf("BIND = %d\n",ret);
    ret = listen(sock, 10); //Taille de la liste d'attente
    printf("LISTEN = %d\n",ret);

    size = sizeof(conn_addr);
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    inet_ntop(AF_INET, &conn_addr.sin_addr, cIp, sizeof(cIp));
    cPort = ntohs(conn_addr.sin_port);
    printf("ACCEPT = %d\n",ret);
    while (1==1){
        size = read(cnx, buffer, TAILLEB);
        sscanf(buffer, "%s", commande);
        horo = getHoro();
        
        setLog(commande, 1);

        if (strncmp(commande, "ADD", 3) == 0){
            bordereaux bord;
            addCommande(cnx, commande, buffer, &bord, horo);
        }
    }
}

// Etape 1
// ADD numCommande entrepriseExp adresseExp cpExp  nomDest prenomDest adresseDest cpDest adresse syntaxe ex : 6_rue_camelia
void addCommande(int cnx, char commande[20], char buffer[TAILLEB], bordereaux *bord, time_t horo){
    char chaine[1024];

    //recuperation des information de la requete
    sscanf(buffer, "%s %s %s %s %d %s %s %s %d", commande, bord->numCommande, bord->exp.entreprise, bord->exp.adresse, &bord->exp.codePostal, bord->dest.prenom, bord->dest.nom, bord->dest.adresse, &bord->dest.codePostal);
    
    //creation numéro de suivi
    for(int i = 0; i < 3 && bord->exp.entreprise[i] != '\0'; i++) {
        bord->numSuivi[i] = toupper((unsigned char)bord->exp.entreprise[i]);
    }
    bord->numSuivi[3] = '\0';
    snprintf(chaine, sizeof(chaine), "%s%ld",bord->numSuivi, atoi(bord->numCommande)+horo);
    strcpy(bord->numSuivi, chaine);

    
    
    //envoi du numéro de suivi
    snprintf(chaine, sizeof(chaine), "BORD %s com%s ts%ld",bord->numSuivi, bord->numCommande, horo);
    send(cnx, chaine, strlen(chaine), 0);

    //ecriture de log
    setLog(chaine, 0);
}
// Etape 2

// Etape 3

// Etape 4

// Etape 5

// Etape 6

// Etape 7

// Etape 8

// Etape 9
time_t getHoro(){
    return time(NULL);
}

//timestamp convertion jj/mm/aaaa hh:mm:ss
void getHoroLocal(char *buffer, size_t size){
    time_t currentHoro = getHoro();
    struct tm *t = localtime(&currentHoro);
    strftime(buffer, size, "%d/%m/%Y %H:%M:%S", t);
}

void getFileName(char *buffer, size_t size){
    time_t currentHoro = getHoro();
    struct tm *t = localtime(&currentHoro);
    strftime(buffer, size, "%Y%m%d", t);
}

int setLog(char message[512], int origin){
    char horoLocal[50];
    char fileDate[9];
    char fileName[128];
    FILE *logfile;
    getFileName(fileDate, sizeof(fileDate));
    snprintf(fileName, sizeof(fileName), "logs/%s_log.txt", fileDate);

    logfile = fopen(fileName, "a");
    getHoroLocal(horoLocal, sizeof(horoLocal));
    if (origin == 1){
        fprintf(logfile, "[ %s ] CLIENT %s:%d %s\n", horoLocal, cIp, cPort, message);
        
    } else {
        fprintf(logfile, "[ %s ] SERVICE %s\n", horoLocal, message);
    }
    fflush(logfile);
    fclose(logfile);
    return 0;
}