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
#include "model/client.h"
#include "etat/etat.h"
#include <getopt.h>

// Déclaration
time_t getHoro();
void print_help();
void manage_opt(int argc, char *argv[]);
int init_server();
void run_server_loop();
void handle_client(int fd, struct sockaddr_in conn_addr);
void process_commands(int fd, char *buffer);
int connecxionBd();
void getEtat(int fd, char buffer[TAILLEB]);
void avance();
void chomp(char *s);

time_t horo;
char cIp[INET_ADDRSTRLEN];
int cPort;
int sPort = -1;
PGconn *conn;
int sock;
int opt_h = 0;
int opt_f = 0;
char *arg_f;
int opt_p = 0;

int main(int argc, char *argv[]) {
    log_init();
    manage_opt(argc, argv);
    init_server();
    run_server_loop();
    log_close();
    return 0;
}

void manage_opt(int argc, char *argv[]){
    int opt;
    static struct option long_options[] = {
        {"port", required_argument, 0, 'p'},
        {"help", no_argument, 0, 'h'},
        {"file", required_argument, 0, 'f'},
        {0,0,0,0}
    };

    while ((opt = getopt_long(argc, argv, "p:hf:", long_options, NULL)) != -1){
        switch (opt) {
            case 'h':
                print_help();
                exit(0);
            case 'f':
                opt_f = 1;
                arg_f = optarg;
                break;
            case 'p':
                char *endptr;
                opt_p = 1;
                errno = 0;
                sPort = strtol(optarg, &endptr, 10);

                if (errno != 0 || *endptr != '\0' || sPort <= 0 || sPort > 65535){
                    fprintf(stdout, "Port invalide : %s\n", optarg);
                    exit(EXIT_FAILURE);
                }
                break;
            case '?':
                print_help();
                exit(0);
        }  
    }
    if (opt_p == 0){
        fprintf(stdout, "Erreur : le port est obligatoire\n");
        exit(EXIT_FAILURE);
    }

    if (opt_f == 0){
        fprintf(stdout, "Erreur : le fichier est obligatoir\n");
        exit(EXIT_FAILURE);
    }
}

int init_server() {
    if (sPort == -1){
        exit(EXIT_FAILURE);
    }
    LOG_SERV(LOG_INFO, "Démarrage du service Delivraptor");

    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
        LOG_SERV(LOG_ERROR, "Erreur socket(): %s", strerror(errno));
        exit(EXIT_FAILURE);
    }

    int opt = 1;
    setsockopt(sock, SOL_SOCKET, SO_REUSEADDR, &opt, sizeof(opt));

    struct sockaddr_in addr = {
        .sin_family = AF_INET,
        .sin_port = htons(sPort),
        .sin_addr.s_addr = inet_addr("127.0.0.1")
    };

    if (bind(sock, (struct sockaddr *)&addr, sizeof(addr)) < 0) {
        LOG_SERV(LOG_ERROR, "Erreur bind(): %s", strerror(errno));
        exit(EXIT_FAILURE);
    }

    if (listen(sock, 10) < 0) {
        LOG_SERV(LOG_ERROR, "Erreur listen(): %s", strerror(errno));
        exit(EXIT_FAILURE);
    }

    LOG_SERV(LOG_INFO, "Serveur prêt sur 127.0.0.1:8080");
    return sock;
}

void run_server_loop() {
    struct sockaddr_in conn_addr;
    socklen_t size = sizeof(conn_addr);

    while (1) {
        int cnx = accept(sock, (struct sockaddr *)&conn_addr, &size);
        if (cnx < 0) {
            LOG_SERV(LOG_ERROR, "Erreur accept(): %s", strerror(errno));
            continue;
        }

        LOG_SERV(LOG_INFO, "Connexion acceptée (fd=%d)", cnx);

        pid_t pid = fork();
        if (pid < 0) {
            LOG_SERV(LOG_ERROR, "Erreur fork(): %s", strerror(errno));
            close(cnx);
            continue;
        }

        if (pid == 0) {
            // Processus enfant
            close(sock);
            handle_client(cnx, conn_addr);
            close(cnx);
            _exit(0);
        }

        // Processus parent
        close(cnx);
    }
}

void handle_client(int fd, struct sockaddr_in conn_addr) {
    inet_ntop(AF_INET, &conn_addr.sin_addr, cIp, sizeof(cIp));
    cPort = ntohs(conn_addr.sin_port);

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client connecté");

    connecxionBd();

    char buffer[TAILLEB];
    int size;

    while ((size = read(fd, buffer, TAILLEB - 1)) > 0) {
        buffer[size] = '\0';
        process_commands(fd, buffer);
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client déconnecté");
}

void process_commands(int fd, char *buffer) {
    char *line = strtok(buffer, "\n");

    while (line) {
        chomp(line);

        char cmdstr[16];
        if (sscanf(line, "%15s", cmdstr) != 1) {
            LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande vide ou invalide");
            line = strtok(NULL, "\n");
            continue;
        }

        cmd_t cmd = get_commande(cmdstr);
        horo = getHoro();

        LOG_CLIENT(LOG_INFO, cIp, cPort, "Commande reçue : %s", cmdstr);

        switch (cmd) {
            case CMD_CONN:
                handle_conn(fd, line);
                break;

            case CMD_ADD: {
                bordereaux bord;
                add_bord(fd, line, &bord, horo);
            } break;

            case CMD_ETA:
                getEtat(fd, line);
                break;

            case CMD_NEXT:
                avance();
                send(fd, "next success\n", 14, 0);
                break;

            default:
                LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande inconnue : %s", cmdstr);
                send(fd, "CMD ERR NOT_EXIST\n", 19, 0);
                break;
        }

        line = strtok(NULL, "\n");
    }
}

#include <stdio.h>
#include <stdlib.h>

void print_help() {
    printf(
        "Usage: delivraptor [OPTIONS]\n"
        "\n"
        "Options:\n"
        "  -h, --help                   Affiche cette aide et quitte\n"
        "  -p, --port <port>            Définit le port d'écoute du serveur\n"
        "  -f, --file <filename.log>    Active le mode debug (logs détaillés)\n"
        "\n"
        "Description:\n"
        "  Delivraptor est un serveur TCP permettant la gestion des\n"
        "  commandes, bordereaux et états de livraison.\n"
        "\n"
        "Exemples:\n"
        "  delivraptor -p 8080\n"
        "  delivraptor --port 9090\n"
        "\n"
    );
}



time_t getHoro(){
    return time(NULL);
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
