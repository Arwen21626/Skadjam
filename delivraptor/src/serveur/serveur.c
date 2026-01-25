#include "serveur.h"

/* Affiche l’aide en ligne du programme.
   Utilisé lorsque l’utilisateur passe -h ou --help.
   Aucun effet de bord autre que l’affichage. */
void print_help() {
    printf(
        "Usage: ./delivraptor [OPTIONS]\n"
        "\n"
        "Options:\n"
        "  -h, --help                   Affiche cette aide et quitte\n"
        "  -p, --port <port>            Définit le port d'écoute du serveur\n"
        "  -f, --file <filename>        Définit le fichier contenant les utilisateurs\n"
        "\n"
        "Description:\n"
        "  Delivraptor est un serveur permettant la gestion des\n"
        "  commandes, bordereaux et états de livraison.\n"
        "\n"
        "Exemples:\n"
        "  delivraptor -p 8080\n"
        "  delivraptor --port 9090\n"
        "  delivraptor -f list_id.txt\n"
        "\n"
    );
}

/* Analyse les options de la ligne de commande.
   Paramètres :
     - argc / argv : arguments du programme
   Variables globales modifiées :
     - opt_p : indique si un port a été fourni
     - opt_f : indique si un fichier de log a été fourni
     - sPort : port d’écoute du serveur
     - arg_f : chemin du fichier contenant les identifiants utilisateurs
   Logique :
     - utilise getopt_long pour gérer -p, -f, -h
     - valide le port
     - exige que -p et -f soient fournis
   Effets :
     - écrit dans les logs
     - peut appeler exit() en cas d’erreur */
void manage_opt(int argc, char *argv[]) {
    LOG_SERV(LOG_DEBUG, "manage_opt: début du parsing des options");

    int opt;
    static struct option long_options[] = {
        {"port", required_argument, 0, 'p'},
        {"help", no_argument, 0, 'h'},
        {"file", required_argument, 0, 'f'},
        {0,0,0,0}
    };

    while ((opt = getopt_long(argc, argv, "p:hf:", long_options, NULL)) != -1) {
        switch (opt) {
            case 'h':
                print_help();
                exit(0);

            case 'f':
                opt_f = 1;
                arg_f = optarg;
                LOG_SERV(LOG_INFO, "manage_opt: fichier log défini (%s)", arg_f);
                break;

            case 'p': {
                char *endptr;
                opt_p = 1;
                errno = 0;
                sPort = strtol(optarg, &endptr, 10);

                /* Validation du port */
                if (errno != 0 || *endptr != '\0' || sPort <= 0 || sPort > 65535) {
                    LOG_SERV(LOG_ERROR, "manage_opt: port invalide (%s)", optarg);
                    exit(EXIT_FAILURE);
                }

                LOG_SERV(LOG_INFO, "manage_opt: port défini (%d)", sPort);
                break;
            }

            case '?':
                print_help();
                exit(0);
        }
    }

    /* Vérification des options obligatoires */
    if (!opt_p) {
        LOG_SERV(LOG_ERROR, "manage_opt: port obligatoire manquant");
        exit(EXIT_FAILURE);
    }

    if (!opt_f) {
        LOG_SERV(LOG_ERROR, "manage_opt: fichier obligatoire manquant");
        exit(EXIT_FAILURE);
    }

    LOG_SERV(LOG_DEBUG, "manage_opt: parsing terminé");
}

/* Initialise le socket serveur.
   Variables globales utilisées :
     - sPort : port d’écoute
     - sock : socket serveur créé
   Logique :
     - crée un socket
     - active SO_REUSEADDR
     - bind sur 127.0.0.1:sPort
     - listen()
   Retour :
     - fd du socket serveur
   Effets :
     - écrit dans les logs
     - exit() en cas d’erreur critique */
int init_server() {
    LOG_SERV(LOG_DEBUG, "init_server: initialisation du serveur");

    if (sPort == -1) {
        LOG_SERV(LOG_ERROR, "init_server: port non initialisé");
        exit(EXIT_FAILURE);
    }

    LOG_SERV(LOG_INFO, "init_server: démarrage du service Delivraptor");

    sock = socket(AF_INET, SOCK_STREAM, 0);
    if (sock < 0) {
        LOG_SERV(LOG_ERROR, "init_server: erreur socket() (%s)", strerror(errno));
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
        LOG_SERV(LOG_ERROR, "init_server: erreur bind() (%s)", strerror(errno));
        exit(EXIT_FAILURE);
    }

    if (listen(sock, 10) < 0) {
        LOG_SERV(LOG_ERROR, "init_server: erreur listen() (%s)", strerror(errno));
        exit(EXIT_FAILURE);
    }

    LOG_SERV(LOG_INFO, "init_server: serveur prêt sur 127.0.0.1:%d", sPort);
    return sock;
}

