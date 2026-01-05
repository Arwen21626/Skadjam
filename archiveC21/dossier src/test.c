#include <sys/types.h>
#include <sys/socket.h>
#include <netinet/in.h>
#include <arpa/inet.h>
#include <stdlib.h>
#include <stdio.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>

int main() {
    pid_t pid;
    int sock;
    int ret;
    int size, nb_lus;
    int cnx, fd;

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
}
// Etape 1

// Etape 2

// Etape 3

// Etape 4

// Etape 5

// Etape 6

// Etape 7

// Etape 8

// Etape 9