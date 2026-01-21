<?php
class Recupraptor{
    private $user = NULL;
    private $password = NULL;
    private $ip = NULL;
    private $port = NULL;
    private $conn = NULL;
    private $numSuivi = NULL;
    private $etat = NULL;
    private $raison = NULL;
    private $image = NULL;
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
        print_r($this->ip);
        print_r($this->port);
        $this->conn = fsockopen($this->ip, $this->port, $errno, $errstr);
        print_r("fin connexion");
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
            throw new Exception("ERREUR CMD FORMAT : cmd " . $reponse);
        }

        $reponse = trim(fgets($this->conn));
        if (!preg_match("/^SIZE\s([0-9]+)$/", $reponse, $taille)){
            throw new Exception("ERREUR SIZE FORMAT : size " . $reponse);
        }

        if ($taille[1]<255){
            while (!$fin){
                $reponse = trim(fgets($this->conn));
                if ($reponse != "END"){
                    $message .= $reponse;
                    $len += strlen($message);
                }else{
                    $fin = 1;
                }
    
            }
        }else{
            while (!$fin){
                $reponse = fgets($this->conn);
                if (trim($reponse) != "END"){
                    $message .= $reponse;
                    $len += strlen($message);
                }else{
                    $fin = 1;
                }
    
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
            case "LVRAB" : return "Livré absent";
            case "REFU" : return "Refusé";
            default : return "Inconnu";
        }
    }

    public function get_etat(string $numSuivi){
        $message = NULL;
        
        if ($reponse = $this->send_commande("ETA $numSuivi")){
            if(!preg_match("/^([A-Z]+)\s/", $reponse, $res)){
                throw new Exception("ERREUR GET ETAT regex ETAT");
            }else{
                $etat_reponse = $res[1];
                if (trim($etat_reponse) === "REFU"){
                    if (!preg_match("/msg:\s*(.+)$/", $reponse, $res)){
                        throw new Exception("ERREUR GET ETAT regex MESSAGE");
                    }
                    $message = $res[1];
                    $this->raison = $message;
                }

                if (trim($etat_reponse) === "LVRAB"){
                    $this->get_img($numSuivi);
                }
                $str_etat = Recupraptor::etat_to_str(trim($etat_reponse));
                $this->etat = $str_etat;
                return $this->etat;
            }
        }else{
            throw new Exception("ERREUR non connecté");
        }


        return false;
    }

    public function get_raison(){
        return $this->raison;
    }

    public function get_image_url(){
        return $this->image;
    }

    public function get_img(string $numSuivi){
        $racine = $_SERVER['DOCUMENT_ROOT'];
        $tmpDir = $racine . "/images/tmp_images";

        if (!is_dir($tmpDir)){
            mkdir($tmpDir, 0777, true);
        }

        if ($reponse = $this->send_commande("IMG $numSuivi")){
            $tmpFile = tempnam($tmpDir, "img_");
            $jpgFile = $tmpFile . ".jpg";
            rename($tmpFile, $jpgFile);

            file_put_contents($jpgFile, $reponse);
            $url = "/tmp_images/" . basename($jpgFile);
            
            $this->image = $url;
        }
    }
}
?>