/* Boucle principale du serveur.
   Logique :
     - attend des connexions via accept()
     - fork() un processus enfant pour chaque client
     - le parent continue d’écouter
   Variables globales :
     - sock : socket serveur
   Effets :
     - écrit dans les logs
     - crée des processus enfants */
void run_server_loop() {
    LOG_SERV(LOG_INFO, "run_server_loop: boucle serveur démarrée");

    struct sockaddr_in conn_addr;
    socklen_t size = sizeof(conn_addr);

    while (1) {
        int cnx = accept(sock, (struct sockaddr *)&conn_addr, &size);
        if (cnx < 0) {
            LOG_SERV(LOG_ERROR, "run_server_loop: erreur accept() (%s)", strerror(errno));
            continue;
        }

        LOG_SERV(LOG_INFO, "run_server_loop: connexion acceptée (fd=%d)", cnx);

        pid_t pid = fork();
        if (pid < 0) {
            LOG_SERV(LOG_ERROR, "run_server_loop: erreur fork() (%s)", strerror(errno));
            close(cnx);
            continue;
        }

        if (pid == 0) {
            /* Processus enfant */
            close(sock);
            handle_client(cnx, conn_addr);
            LOG_SERV(LOG_DEBUG, "run_server_loop: fin processus enfant");
            close(cnx);
            _exit(0);
        }

        /* Processus parent */
        close(cnx);
    }
}

/* Gère un client dans un processus enfant.
   Paramètres :
     - fd : socket client
     - conn_addr : adresse du client
   Variables globales modifiées :
     - cIp, cPort : IP et port du client
     - conn : connexion PostgreSQL (via connexionBd())
   Logique :
     - initialise la connexion BDD
     - lit les commandes du client
     - délègue à process_commands()
   Effets :
     - écrit dans les logs
     - boucle jusqu’à déconnexion */
