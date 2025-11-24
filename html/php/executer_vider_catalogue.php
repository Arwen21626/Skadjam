<?php 
    session_start();
    include __DIR__ .'/../01_premiere_connexion.php';
    $idCompte = $_SESSION['idCompte'];

    $tabProduitsASupprimer = [];

    try{
        foreach($dbh -> query("SELECT *
                            FROM sae3_skadjam._produit pr
                            INNER join sae3_skadjam._montre m
                                ON pr.id_produit=m.id_produit
                            INNER JOIN sae3_skadjam._photo ph  
                                ON ph.id_photo = m.id_photo 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._avis a
                                ON a.id_produit = pr.id_produit
                            WHERE v.id_compte = $idCompte"
                               , PDO::FETCH_ASSOC) as $row){
            $tabProduitsASupprimer[] = $row;
        }
        
        foreach($tabProduitsASupprimer as $id => $produit){
            $idPhoto = $produit['id_photo'];
            $idProduit = $produit['id_produit'];

            $dbh -> query("DELETE FROM sae3_skadjam._avis 
                            WHERE id_produit = $idProduit");
            $dbh -> query("DELETE FROM sae3_skadjam._montre 
                            WHERE id_produit = $idProduit");
            $dbh -> query("DELETE FROM sae3_skadjam._photo 
                            WHERE id_photo = $idPhoto");
        }

        foreach($tabProduitsASupprimer as $id => $produit){
            $idProduit = $produit['id_produit'];

            $dbh -> query("DELETE FROM sae3_skadjam._produit 
                            WHERE id_produit = $idProduit");
        }

    }
    catch (PDOException $e){
        print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
    }

    
?>