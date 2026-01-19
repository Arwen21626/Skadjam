#include "serveur/serveur.h"

int main(int argc, char *argv[]) {
    log_init();
    manage_opt(argc, argv);
    init_server();
    run_server_loop();
    log_close();
    return 0;
}