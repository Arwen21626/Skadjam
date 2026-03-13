<?php
// Pour le nom du fichier c'est pas ce que vous croyez (Arwen)

session_start();

include __DIR__ . '/../01_premiere_connexion.php';
require_once __DIR__ . "/../php/verif_role_fo.php";

// Déclaration des variables
$idProd = $_GET['idProduit'];
$ajout = $_GET['ajout'];
$tabFABDD = [];
$idCompte = -1;
$vientDe = '';
$trouve = false;
$ancre = "#$idProd";

// Si le user vient de l'index
if ($_GET['vientDe'] == "index") {
    $vientDe = "/index.php".$ancre;
}
// Si le user vient de nouveaux produits
elseif ($_GET['vientDe'] == "nP") {
    $vientDe = "/html/fo/nouveaux_produits.php".$ancre;
}
// Si le user vient de recherche
elseif ($_GET['vientDe'] == "recherche") {
    $vientDe = "/html/fo/recherche.php".$ancre;
}
// Si le user vient de futurs achats
elseif ($_GET['vientDe'] == "fa") {
    $vientDe = "/html/fo/futurs_achats.php".$ancre;
}
// Si le user vient de promotions
elseif ($_GET['vientDe'] == "promo") {
    $vientDe = "/html/fo/promotion.php".$ancre;
}

// Si le user vient des plus vendus
elseif ($_GET['vientDe'] == "pV") {
    $vientDe = "/html/fo/les_plus_vendus.php".$ancre;
}

$chemin = '';

if ($ajout == "fa") {
    if ($_SESSION['role'] == "visiteur") {
        // Parcours pour voir si le produit est dans la session
        $trouve = array_search($idProd, $_SESSION['futurAchat']);
        // Si le produit n'est pas déjà présent on l'ajoute
        if ($trouve == false) {
            $_SESSION['futurAchat'][$idProd] = $idProd;
        }
        // Sinon on le retire
        else {
            unset($_SESSION['futurAchat'][$trouve]);
        }
        $chemin = $vientDe;
    }
    elseif ($_SESSION['role'] == "client") {
        $idCompte = $_SESSION['idCompte'];
        // Récupération de la liste des futurs achats (FA) du client
        foreach($dbh->query("SELECT id_produit FROM sae3_skadjam._futur_achat WHERE id_client = $idCompte", PDO::FETCH_ASSOC) as $row){
            $tabFABDD[$row['id_produit']] = $row['id_produit'];
        }
        // Parcours pour voir si le produit est dans la bdd
        $trouve = array_search($idProd, $tabFABDD);

        // Si le produit n'est pas déjà présent on l'ajoute
        if ($trouve == false) {
            $insertFA = $dbh->prepare("INSERT INTO sae3_skadjam._futur_achat(id_produit, id_client) VALUES (?,?)");
            $insertFA->execute([$idProd, $idCompte]);
        }
        // Sinon on le retire
        else{
            $dbh->query("DELETE FROM sae3_skadjam._futur_achat WHERE id_produit = $idProd AND id_client = $idCompte");
        }
        $chemin = $vientDe;
    }
    header("location:".$chemin);
    
}
elseif ($ajout == "Panier") {
    echo "Alède";
}
?>

