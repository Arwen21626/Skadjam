<?php
// Pour le nom du fichier c'est pas ce que vous croyez (Arwen)

session_start();

include __DIR__ . '/../01_premiere_connexion.php';
require_once __DIR__ . "/../php/verif_role_fo.php";

// Déclaration des variables
$idProd = $_GET['idProduit'];
if (empty($_GET['nbAddPanier'])) {
    $qte = -1;
}
else{
    $qte = $_GET['nbAddPanier'];
}

$ajout = $_GET['ajout'];
$tabFABDD = [];
$idCompte = -1;
$vientDe = '';
$trouve = false;
$ancre = "#$idProd";

// Si le user vient de l'index
if ($_GET['vientDe'] == "index") {
    $vientDe = "/index.php";
}
// Si le user vient de nouveaux produits
elseif ($_GET['vientDe'] == "nP") {
    $vientDe = "/html/fo/nouveaux_produits.php";
}
// Si le user vient de recherche
elseif ($_GET['vientDe'] == "recherche") {
    $vientDe = "/html/fo/recherche.php";
}
// Si le user vient de futurs achats
elseif ($_GET['vientDe'] == "fa") {
    $vientDe = "/html/fo/futurs_achats.php";
}
// Si le user vient de promotions
elseif ($_GET['vientDe'] == "promo") {
    $vientDe = "/html/fo/promotion.php";
}

// Si le user vient des plus vendus
elseif ($_GET['vientDe'] == "pV") {
    $vientDe = "/html/fo/les_plus_vendus.php";
}

$chemin = '';

if ($ajout == "fa") {
    if ($_SESSION['role'] == "visiteur") {
        // Parcours pour voir si le produit est dans la session
        $trouve = array_search($idProd, $_SESSION['futurAchat']);
        // Si le produit n'est pas déjà présent on l'ajoute
        if ($trouve == false) {
            $_SESSION['futurAchat'][$idProd] = $idProd;
            $chemin = $vientDe."?addFA=1".$ancre;
        }
        // Sinon on le retire
        else {
            unset($_SESSION['futurAchat'][$trouve]);
            $chemin = $vientDe."?removeFA=1".$ancre;
        }
        
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
            $chemin = $vientDe."?addFA=1".$ancre;
        }
        // Sinon on le retire
        else{
            $dbh->query("DELETE FROM sae3_skadjam._futur_achat WHERE id_produit = $idProd AND id_client = $idCompte");
            $chemin = $vientDe."?removeFA=1".$ancre;
        }
    }
    header("location:".$chemin); 
    
}
elseif ($ajout == "panier") {
    if ($_SESSION['role'] == "visiteur") {
        if (!isset($_SESSION['panier']['contient'][$idProd])) {
            // Ajouter le produit
            $_SESSION['panier']['contient'][$idProd] = [
                'id' => $idProd,
                'quantite_par_produit' => $qte
            ];
            $chemin = $vientDe."?addPanier=1".$ancre;
        } else {
            // Retirer le produit
            unset($_SESSION['panier']['contient'][$idProd]);
            $chemin = $vientDe."?removePanier=1".$ancre;
        }
    }
    elseif ($_SESSION['role'] == "client") {
        $idCompte = $_SESSION['idCompte'];
        // Récupération du panier du client
        foreach($dbh->query("SELECT c.id_panier, c.id_produit FROM sae3_skadjam._panier pa
                INNER JOIN sae3_skadjam._contient c
                    ON pa.id_panier = c.id_panier
                WHERE pa.id_client = $idCompte", PDO::FETCH_ASSOC) as $row){
            $tabPanierBDD[$row['id_produit']] = $row['id_produit'];
        }
        // Récup du num du panier
        $stmt = $dbh->query("SELECT id_panier FROM sae3_skadjam._client WHERE id_compte = $idCompte");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $idPanier = $row['id_panier'];
        // Parcours pour voir si le produit est dans la bdd
        if ($tabPanierBDD != null){
            foreach ($tabPanierBDD as $id) {
                if ($id == $idProd) {
                    $trouve = true;
                }
            }
        }
        
        // Si le produit n'est pas déjà présent on l'ajoute
        if ($trouve == false && $qte != -1) {
            $insertPanier = $dbh->prepare("INSERT INTO sae3_skadjam._contient(id_produit,id_panier,quantite_par_produit) VALUES (?,?,?) ");
            $insertPanier->execute([$idProd, $idPanier, $qte]);
            $chemin = $vientDe."?addPanier=1".$ancre;
        }
        // Sinon on le retire
        else{
            $dbh->query("DELETE FROM sae3_skadjam._contient WHERE id_produit = $idProd");
            $chemin = $vientDe."?removePanier=1".$ancre;
        }
    }
    header("location:".$chemin);
}

?>

