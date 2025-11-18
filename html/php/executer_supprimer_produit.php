<?php
include __DIR__ .'/../01_premiere_connexion.php';
$idProduit = 1;

try{
    //Garder l'id de la photo
    $recupIdPhoto = $dbh->query("SELECT id_photo FROM sae3_skadjam._montre WHERE id_produit = $idProduit;");

    foreach ($recupIdPhoto as $t) {
        $idPhoto = $t['id_photo'];
    }

    //Suppresion de la table _montre
    $dbh->query("DELETE FROM sae3_skadjam._montre WHERE id_produit = $idProduit AND id_photo = $idPhoto");

    //Suppression dans _photo
    $dbh->query("DELETE FROM sae3_skadjam._photo WHERE id_photo = $idPhoto");

    //Suppression dans _produit
    $dbh->query("DELETE FROM sae3_skadjam._produit WHERE id_produit = $idProduit");
}
catch (PDOException $e){
    print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
}
?>