<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use OTPHP\TOTP;
require_once __DIR__.'/../../../../vendor/autoload.php';

$secret = $_POST['secret'];
$input = $_POST['code'];

$otp = TOTP::createFromSecret($secret); // create TOTP object from the secret.
if ($otp->verify($input,leeway:2)){
    $ret = 'l\'authentification fonctionne';
}else{
    $ret = 'l\'authentification n\'a pas fonctionné';
} // Returns true if the input is verified, otherwise false.
ob_clean();
echo json_encode(['verify' => $ret,'data' => 'reg']);