#include "serveur.h"

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
        fprintf(stdout, "Erreur : le port est obligatoir\n");
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

    LOG_SERV(LOG_INFO, "Serveur prêt sur 127.0.0.1:%d", sPort);
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
            LOG_SERV(LOG_DEBUG, "close connexion");
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

    connexionBd();

    char buffer[TAILLEB];
    int size;

    while ((size = read(fd, buffer, TAILLEB - 1)) > 0) {
        buffer[size] = '\0';
        LOG_SERV(LOG_DEBUG, "process commande");
        process_commands(fd, buffer);
        LOG_SERV(LOG_DEBUG, "sorti process commande");
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client déconnecté");
}

void process_commands(int fd, char *buffer) {
    char *line = strtok(buffer, "\n");

    while (line) {
        LOG_SERV(LOG_DEBUG, "debut line %s", line);
        chomp(line);
        LOG_SERV(LOG_DEBUG, "ap chomp line %s", line);

        char cmdstr[16];
        if (sscanf(line, "%15s", cmdstr) != 1) {
            LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande vide ou invalide");
            line = strtok(NULL, "\n");
            continue;
        }

        cmd_t cmd = get_commande(cmdstr);
        horo = time(NULL);

        LOG_CLIENT(LOG_INFO, cIp, cPort, "Commande reçue : %s", cmdstr);

        switch (cmd) {
            case CMD_CONN:
                handle_conn(fd, line);
                break;

            case CMD_ADD: 
                add_bord(conn, fd, line);
                LOG_SERV(LOG_DEBUG, "sortie add bord");
                break;

            case CMD_ETA:
                get_etat(conn, fd, line);
                break;

            case CMD_NEXT:
                avance(conn);
                send(fd, "next success\n", 14, 0);
                break;
            
            case CMD_IMG:
                get_img(conn, fd, line);
                break;

            default:
                LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande inconnue : %s", cmdstr);
                send(fd, "CMD ERR NOT_EXIST\n", 19, 0);
                break;
        }
        LOG_SERV(LOG_DEBUG, "fin line %s", line);
        line = strtok(NULL, "\n");
    }
    LOG_SERV(LOG_DEBUG, "Sortie line");
}

void handle_conn(int fd, const char *line) {
    char cmd[16], user[128], pwd[128];
    char response[256];

    // Extraction des paramètres
    if (sscanf(line, "%15s %127s %127s", cmd, user, pwd) != 3) {
        LOG_CLIENT(LOG_WARN, cIp, cPort, "CONN: format invalide");
        send(fd, "CONNEXION DENIED\n", 18, 0);
        return;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Tentative d'authentification pour '%s'", user);

    int status = auth_user(user, pwd);

    if (status == 0) {
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Authentification réussie pour '%s'", user);
        snprintf(response, sizeof(response), "CONNEXION SUCCESS\n");
        send(fd, response, strlen(response), 0);
        return;
    }

    if (status == 1) {
        LOG_CLIENT(LOG_WARN, cIp, cPort, "Authentification échouée : mauvais identifiants");
        snprintf(response, sizeof(response), "CONNEXION DENIED\n");
        send(fd, response, strlen(response), 0);
        return;
    }

    // status == -1 → erreur serveur
    LOG_SERV(LOG_ERROR, "Erreur interne lors de l'authentification");
    send(fd, "ERR SERVER\n", 11, 0);
}

int auth_user(const char *user, const char *pwd) {
    char line[256];
    char us[128], pswd[128];

    FILE *f = fopen(arg_f, "r");
    if (!f) {
        LOG_SERV(LOG_ERROR, "Impossible d'ouvrir lst_client.data : %s", strerror(errno));
        return -1; // erreur serveur
    }

    while (fgets(line, sizeof(line), f)) {
        if (sscanf(line, "%127s %127s", us, pswd) == 2) {
            if (strcmp(us, user) == 0 && strcmp(pswd, pwd) == 0) {
                fclose(f);
                return 0; // OK
            }
        }
    }

    fclose(f);
    return 1; // identifiants incorrects
}