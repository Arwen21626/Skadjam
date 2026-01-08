<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];
    $idPanier = 1;

    $sql = "SELECT 
                pr.libelle_produit,
                v.raison_sociale,
                c.quantite_par_produit,
                pr.prix_ht,
                pr.prix_ttc,
                pr.prix_ttc * c.quantite_par_produit as sous_total_ttc,
                pr.prix_ht * c.quantite_par_produit as sous_total_ht,   
                p.montant_total_ttc,
                p.nb_produit_total   
            FROM sae3_skadjam._panier p
            INNER JOIN sae3_skadjam._contient c
                ON c.id_panier = p.id_panier
            INNER JOIN sae3_skadjam._produit pr
                ON pr.id_produit = c.id_produit       
            INNER JOIN sae3_skadjam._vendeur v
                ON v.id_compte = pr.id_vendeur
            WHERE p.id_panier = :id_panier
        ";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':id_panier' => $idPanier
    ]);

    $tabInfosPanier = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($tabInfosPanier);

    
    /*try {
        $date = date("j/n/Y");
        $dbh->beginTransaction();

        //Insertion de la commande
        $sqlCommande = "
            INSERT INTO sae3_skadjam._commande
            (etat, date_commande, montant_total_ttc, id_client)
            VALUES (:etat, :date_commande, :montant_total_ttc, :id_client)
            RETURNING id_commande
        ";

        $stmtCommande = $dbh->prepare($sqlCommande);
        $stmtCommande->execute([
            ':etat' => 'Attente de validation',
            ':date_commande' => $date,
            ':montant_total_ttc' => $tabInfosPanier['montant_total_ttc'],
            ':id_client' => $idCompte
        ]);

        $idCommande = $stmtCommande->fetchColumn();

        //Récupération du total du montant hors taxe
        $montant_total_ht = 0;
        foreach($tabInfosPanier as $infoPanier){
            $montant_total_ht = $montant_total_ht + ($infoPanier['sous_total_ht']);
        }

        //Insertion de la facture
        $sqlFacture = "
            INSERT INTO sae3_skadjam._facture
            (montant_ht, destinataire, date_commande, id_commande)
            VALUES (:montant_ht, :destinataire, :date_commande, :id_commande)
            RETURNING numero_facture
        ";

        $stmtFacture = $dbh->prepare($sqlFacture);
        $stmtFacture->execute([
            ':montant_ht' => $montant_total_ht,
            ':destinataire' => $idCompte,
            ':date_commande' => $date,
            ':id_commande' => $idCommande
        ]);

        $numeroFacture = $stmtFacture->fetchColumn();

        //Mise à jour de la commande
        $sqlUpdate = "
            UPDATE sae3_skadjam._commande
            SET id_facture = :id_facture
            WHERE id_commande = :id_commande
        ";

        $stmtUpdate = $dbh->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':id_facture' => $numeroFacture,
            ':id_commande' => $idCommande
        ]);

        // Validation
        $dbh->commit();
        echo "Commande et facture créées avec succès";

    } 
    
    catch (Exception $e) {
        $dbh->rollBack();
        echo "Erreur : " . $e->getMessage();
    }*/


    /*try {     
        $tabInfoCommandes = null;           
        //récupère toutes les infos des tables produits, panier et contient
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr
                            INNER JOIN sae3_skadjam._contient c
                                ON c.id_produit = pr.id_produit
                            INNER JOIN sae3_skadjam._panier pa
                                ON pa.id_panier = c.id_panier
                            WHERE c.id_client = $idCompte"
                            , PDO::FETCH_ASSOC) as $row){
            $tabInfoCommandes[] = $row;
        } 
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }*/
?>

