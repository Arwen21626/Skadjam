<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use chillerlan\QRCode\{QRCode, QROptions};
use OTPHP\TOTP;

require_once __DIR__.'/../../../../vendor/autoload.php';
require_once __DIR__.'/../../../../authentikATOR/src/PSR20.php';

header('Content-Type: application/json');

$clock = new PSR20();

$site = "Alizon";
$user = "seraphim";

$otp = TOTP::generate($clock);
$otp->setIssuer($site);
$otp->setLabel($user);
$secret = $otp->getSecret();
$otpauth = $otp->getProvisioningUri();
$options = new QROptions([
    'returnResource' => false, // IMPORTANT
]);

$qrcode = (new QRCode($options))->render($otpauth);

ob_clean(); // nettoie toute sortie avant d'envoyer le JSON

echo json_encode([ 
    "cle" => "comment ca va", 
    "secret" => $secret, 
    "qrcode" => $qrcode ]);