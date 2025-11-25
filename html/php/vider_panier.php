<?php
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

// Récupère l'id du compte
$idClient = $_SESSION["idCompte"];

// Récupère pour quelle raison on vide le panier | 
// --- normal -> l'utilisateur a cliqué sur le bouton "Vider le panier" de son panier -> redirection sur la page panier
// --- achat -> l'utilisateur a réalisé le processus d'achat des produits contenus dans son panier et le processus a abouti -> on vide le panier et on redirige vers la suite

$typeVider = $_POST["typeVider"];

// Récupère l'id du panier du client

$rqt = $dbh->query("SELECT id_panier FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
$idPanier = $rqt->fetch()["id_panier"];

// Suppression des produits du panier dans la table _contient

$rqt = $dbh->prepare("
    DELETE FROM sae3_skadjam._contient
    WHERE id_panier = :idPanier
");

$rqt->execute([
    ':idPanier' => $idPanier
]);


// Redirection différente selon pourquoi on vide le panier -> façon normal ou lors de l'achat
if ($typeVider === "normal") 
{
    header("location:/html/fo/panier.php");
}
else if ($typeVider === "achat")
{
    header("location:/index.php");
}

?>