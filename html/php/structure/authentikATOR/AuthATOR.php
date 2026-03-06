<?
use chillerlan\QRCode\{QRCode, QROptions};
use OTPHP\TOTP;

require_once __DIR__.'/../../../../vendor/autoload.php';
require_once __DIR__.'/../../../../authentikATOR/src/PSR20.php';
require_once __DIR__.'/../../../01_premiere_connexion.php';

class AuthATOR{
    private $entName;
    private $secret;
    private $userId;
    private $userNamde;
    private $userCode;
    private $servCode;

    public function __construct($nom_societe){
        if ($nom_societe != ""){
            $this->entName = $nom_societe;
        }else{
            throw new Exception("erreur nom societe");
        }
    }

    public function create_secret(){
        $dbh->
        $clock = new PSR20();

        $otp = TOTP::generate($clock);
        $otp->setIssuer($this->entName);
        $otp->setLabel($this->userId);
        $this->secret = $otp->getSecret();
    }

}