<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];
    $idCommande = $_GET['idCommande'];

    //=== Récupération de la commande ===
    try{               
        $sql = "SELECT 
                c.id_commande, 
                c.date_commande, 
                p.libelle_produit, 
                p.id_produit, 
                v.raison_sociale,
                d.quantite, 
                p.prix_ht, 
                p.prix_ttc, 
                p.prix_remise,
                c.montant_total_ttc,
                d.sous_total,
                f.montant_ht
                FROM sae3_skadjam._commande c
                INNER JOIN sae3_skadjam._details d
                    ON d.id_commande = c.id_commande
                INNER JOIN sae3_skadjam._produit p
                    ON p.id_produit = d.id_produit
                INNER JOIN sae3_skadjam._vendeur v
                    ON v.id_compte = p.id_vendeur
                INNER JOIN sae3_skadjam._facture f
                    ON f.numero_facture = c.id_facture
                WHERE c.id_commande = :id_commande AND p.id_vendeur = :id_vendeur";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([':id_commande' => $idCommande,':id_vendeur'  => $idCompte]);
                      
        $tabInfosCommande = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($tabInfosCommande)) {
            die("Erreur : commande inexistante");
        }

        //Déclaration variables
        $date = $tabInfosCommande[0]['date_commande'];
        /*$quantite_totale = 0;
        $total_ht = $tabInfosCommande[0]['montant_ht'];
        $total_ttc = $tabInfosCommande[0]['montant_total_ttc'];
        $total_remise = 0;*/
        $v_quantite_totale = 0;
        $v_total_ht = 0;
        $v_total_ttc = 0;
        $v_total_remise = 0;
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de la commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_back.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[600px]">
        <h2 class="mt-10">Récapitulatif de la commande</h2>

        <div class="ml-5 flex flex-row items-end mt-10">
            <h3 class="mr-3">Numéro de la commande : </h3> 
            <p><?php echo $idCommande;?></p>
        </div>

        <div class="ml-5 flex flex-row items-end">
            <h3 class="mr-3">Date : </h3>
            <p><?php echo $date;?></p>
        </div>

        <div class="flex justify-center mt-10">
            <?php //tableau des commandes ?>
            <table class="table-auto w-280">
                <thead>
                    <tr>
                        <th class="text-left w-110 pl-3"><h4>Article</h4></th>
                        <th class="pr-3"><h4>Référence</h4></th>
                        <th class="pr-3"><h4>Quantité</h4></th>
                        <th class="pr-3"><h4>Prix unitaire HT</h4></th>
                        <th class="pr-3"><h4>Prix unitaire TTC</h4></th>
                        <th class="pr-3"><h4>Prix remisé</h4></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $impair = 0;
                        foreach($tabInfosCommande as $ligne){ 
                            $impair ++;
                            if(fmod($impair, 2) == 0){
                                $classe = "py-4";
                            }
                            else{
                                $classe = "py-4 bg-bleu";
                            }?>
                            <tr class="<?php echo $classe; ?>">
                                <td class="text-left py-3 pl-3"><p><?php echo $ligne['libelle_produit'];?></p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['id_produit'];?></p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['quantite'];?></p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['prix_ht'];?></p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['prix_ttc'];?></p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['prix_remise'];?></p></td>
                                <?php 
                                    //calcul du total de la commande
                                    /*$quantite_totale += $ligne['quantite'];
                                    $total_remise += $ligne['prix_remise'] * $ligne['quantite'];*/

                                    //calcul du sous-total par vendeur
                                    $v_quantite_totale = $v_quantite_totale + $ligne['quantite'];
                                    $v_total_ht = $v_total_ht + $ligne['sous_total'];
                                    $v_total_ttc += $ligne['prix_ttc'] * $ligne['quantite'];
                                    $v_total_remise +=  $ligne['prix_remise'] * $ligne['quantite'];
                                ?>
                            </tr>
                        <?php } ?>
                </tbody>
                <tfoot>
                    <?php $impair ++;
                        if(fmod($impair, 2) == 0){
                            $classe = "py-4";
                        }
                        else{
                            $classe = "py-4 bg-bleu";
                        };?>
                    <tr class="<?php echo $classe; ?>">
                        <th colspan="2" class="text-left w-110 pl-3"><h4>Total :</h4></th>
                        <th class="text-center py-3"><h4><?php echo $v_quantite_totale;?></h4></th>
                        <th class="text-center py-3"><h4><?php echo $v_total_ht;?></h4></th>
                        <th class="text-center py-3"><h4><?php echo $v_total_ttc;?></h4></th>
                        <th class="text-center py-3"><h4><?php echo $v_total_remise;?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <a href="liste_commandes.php" class="flex justify-center mt-15 mb-15"><button class="border-vertFonce border-2 rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>
</html>