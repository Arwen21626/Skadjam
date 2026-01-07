<?php 
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

// Récupération des infos par POST

$idProd = $_POST["idProduit"];
$typeRetrait = $_POST["typeRetrait"];
$prixTTC = $_POST["prixTTC"];


if ($_SESSION['role'] === 'client') 
{
    // Récupération de l'id du client
    $idClient = $_SESSION["idCompte"];

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

        header("location:/html/fo/panier.php#" . $idProd);
    }
}
else if ($_SESSION['role'] === 'visiteur')
{
    if ($typeRetrait === "suppression" || $quantiteProd === "1") 
    {
        // Suppression du produit du panier en supprimant la ligne correspondante dans _contient

        foreach ($_SESSION['panier']['contient'] as $i => $prod) 
        {
            if ($prod['id'] == $idProd) 
            {
                $_SESSION['panier']['montant_total_ttc'] -= $prixTTC * $prod['quantite_par_produit'];
                $_SESSION['panier']['nb_produit_total'] -= $prod['quantite_par_produit'];
                unset($_SESSION['panier']['contient'][$i]);
                break;
            }    
        }

        header("location:/html/fo/panier.php");
    }
    else if ($typeRetrait === "decrement")
    {
        foreach ($_SESSION['panier']['contient'] as $i => $prod) 
        {
            if ($prod['id'] == $idProd) 
            {
                $_SESSION['panier']['montant_total_ttc'] -= $prixTTC;
                $_SESSION['panier']['nb_produit_total']--;
                $_SESSION['panier']['contient'][$i]['quantite_par_produit']--;
                break;
            }    
        }
        
        header("location:/html/fo/panier.php#" . $idProd);
    }
}








?>