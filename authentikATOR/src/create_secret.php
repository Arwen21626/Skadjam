<?php
/*
 * TODO:
 *      - créer le secret
 *      -
 */
use chillerlan\QRCode\{QRCode, QROptions};
use OTPHP\TOTP;

require_once __DIR__.'/../../vendor/autoload.php';
require_once __DIR__.'/PSR20.php';

$clock = new PSR20();

$otp = TOTP::generate($clock);
$secret = $otp->getSecret();