void handle_client(int fd, struct sockaddr_in conn_addr) {
    inet_ntop(AF_INET, &conn_addr.sin_addr, cIp, sizeof(cIp));
    cPort = ntohs(conn_addr.sin_port);

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client connecté");

    connexionBd();

    char buffer[TAILLEB];
    int size;

    while ((size = read(fd, buffer, TAILLEB - 1)) > 0) {
        buffer[size] = '\0';
        LOG_SERV(LOG_DEBUG, "handle_client: commande reçue");
        process_commands(fd, buffer);
        LOG_SERV(LOG_DEBUG, "handle_client: fin traitement commande");
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Client déconnecté");
}

/* Traite un buffer contenant potentiellement plusieurs commandes.
   Paramètres :
     - fd : socket client
     - buffer : texte brut reçu
   Variables globales :
     - horo : timestamp utilisé pour générer id_suivi
   Logique :
     - découpe par lignes
     - identifie la commande (CMD_ADD, CMD_ETA, etc.)
     - appelle la fonction correspondante
   Effets :
     - écrit dans les logs
     - peut envoyer des réponses au client */
void process_commands(int fd, char *buffer) {
    LOG_SERV(LOG_DEBUG, "process_commands: début traitement");

    char *line = strtok(buffer, "\n");

    while (line) {
        LOG_SERV(LOG_DEBUG, "process_commands: ligne brute='%s'", line);

        chomp(line);
        LOG_SERV(LOG_DEBUG, "process_commands: ligne nettoyée='%s'", line);

        char cmdstr[16];
        if (sscanf(line, "%15s", cmdstr) != 1) {
            LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande vide ou invalide");
            line = strtok(NULL, "\n");
            continue;
        }

        cmd_t cmd = get_commande(cmdstr);
        horo = time(NULL);

        LOG_CLIENT(LOG_INFO, cIp, cPort, "Commande reçue: %s", cmdstr);

        switch (cmd) {
            case CMD_CONN:
                handle_conn(fd, line);
                break;

            case CMD_ADD:
                add_bord(conn, fd, line);
                LOG_SERV(LOG_DEBUG, "process_commands: fin ADD");
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
                LOG_CLIENT(LOG_WARN, cIp, cPort, "Commande inconnue: %s", cmdstr);
                send(fd, "CMD ERR NOT_EXIST\n", 19, 0);
                break;
        }

        LOG_SERV(LOG_DEBUG, "process_commands: fin ligne");
        line = strtok(NULL, "\n");
    }

    LOG_SERV(LOG_DEBUG, "process_commands: fin traitement");
}

/* Gère la commande CONN (authentification).
   Paramètres :
     - fd : socket client
     - line : ligne complète "CONN user pwd"
   Logique :
     - parse user + pwd
     - hash le mot de passe en MD5
     - compare avec le fichier arg_f
   Effets :
     - écrit dans les logs
     - envoie SUCCESS ou DENIED */
void handle_conn(int fd, const char *line) {
    LOG_SERV(LOG_DEBUG, "handle_conn: début");

    char cmd[16], user[128], pwd[128];
    char response[256];

    if (sscanf(line, "%15s %127s %127s", cmd, user, pwd) != 3) {
        LOG_CLIENT(LOG_WARN, cIp, cPort, "CONN: format invalide");
        send(fd, "ERR SERVER\n", 18, 0);
        return;
    }

    LOG_CLIENT(LOG_INFO, cIp, cPort, "Tentative d'authentification pour '%s'", user);

    int status = auth_user(user, pwd);

    if (status == 0) {
        LOG_CLIENT(LOG_INFO, cIp, cPort, "Authentification réussie pour '%s'", user);
        snprintf(response, sizeof(response), "CONNECTION SUCCESS\n");
        send(fd, response, strlen(response), 0);
        return;
    }

    if (status == 1) {
        LOG_CLIENT(LOG_WARN, cIp, cPort, "Authentification échouée: mauvais identifiants");
        snprintf(response, sizeof(response), "CONNECTION DENIED\n");
        send(fd, response, strlen(response), 0);
        return;
    }

    LOG_SERV(LOG_ERROR, "handle_conn: erreur interne auth_user()");
    send(fd, "ERR SERVER\n", 11, 0);
}

/* Vérifie les identifiants utilisateur.
   Paramètres :
     - user : nom d’utilisateur
     - pwd : mot de passe en clair
   Variables globales :
     - arg_f : fichier contenant "user md5(password)"
   Logique :
     - hash pwd en MD5
     - lit le fichier ligne par ligne
     - compare user + hash
   Retour :
     - 0 = OK
     - 1 = mauvais identifiants
     - -1 = erreur interne */
int auth_user(const char *user, const char *pwd) {
    LOG_SERV(LOG_DEBUG, "auth_user: début (user=%s)", user);

    char line[256];
    char us[128], pswd[128];

    FILE *f = fopen(arg_f, "r");
    if (!f) {
        LOG_SERV(LOG_ERROR,
                 "auth_user: impossible d'ouvrir fichier client (%s)",
                 strerror(errno));
        return -1;
    }

    char password[MD5_DIGEST_LENGTH * 2 + 1];
    md5_hash(pwd, password);

    while (fgets(line, sizeof(line), f)) {
        if (sscanf(line, "%127s %127s", us, pswd) == 2) {
            if (strcmp(us, user) == 0 && strcmp(pswd, password) == 0) {
                fclose(f);
                LOG_SERV(LOG_INFO,
                         "auth_user: authentification OK pour %s",
                         user);
                return 0;
            }
        }
    }

    fclose(f);
    LOG_SERV(LOG_INFO,
             "auth_user: identifiants incorrects pour %s",
             user);
    return 1;
}

/* Calcule le hash MD5 d’un mot de passe.
   Paramètres :
     - password : mot de passe en clair
     - output : buffer de sortie (32 hex chars + '\0')
   Logique :
     - MD5() remplit digest (16 octets)
     - conversion en hexadécimal
   Effets :
     - remplit output */
void md5_hash(const char *password, char *output) {
    unsigned char digest[MD5_DIGEST_LENGTH];
    MD5((unsigned char*)password, strlen(password), digest);

    for (int i = 0; i < MD5_DIGEST_LENGTH; i++) {
        sprintf(&output[i * 2], "%02x", digest[i]);
    }
}
