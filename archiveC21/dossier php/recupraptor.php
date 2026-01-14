<?php
class Recupraptor{
    private $user;
    private $password;
    private $ip;
    private $port;
    private $conn = NULL;
    private $numSuivi = NULL;
    private $etat = NULL;
    private $err = NULL;


    public function __construct(string $ip, int $port, string $user, string $password){
        echo "[INIT] Création de l'objet Recupator\n";
        $this->user = $user;
        $this->password = $password;
        $this->ip = $ip;
        $this->port = $port;
    }

    private function init_conn(): bool {
        echo "[CONN] Tentative de connexion au serveur {$this->ip}:{$this->port}\n";

        $this->conn = fsockopen($this->ip, $this->port, $errno, $errstr);

        if (!$this->conn){
            echo "[CONN][ERREUR] Impossible de se connecter : $errstr ($errno)\n";
            throw new Exception("Connexion failed : $errstr ($errno)");
        }

        echo "[CONN] Connexion établie, envoi des identifiants...\n";

        $send = "CONN {$this->user} {$this->password}";

        if (!fwrite($this->conn, $send)) {
            echo "[CONN][ERREUR] Impossible d'envoyer les identifiants\n";
            fclose($this->conn);
            $this->conn = NULL;
            throw new Exception("Erreur send identifiants");
        }

        echo "[CONN] Identifiants envoyés, attente réponse...\n";

        $reponse = trim(fgets($this->conn));
        echo "[CONN] Réponse reçue : '$reponse'\n";

        switch ($reponse) {
            case 'CONNEXION SUCCESS':
                echo "[CONN] Authentification réussie\n";
                return true;

            case 'CONNEXION DENIED':
                echo "[CONN][ERREUR] Identifiants incorrects\n";
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Connection denied : wrong username or password");

            default:
                echo "[CONN][ERREUR] Réponse inconnue du serveur\n";
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Reponse inconnu : $reponse");
        }
    }

    private function close_conn(): void {
        if ($this->conn){
            echo "[CONN] Fermeture de la connexion\n";
            fclose($this->conn);
            $this->conn = NULL;
        }
    }

    private function send_commande(string $commande) : string {
        echo "[SEND] Préparation de la commande : '$commande'\n";

        if (!$this->conn){
            echo "[SEND] Pas de connexion active, initialisation...\n";
            $this->init_conn();
        }

        echo "[SEND] Envoi de la commande...\n";

        if (!fwrite($this->conn, $commande)){
            echo "[SEND][ERREUR] Impossible d'envoyer la commande\n";
            throw new Exception("Erreur envoie de la commande : $commande");
        }

        echo "[SEND] Commande envoyée, attente réponse...\n";

        $reponse = fgets($this->conn);

        if ($reponse === false) {
            echo "[SEND][ERREUR] Aucune réponse reçue\n";
            throw new Exception("Aucune reponse recu pour la commande : $commande");
        }

        echo "[SEND] Réponse brute : '$reponse'\n";

        $this->close_conn();

        $clean = trim($reponse);
        echo "[SEND] Réponse nettoyée : '$clean'\n";

        return $clean;
    }

    public function create_bord(
        string $numCommande, 
        string $nomExp, 
        string $adrExp, 
        int $cpExp, 
        string $nomDest, 
        string $prenomDest, 
        string $adrDest, 
        int $cpDest)
        {
            echo "[ADD] Construction de la commande ADD...\n";

            $cmd = sprintf(
                "ADD %s %s |%s| %d %s %s |%s| %d", 
                $numCommande, 
                $nomExp, 
                $adrExp, 
                $cpExp, 
                $prenomDest, 
                $nomDest, 
                $adrDest, 
                $cpDest);

            echo "[ADD] Commande générée : '$cmd'\n";

            $reponse = $this->send_commande($cmd);

            echo "[ADD] Analyse de la réponse...\n";

            if (preg_match('/^BORD\s([A-Z]{3}[0-9]{10})\scom(\d+)$/', $reponse, $m)){
                $this->numSuivi = $m[1];
                echo "[ADD] Numéro de suivi détecté : {$this->numSuivi}\n";
            } else {
                echo "[ADD][ERREUR] Réponse inattendue : '$reponse'\n";
            }

            return $this->numSuivi;
        }

    public function get_etat(string $numSuivi){
        echo "[ETA] Demande d'état pour : $numSuivi\n";

        $reponse = $this->send_commande("ETA $numSuivi");

        echo "[ETA] Réponse reçue : '$reponse'\n";

        if (preg_match('/^ETA\s([A-Z]+)\s([A-Z]{3}[0-9]{10})$/', $reponse, $m)){
            $this->etat = $m[1];
            echo "[ETA] État extrait : {$this->etat}\n";
        } else {
            echo "[ETA][ERREUR] Format de réponse invalide\n";
        }

        return $this->etat;
    }
}
?>
