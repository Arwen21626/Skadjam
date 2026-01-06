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

char numDept["22", "29", "35", "56"];

char raisonRefus[
    "Le colis est trop abimé",
    "Le colis a été ouvert",
    "Le colis n'a pas été commandé",
    "Le colis est arrivé trop tard",
    "Le colis bouge"
];

char livraison[
    "Livré en mains propres",
    "Livré en abscence",
    "Refusé"
];

char etatLivraison[
    "Chez Alizon",
    "En cours d'acheminement vers le transporteur",
    "Arrivé chez le transporteur",
    "En cours d'acheminement vers la plateforme regionale",
    "Arrivé à la plateforme regionale",
    "En cours d'acheminement vers le centre local",
    "Arrivé au centre local",
    "En cours de livraison",

];

// Déclaration
void addCommande(int cnx, char buffer[TAILLEB], bordereaux *bord, int horo);

int main() {
    pid_t pid;
    int sock;
    int ret;
    int size, nb_lus;
    int cnx, fd;
    char buffer[TAILLEB];
    int horo;

    sock = socket(AF_INET, SOCK_STREAM, 0);
    printf("SOCK = %d\n",sock);

    int opt = 1;
    if (setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt))) {
        perror("setsockopt");
        exit(EXIT_FAILURE);
    }

    struct sockaddr_in addr;
    struct sockaddr_in conn_addr;

    addr.sin_addr.s_addr = inet_addr("127.0.0.1"); //Adresse IP
    addr.sin_family = AF_INET;
    addr.sin_port = htons(8080); //Choix du port d'ecoute
    ret = bind(sock, (struct sockaddr *)&addr, sizeof(addr));
    printf("BIND = %d\n",ret);
    ret = listen(sock, 10); //Taille de la liste d'attente
    printf("LISTEN = %d\n",ret);

    size = sizeof(conn_addr);
    cnx = accept(sock, (struct sockaddr *)&conn_addr, (socklen_t *)&size);
    printf("ACCEPT = %d\n",ret);
    while (1==1){
        size = read(cnx, buffer, TAILLEB);
        if (strncmp(buffer, "ADD", 3) == 0){
            horo = time(NULL);
            bordereaux bord;
            addCommande(cnx, buffer, &bord, horo);
        }
    }
}

// Etape 1
// ADD numCommande entrepriseExp adresseExp cpExp  nomDest prenomDest adresseDest cpDest adresse syntaxe ex : 6_rue_camelia
void addCommande(int cnx, char buffer[TAILLEB], bordereaux *bord, int horo){
    char chaine[512];
    char commande[3];
    sscanf(buffer, "%s %s %s %s %d %s %s %s %d",commande, bord->numCommande, bord->exp.entreprise, bord->exp.adresse, &bord->exp.codePostal, bord->dest.prenom, bord->dest.nom, bord->dest.adresse, &bord->dest.codePostal);
    for(int i = 0; i < 3 && bord->exp.entreprise[i] != '\0'; i++) {
        bord->numSuivi[i] = toupper((unsigned char)bord->exp.entreprise[i]);
    }
    bord->numSuivi[3] = '\0';
    fprintf(stdout, " >> [%d] %s\nDest\nnom : %s | prenom : %s\nadresse : %s | cp : %d\nExp\nadresse : %s | cp : %d\nnum commande : %s\nnum suivie : %s", horo, bord->exp.entreprise, bord->dest.nom, bord->dest.prenom, bord->dest.adresse, bord->dest.codePostal, bord->exp.adresse, bord->exp.codePostal, bord->numCommande, bord->numSuivi);
    snprintf(chaine, sizeof(chaine), "BOR %s%d ts%d",bord->numSuivi, atoi(bord->numCommande)+horo, horo);

    send(cnx, chaine, strlen(chaine), 0);
}
// Etape 2

// Etape 3

// Etape 4

// Etape 5

// Etape 6

// Etape 7

// Etape 8

// Etape 9