<?php
use chillerlan\QRCode\{QRCode, QROptions};
use OTPHP\TOTP;

require_once __DIR__.'/../../../../vendor/autoload.php';
require_once __DIR__.'/bddAuthATOR.php';
require_once __DIR__.'/../../../01_premiere_connexion.php';

class AuthATOR{
    private $conn;
    private $bdd;
    private $entName;
    private $secret;
    private $otpObj;
    private $userId;
    private $userName;

    public function __construct($connBdd, $nomSociete, $idClient, $secret){
        if ($nomSociete != ""){
            $this->bdd = new BddAuthATOR();
            $this->conn = $connBdd;
            $this->entName = $nomSociete;
            $this->userId = $idClient;

            if ($secret==""){
                AuthATOR::initSecret();
            }else{
                $this->secret = $secret;
            }
        }else{
            throw new Exception("erreur nom societe");
        }
    }

    //recupere le secret si existe
    //cree l'obj otp sinon
    private function initSecret(){
        $ret = $this->bdd->getSecret($this->conn, $this->userId);
        $this->secret = $ret['code_secret'];
        $this->userName = $ret['adresse_mail'];

        if ($ret['code_secret'] == NULL){
            $clock = new PSR20();
    
            $otp = TOTP::generate($clock);
            $otp->setIssuer($this->entName);
            $otp->setLabel($this->userName);
            $this->otpObj = $otp;
            $this->secret = $otp->getSecret();
        }
        
    }

    //envoi le secret
    public function getSecret(){
        return $this->secret;
    }
    
    //envoi le qrcode
    public function getQrCode(){
        $otpauth = $this->otpObj->getProvisioningUri();
        $options = new QROptions(['returnResource' => false,]);

        $qrcode = (new QRCode($options))->render($otpauth);

        return $qrcode;
    }

    //enregistre le secret en base
    public function saveSecret(){
        $this->userName = $this->bdd->addSecret($this->conn, $this->userId, $this->secret);
    }

    //verifie le code
    public function verifyOtp($userCode){
        $otp = TOTP::createFromSecret($this->secret);
        return $otp->verify($userCode,leeway:2);
    }

}