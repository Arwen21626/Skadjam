<?php

session_start();

include __DIR__ . '/../01_premiere_connexion.php';

// Déclaration des variables
$idProd = $_GET['idProduit'];
$ajout = $_GET['ajout'];
$tabFABDD = [];
$idCompte = -1;
$vientDe = '';

// Si le user vient de l'index
if ($_GET['vientDe'] == "index") {
    $vientDe = "/index.php";
}
// Si le user vient de nouveaux produits
elseif ($_GET['vientDe'] == "nP") {
    $vientDe = "/fo/nouveaux_produits.php";
}
elseif ($_GET['vientDe'] == "recherche") {
    $vientDe = "/fo/recherche.php";
}




if ($ajout == "FA") {
    if ($_SESSION['role'] == "visiteur") {
        // Mis en clé pour éviter les doublons et retrouver plus vite
        $_SESSION['futurAchat'][$idProd] = $idProd;
    }
    else{
        $idCompte = $_SESSION['idCompte'];
        // Récupération de la liste des futurs achats (FA) du client
        foreach($dbh->query("SELECT id_produit FROM sae3_skadjam._futur_achat WHERE id_client = $idCompte", PDO::FETCH_ASSOC) as $row){
            $tabFABDD[] = $row;
        }
        // Si le produit n'est pas déjà présent on l'ajoute
        if (!in_array($idProd, $tabFABDD)) {
            $insertFA = $dbh->prepare("INSERT INTO sae3_skadjam._futur_achat(id_produit, id_client) VALUES (?,?)");
            $insertFA->execute([$idProd, $idCompte]);
        }
        // Sinon on le retire
        // A faire plus tard
    }
    // header("location:".$vientDe);
    
}
elseif ($ajout == "Panier") {
    echo "Alède";
}
?>


