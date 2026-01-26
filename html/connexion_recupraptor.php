<?php
include __DIR__ . "/recupraptor.php";
include __DIR__ . "/../recupraptor_connexions_params.php";
try{
    $rpr = new Recupraptor($rip, $rport, $ruser, $rpass);
} catch (Exception $e){
    echo "Erreur : " . $e->getMessage();
    die();
}

?>