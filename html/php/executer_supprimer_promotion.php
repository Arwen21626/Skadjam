<?php
include __DIR__ .'/../01_premiere_connexion.php';

session_start();

$idProduit = $_GET['idProduit'];

try{
    $dbh->beginTransaction();
    $idPromotion = $promotion['id_promotion'];
    // Suppression de la promotion existante
    try {
        $dbh->prepare("DELETE FROM sae3_skadjam._promu
                        WHERE id_produit = :id_produit")->execute([':id_produit' => $idProduit]);

        $dbh->prepare("DELETE FROM sae3_skadjam._promotion
                        WHERE id_promotion = :id_promotion")->execute([':id_promotion' => $promotion['id_promotion']]);
        $dbh->commit();
    } catch (Exception $e) {
        $dbh->rollBack();
        throw $e;
    }
}
catch (PDOException $e){
    print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
}
?>