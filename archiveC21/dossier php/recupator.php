<?php
class Recupator{
    private $user;
    private $password;
    private $ip;
    private $port;
    private $conn = NULL;
    private $numSuivi = NULL;
    private $etat = NULL;
    private $err = NULL;


    public function __construct(string $ip, int $port, string $user, string $password){
        $this->user = $user;
        $this->password = $password;
        $this->ip = $ip;
        $this->port = $port;
    }

    private function init_conn(): bool {
        $this->conn = fsockopen($this->ip, $this->port, $errno, $errstr);
        if (!$this->conn){
            throw new Exception("Connexion failed : $errstr ($errno)");
        }

        $send = "CONN {$this->user} {$this->password}";

        if (!fwrite($this->conn, $send)) {
            fclose($this->conn);
            $this->conn = NULL;
            throw new Exception("Erreur send identifiants");
        }

        $reponse = trim(fgets($this->conn));

        switch ($reponse) {
            case 'CONNEXION SUCCESS':
                return true;
                break;
            case 'CONNEXION DENIED':
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Connection denied : wrong username or password");
            default:
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Reponse inconnu : $reponse");
                break;
        }
    }

    private function close_conn(): void {
        if ($this->conn){
            fclose($this->conn);
            $this->conn = NULL;
        }
    }

    private function send_commande(string $commande) : string {
        if (!$this->conn){
            $this->init_conn();
        }

        $line = $commande;

        if (!fwrite($this->conn, $line)){
            throw new Exception("Erreur envoie de la commande : $commande");
        }

        $reponse = fgets(($this->conn));

        if ($reponse === false) {
            throw new Exception("Aucune reponse recu pour la commande : $commande");
        }
        $this->close_conn();
        return trim($reponse);
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

            $reponse = $this->send_commande($cmd);
            if (preg_match('/^BORD\s([A-Z]{3}[0-9]{10})\scom(\d+)$/', $reponse, $m)){
                $this->numSuivi = $m[1];

            }
            return $this->numSuivi;
        }

    public function get_etat(string $numSuivi){
        $reponse = $this->send_commande("ETA $numSuivi");
        if (preg_match('/^ETA\s([A-Z]+)\s([A-Z]{3}[0-9]{10})$/', $reponse, $m)){
            $this->etat = $m[1];
        }
        return $this->etat;
    }
}

?>