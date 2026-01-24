#include "serveur/serveur.h"

/* Point d’entrée du programme serveur Delivraptor.
   Logique générale :
     1. Initialiser le système de logs
     2. Lire et valider les options de lancement (port, fichier d’auth)
     3. Initialiser le socket serveur (bind + listen)
     4. Entrer dans la boucle principale d’acceptation des clients
     5. À la fin (théorique), fermer les logs
   Effets :
     - écrit dans les logs
     - configure les variables globales (sPort, arg_f, etc.)
     - démarre le serveur TCP
*/
int main(int argc, char *argv[]) {
    LOG_SERV(LOG_DEBUG, "main: démarrage du programme");

    /* Initialisation du système de logs (fichier daté dans /logs/) */
    log_init();
    LOG_SERV(LOG_INFO, "main: système de logs initialisé");

    /* Analyse des options (-p, -f, -h) et validation */
    manage_opt(argc, argv);
    LOG_SERV(LOG_INFO, "main: options analysées avec succès");

    /* Création du socket serveur + bind + listen */
    init_server();
    LOG_SERV(LOG_INFO, "main: serveur initialisé, entrée dans la boucle principale");

    /* Boucle infinie acceptant les connexions et forkant un processus par client */
    run_server_loop();

    /* Code théoriquement jamais atteint (boucle infinie) */
    LOG_SERV(LOG_INFO, "main: arrêt du serveur, fermeture des logs");
    log_close();

    LOG_SERV(LOG_DEBUG, "main: fin du programme");
    return 0;
}
