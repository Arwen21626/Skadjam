<?php
session_start();

$idProd = $_POST["idProduit"];
$idClient = $_SESSION["idCompte"];

echo $idProd . "<br>" . $idClient;

?>
