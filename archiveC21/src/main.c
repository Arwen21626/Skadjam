#include "serveur/serveur.h"

int main(int argc, char *argv[]) {
    LOG_SERV(LOG_DEBUG, "main: démarrage du programme");

    log_init();
    LOG_SERV(LOG_INFO, "main: système de logs initialisé");

    manage_opt(argc, argv);
    LOG_SERV(LOG_INFO, "main: options analysées avec succès");

    init_server();
    LOG_SERV(LOG_INFO, "main: serveur initialisé, entrée dans la boucle principale");

    run_server_loop();

    LOG_SERV(LOG_INFO, "main: arrêt du serveur, fermeture des logs");
    log_close();

    LOG_SERV(LOG_DEBUG, "main: fin du programme");
    return 0;
}
