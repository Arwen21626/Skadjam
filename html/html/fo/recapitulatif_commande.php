<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];
    $idPanier = $_SESSION['idPanier'];

    $sql = "SELECT *
    FROM sae3_skadjam._panier
    WHERE id_panier = :id_panier";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([
        ':id_panier' => $idPanier
    ]);

    $panier = $stmt->fetch(PDO::FETCH_ASSOC);

    
    try {
        $date = date("j/n/Y");
        $dbh->beginTransaction();

        // 1️⃣ Insertion de la commande
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
            ':montant_total_ttc' => $panier['montant_total_ttc'],
            ':id_client' => $idCompte
        ]);

        $idCommande = $stmtCommande->fetchColumn();

        // 2️⃣ Insertion de la facture
        $sqlFacture = "
            INSERT INTO sae3_skadjam._facture
            (montant_ht, destinataire, date_commande, id_commande)
            VALUES (:montant_ht, :destinataire, :date_commande, :id_commande)
            RETURNING numero_facture
        ";

        $stmtFacture = $dbh->prepare($sqlFacture);
        $stmtFacture->execute([
            ':montant_ht' => 100.42,
            ':destinataire' => 3,
            ':date_commande' => '2026-01-07',
            ':id_commande' => $idCommande
        ]);

        $numeroFacture = $stmtFacture->fetchColumn();

        // 3️⃣ Mise à jour de la commande
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
    }


    try {     
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
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de la commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px]">
        <h2>Récapitulatif de la commande</h2>

        <?php if($tabInfoCommandes == null){ ?>
            <p>Erreur : un problème d'affichage de votre récapitulatif de commande est survenu.</p>
        <?php }

        else{?>
            <h4>Numéro de la commande : </h4> 
            <p></p>
            <h4>Date : </h4>
            <p></p>
            <div class="flex justify-center">
                <?php //tableau des commandes ?>
                <table class="table-auto w-250">
                    <thead>
                        <tr>
                            <th scope="col" class="text-left w-125 pl-3"><h3>Article</h3></th>
                            <th scope="col"><h3>Vendeur</h3></th>
                            <th scope="col"><h3>Quantité</h3></th>
                            <th scope="col"><h3>Prix unitaire HT</h3></th>
                            <th scope="col"><h3>Prix unitaire TTC</h3></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            //pour changer la classe de css une ligne sur 2
                            $impair = 0;
                            $classe;
                            $classe1 = "py-4";
                            $classe2 = "py-4 bg-bleu";
                            foreach($tabInfoCommandes as $id => $commande){
                                $idCommande = $commande['id_commande']; 
                                $impair ++;
                                if(fmod($impair, 2) == 0){
                                    $classe = $classe1;
                                }
                                else{
                                    $classe = $classe2;
                                }?>
                                <tr class="<?php echo $classe; ?>">
                                    <th scope="row" class="text-left py-3 pl-3" ><p><?php echo $idCommande; ?></p></th>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['date_commande']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['etat']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['montant_total_ttc']); ?></p></td>
                                    <td><a href="<?php echo htmlentities("commande.php?idCommande=".$idCommande);?>">
                                        <img src="../../images/logo/bootstrap_icon/plus-square.svg" alt="voir plus d'informations" class="w-10 h-auto">
                                    </a></td>
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <a href="../../index.php" class="flex justify-end mr-60 mt-15"><button class="bg-beige shadow rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>

</body>
</html>