<?php
include __DIR__ .'/../01_premiere_connexion.php';

session_start();

$idProduit = $_GET['idProduit'];

try{
    // attribut est_supprime passé à true dans _produit
    $requete = $dbh->prepare("UPDATE sae3_skadjam._produit pr 
                                    SET est_supprime = true 
                                    WHERE pr.id_produit = $idProduit;");
    $requete->execute();

    //Suppression dans _contient
    $dbh->query("DELETE FROM sae3_skadjam._contient WHERE id_produit = $idProduit"); 
    //Suppression dans _avis
    $dbh->query("DELETE FROM sae3_skadjam._avis WHERE id_produit = $idProduit");                  
        
}
catch (PDOException $e){
    print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
}
?>