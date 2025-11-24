<?php 
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

// Récupération id du produit, id du client et le type de retrait

$idClient = $_SESSION["idCompte"];
$idProd = $_POST["idProduit"];
$typeRetrait = $_POST["typeRetrait"];
$quantiteProd = $_POST["quantiteProd"];
$quantiteTot = $_POST["quantiteTot"];
$prixTTC = $_POST["prixTTC"];
$prixTot = $_POST["prixTot"];

if ($typeRetrait === "suppression" || $quantiteProd === "1") 
{
    // Suppression du produit du panier en supprimant la ligne correspondante dans _contient

    $rqt = $dbh->prepare("
        DELETE FROM sae3_skadjam._contient
        WHERE id_produit = :idProd
    ");

    $rqt->execute([
        ':idProd' => $idProd
    ]);

    // Mise à jour des informations générales du panier

    $quantiteTot -= $quantiteProd;
    $prixTot -= $prixTTC * $quantiteProd;

    $rqt = $dbh->prepare("
        UPDATE sae3_skadjam._panier SET
        nb_produit_total = :quantiteTot,
        montant_total_ttc = :montantTot
        WHERE id_client = :idClient
    ");

    $rqt->execute([
        ':quantiteTot' => $quantiteTot,
        ':montantTot' => $prixTot,
        ':idClient' => $idClient
    ]);

    header("location:/html/fo/panier.php");
}
else if ($typeRetrait === "decrement")
{
    $quantiteProd--;

    $rqt = $dbh->prepare("
        UPDATE sae3_skadjam._contient SET
        quantite_par_produit = :quantite
        WHERE id_produit = :idProd
    ");

    $rqt->execute([
        ':quantite' => $quantiteProd,
        ':idProd' => $idProd
    ]);

    // Mise à jour des informations générales du panier

    $quantiteTot--;
    $prixTot -= $prixTTC;

    $rqt = $dbh->prepare("
        UPDATE sae3_skadjam._panier SET
        nb_produit_total = :quantiteTot,
        montant_total_ttc = :montantTot
        WHERE id_client = :idClient
    ");

    $rqt->execute([
        ':quantiteTot' => $quantiteTot,
        ':montantTot' => $prixTot,
        ':idClient' => $idClient
    ]);

    header("location:/html/fo/panier.php#" . $idProd);
}



?>