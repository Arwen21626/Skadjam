<?php 

/* 
    Ici on ne va modifier seulement la table _contient, donc la liste des produits pour mettre à jour leurs nouvelles quantités
    La modification de la table _panier se fait au chargement de la page panier.php 
    (peut être pas idéale mais évite du code supplémentaire ici + de la gestion d'information supplémentaire)
*/

require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

if ($_SESSION['role'] === 'client') {
    
    $rqt = $dbh->query("SELECT id_panier FROM sae3_skadjam._client WHERE id_compte = " . $_SESSION["idCompte"]);
    $idPanier = $rqt->fetch(PDO::FETCH_ASSOC)["id_panier"];

    foreach ($_POST["produits"] as $idProd => $infoProd) {

        if ($infoProd["quantite"] > 0) {

            $rqt = $dbh->prepare("UPDATE sae3_skadjam._contient SET
                              quantite_par_produit = ?
                              WHERE id_produit = ? AND id_panier = ?");
            $rqt->execute([$infoProd["quantite"], $idProd, $idPanier]);
        }
        else {

            $rqt = $dbh->prepare("DELETE FROM sae3_skadjam._contient 
                                  WHERE id_produit = ? AND id_panier = ?");
            $rqt->execute([$idProd, $idPanier]);
        }  
    }
}
else if ($_SESSION['role'] === 'visiteur'){

    foreach ($_POST["produits"] as $idProd => $infoProd) {

        foreach ($_SESSION['panier']['contient'] as $i => $produit) {
            
            if ($produit['id'] == $idProd) {

                if ($infoProd["quantite"] > 0) {

                    $_SESSION['panier']['contient'][$i]['quantite_par_produit'] = $infoProd['quantite'];
                }
                else {

                    unset($_SESSION['panier']['contient'][$i]);
                }

                break;
            }
        }        
    }
}

header("location:/html/fo/panier.php");
?>