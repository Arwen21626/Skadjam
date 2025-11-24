<?php
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

// Récupère l'id du compte
$idClient = $_SESSION["idCompte"];

// Récupère l'id du panier du client

$rqt = $dbh->query("SELECT id_panier FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
$idPanier = $rqt->fetch()["id_panier"];

// Remise à 0 des informations générales du panier

$rqt = $dbh->prepare("
    UPDATE sae3_skadjam._panier SET
    nb_produit_total = 0,
    montant_total_ttc = 0.00
    WHERE id_panier = :idPanier
");

$rqt->execute([
    ':idPanier' => $idPanier
]);

// Suppression des produits du panier dans la table _contient

$rqt = $dbh->prepare("
    DELETE FROM sae3_skadjam._contient
    WHERE id_panier = :idPanier
");

$rqt->execute([
    ':idPanier' => $idPanier
]);

header("location:/html/fo/panier.php");
?>