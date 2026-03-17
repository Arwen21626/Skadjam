<?php
ob_start();
require_once __DIR__.'/AuthATOR.php';
require_once __DIR__.'/../../../../authentikATOR/src/PSR20.php';
header('Content-Type: application/json');

$action = $_POST['action'];
$nomSociete = $_POST['societe'];
$idClient = $_POST['idClient'];
$secret = $_POST['secret'];
$edition = ($_POST['edition'] == 1); // CORRIGÉ : 1 = mode édition (true), 0 = lecture seule (false)

$auth = new AuthATOR($dbh, $nomSociete, $idClient, $secret, $edition);
switch ($action) {
    case 'getSecret':
        $secret_ = $auth->getSecret();
        $qrcode = $auth->getQrCode();
        $intiBefor = $auth->getInitBefor();
        ob_clean();
        echo json_encode([
            "secret" => $secret_,
            "qrcode" => $qrcode,
            "init"   => $intiBefor ? 1 : 0, // CORRIGÉ : 1 = déjà configuré, 0 = pas encore
        ]);
        break;
    
    case 'verifOtp':
        $userCode = $_POST['userCode'];
        $isOk = $auth->verifyOtp($userCode);
        ob_clean();
        echo json_encode(["verify" => $isOk]); // CORRIGÉ : true/false direct, plus de 0/1 inversé
        break;
    
    case 'saveSecret':
        $auth->saveSecret();
        ob_clean();
        echo json_encode(["save" => true]); // CORRIGÉ : true = sauvegarde OK
        break;

    case 'getTentative':
        $res = $auth->getTentative();
        echo json_encode(["tentative" => $res]);
        break;

    case 'getTempsRestant':
        $res = $auth->getTempsRestant();
        echo json_encode(['restant' => $res]);
        break;

    case 'addTempsRestant':
        $auth->addTempsRestant();
        echo json_encode(['addTemps' => true]); // CORRIGÉ : true = succès
        break;

    case 'addTentative':
        $auth->addTentative();
        echo json_encode(['addTentative' => true]); // CORRIGÉ : true = succès
        break;
    
    case 'resetTentative':
        $auth->resetTentative();
        echo json_encode(['resetTentative' => true]); // CORRIGÉ : true = succès
        break;

    case 'delSecret':
        $auth->delSecret();
        echo json_encode(['delSecret' => true]); // CORRIGÉ : true = succès
        break;
    
    default:
        http_response_code(400);
        ob_clean();
        echo json_encode(['error' => 'Action inconnue']);
        break;
}