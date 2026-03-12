<?php
require_once __DIR__.'/AuthATOR.php';
require_once __DIR__.'/../../../../authentikATOR/src/PSR20.php';

header('Content-Type: application/json');

$action = $_POST['action'];
$nomSociete = $_POST['societe'];
$idClient = $_POST['idClient'];
$secret = $_POST['secret'];//MODIF var secret
$edition = ($_POST['edition']==0)?true:false;


$auth = new AuthATOR($dbh, $nomSociete, $idClient, $secret, $edition);//MODIF param secret
switch ($action) {
    case 'getSecret':
        $secret_ = $auth->getSecret();
        $qrcode = $auth->getQrCode();
        //$qrcode = "test";
        $intiBefor = $auth->getInitBefor();
        ob_clean();
        echo json_encode([
            "secret" => $secret_,
            "qrcode" => $qrcode,
            "init" => ($intiBefor)?0:1,
        ]);
        break;
    
    case 'verifOtp':
        $userCode = $_POST['userCode'];
        $isOk = $auth->verifyOtp($userCode);
        if ($isOk){
            $reponse = 0;
        }else{
            $reponse = 1;
        }
        ob_clean();
        echo json_encode(["verify" => $reponse]);
        break;
    
    case 'saveSecret':
        $isSave = $auth->saveSecret();
        ob_clean();
        echo json_encode(["save" => 0]);
        break;

    case 'getTentative' :
        $res = $auth->getTentative();
        echo json_encode(["tentative" => $res]);
        break;

    case 'getTempsRestant':
        $res = $auth->getTempsRestant();
        echo json_encode(['restant'=> $res]);
        break;

    case 'addTempsRestant':
        $auth->addTempsRestant();
        echo json_encode(['addTemps' => 0]);
        break;

    case 'addTentative':
        $auth->addTentative();
        echo json_encode(['addTentative'=>0]);
        break;
    
    case 'resetTentative':
        $auth->resetTentative();
        echo json_encode(['resetTentative'=>0]);
        break;

    case 'delSecret':
        $auth->delSecret();
        echo json_encode(['delSecret'=>0]);
    
    default:
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Action inconnue']);
        break;
}