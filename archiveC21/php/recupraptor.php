<?php
class Recupraptor{
    private $user = NULL;
    private $password = NULL;
    private $ip = NULL;
    private $port = NULL;
    private $conn = NULL;
    private $numSuivi = NULL;
    private $etat = NULL;
    private $err = NULL;


    public function __construct($ip, $port, $user, $password){
        if (!$user || !$password || !$ip || !$port){
            throw new Exception("Erreur auth : user = {$user} password = {$password} ip = {$ip} port = {$port}");
        }

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

            case 'CONNEXION DENIED':
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Connection denied : wrong username or password");

            default:
                fclose($this->conn);
                $this->conn = NULL;
                throw new Exception("Reponse inconnu : $reponse");
        }
    }

    private function close_conn(): void {
        if ($this->conn){
            fclose($this->conn);
            $this->conn = NULL;
        }
    }

    private function send_commande(string $commande) : string {
        $fin = 0;
        $len = 0;
        $message = "";


        if (!$this->conn){
            $this->init_conn();
        }


        if (!fwrite($this->conn, $commande)){
            throw new Exception("Erreur envoie de la commande : $commande");
        }

        $reponse = trim(fgets($this->conn));
        if (!preg_match("/^CMD\s([A-Z]+)$/", $reponse, $cmd)){
            throw new Exception("ERREUR CMD FORMAT");
        }

        $reponse = trim(fgets($this->conn));
        if (!preg_match("/^SIZE\s[0-9]+$/", $reponse, $taille)){
            throw new Exception("ERREUR CMD FORMAT");
        }


        while (!$fin){
            $reponse = trim(fgets($this->conn));
            if ($reponse != "END"){
                $message .= $reponse;
            }else{
                $fin = 1;
            }

        }


        $this->close_conn();
        return $message;
    }

    public function create_bord(
        string $numCommande, 
        string $nomExp
        )
        {

            $cmd = sprintf(
                "ADD %s %s",
                $nomExp,
                $numCommande
                );

            if ($reponse = $this->send_commande($cmd)){
                $this->numSuivi = $reponse;
                return $this->numSuivi;

            }else{
                throw new Exception("ERREUR non connecté");
            }
            return false;
            
        }


    static private function etat_to_str($etat){
        switch ($etat){
            case "TRTC" : return "En attente";
            case "ACHTR" :
            case "ARRTR" :
            case "ACHPR" :
            case "ARRPR" :
            case "ACHCL" :
            case "ARRCL" : return "Expédié";
            case "LVRSN" : return "En cours de livraison";
            case "LVR" : return "Livré";
            default : return "Inconnu";
        }
    }

    public function get_etat(string $numSuivi){

        if ($reponse = $this->send_commande("ETA $numSuivi")){
            $str_etat = Recupraptor::etat_to_str($reponse);
            $this->etat = $str_etat;
            return $this->etat;
        }else{
            throw new Exception("ERREUR non connecté");
        }


        return false;
    }
}
?>
