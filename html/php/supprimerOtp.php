<?php
session_start();
require_once __DIR__.'/structure/authentikATOR/AuthATOR.php';
require_once __DIR__.'/../01_premiere_connexion.php';
// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["idCompte"])) {
    $_SESSION['redirect'] = '/php/supprimerOtp.php';
    header("Location: " . __DIR__ . "/../../html/fo/connexion.php");
    exit();
}

$role = $_SESSION['role'];
$dossier = ($role==='client')?'fo':'bo';
// verifié si c'est l'utilisateur qui fait l'action
if (empty($_SESSION['session_confirme'])){
    $_SESSION['redirect'] = '/php/supprimerOtp.php';
    header("Location: /html/identificationView.php");
    exit;
}

$auth = new AuthATOR($dbh, 'Alizon', $_SESSION['idCompte'], edition:true);
$res = $auth->delSecret();
unset($_SESSION["session_confirme"]);
$location = "/html/".$dossier."/profil_".$role.".php";
echo $location;
header("Location: ".$location);