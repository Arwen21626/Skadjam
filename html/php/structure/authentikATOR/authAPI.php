<?php
require_once __DIR__.'/AuthATOR.php';
require_once __DIR__.'/../../../../authentikATOR/src/PSR20.php';

header('Content-Type: application/json');

$action = $_POST['action'];
$nomSociete = $_POST['societe'];
$idClient = $_POST['idClient'];
$secret = $_POST['secret'];//MODIF var secret


$auth = new AuthATOR($dbh, $nomSociete, $idClient, $secret);//MODIF param secret
switch ($action) {
    case 'getSecret':
        $secret = $auth->getSecret();
        $qrcode = $auth->getQrCode();
        ob_clean();
        echo json_encode([
            "secret" => $secret,
            "qrcode" => $qrcode
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
        echo json_encode(["save" => true]);
        break;
    
    default:
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Action inconnue']);
        break;
}