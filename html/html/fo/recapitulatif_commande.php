<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require(__DIR__ . '/../../01_premiere_connexion.php');

// === Vérification utilisateur et panier ===
if (empty($_SESSION['idCompte'])) {
    die("Erreur : utilisateur non connecté");
}

if (!isset($_REQUEST['idPanier']) || empty($_REQUEST['idPanier'])) {
    die("Erreur : panier vide");
}

$idPanier = $_REQUEST['idPanier'];
$idCompte = $_SESSION['idCompte'];


// === Récupération du panier ===
try{ $sql = "SELECT 
            pr.libelle_produit,
            pr.id_produit,
            pr.id_vendeur,
            v.raison_sociale,
            c.quantite_par_produit,
            pr.prix_ht,
            pr.prix_ttc,
            pr.prix_remise,
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
    $stmt->execute([':id_panier' => $idPanier]);
    $tabInfosPanier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tabInfosPanier)) {
        die("Erreur : panier vide");
    }

    //Déclaration variables : 
    $quantite_totale = $tabInfosPanier[0]['nb_produit_total'];
    $total_ht = 0;
    $total_ttc = $tabInfosPanier[0]['montant_total_ttc'];
    $total_remise = 0;
    $v_quantite_totale = 0;
    $v_total_ht = 0;
    $v_total_ttc = 0;
    $v_total_remise = 0;
    $tabVendeur = [];

    foreach($tabInfosPanier as $ligne){
        if(in_array($ligne['id_vendeur'], $tabVendeur) == false){
            $tabVendeur[$ligne['id_vendeur']] = $ligne['raison_sociale'];
        }
    }

}
catch (Exception $e){
    echo "Erreur : " . $e->getMessage();
}

//acceptation des cgv --> redirection vers adresse.php
if (isset($_POST['valider'])) {
    if (!isset($_POST['case'])) {
        die("Erreur : vous devez accepter les CGV");
    }
    header("Location: ./adresse.php?idPanier=$idPanier");
    exit;
}
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de votre commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px]">
        <h2 class="mt-10">Récapitulatif de votre commande</h2>
        <!--<h3>Numéro de la commande :</h3>
        <p></p>-->
        <div class="ml-5 flex flex-row items-end mt-10">
            <h3 class="mr-3">Date :</h3>
            <p><?php echo date("d/m/Y");?></p>
        </div>

        <form action="recapitulatif_commande.php" method="post">
            <div class="flex justify-center mt-10">
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
                        <?php $impair = 1;
                        foreach($tabVendeur as $vendeur){
                            $impair ++;
                            if(fmod($impair, 2) == 0){
                                $classe = "py-4";
                            }
                            else{
                                $classe = "py-4 bg-bleu";
                            }?>
                            <tr class="<?php echo $classe; ?> border-t-2 border-solid border-black">
                                <th colspan="6" class="text-left py-3 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                            </tr>
                            <?php foreach($tabInfosPanier as $ligne){ 
                                if($ligne['raison_sociale'] == $vendeur){
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
                                        <td class="text-center py-3"><p><?php echo $ligne['quantite_par_produit'];?></p></td>
                                        <td class="text-center py-3"><p><?php echo $ligne['prix_ht'];?></p></td>
                                        <td class="text-center py-3"><p><?php echo $ligne['prix_ttc'];?></p></td>
                                        <td class="text-center py-3"><p><?php echo $ligne['prix_remise'];?></p></td>
                                        <?php 
                                            //calcul du total de la commande
                                            $total_ht = $total_ht + $ligne['sous_total_ht'];
                                            $total_remise += $ligne['prix_remise'] * $ligne['quantite_par_produit'];


                                            //calcul du sous-total par vendeur
                                            $v_quantite_totale = $v_quantite_totale + $ligne['quantite_par_produit'];
                                            $v_total_ht = $v_total_ht + $ligne['sous_total_ht'];
                                            $v_total_ttc += $ligne['prix_ttc'] * $ligne['quantite_par_produit'];
                                            $v_total_remise +=  $ligne['prix_remise'] * $ligne['quantite_par_produit'];
                                        ?>
                                    </tr>
                                <?php } 
                            }
                            $impair ++;
                            if(fmod($impair, 2) == 0){
                                $classe = "py-4";
                            }
                            else{
                                $classe = "py-4 bg-bleu";
                            }?>
                            <tr class="<?php echo $classe; ?>">
                                <th colspan="2" class="text-left w-110 pl-3"><p>Sous-total :</p></th>
                                <th class="text-center py-3"><p><?php echo $v_quantite_totale;?></p></th>
                                <th class="text-center py-3"><p><?php echo $v_total_ht;?></p></th>
                                <th class="text-center py-3"><p><?php echo $v_total_ttc;?></p></th>
                                <th class="text-center py-3"><p><?php echo $v_total_remise;?></p></th>
                            </tr>
                            <?php $v_quantite_totale = 0;
                            $v_total_ht = 0;
                            $v_total_ttc = 0;
                            $v_total_remise = 0;
                        } ?>
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
                            <th class="text-center py-3"><h4><?php echo $quantite_totale;?></h4></th>
                            <th class="text-center py-3"><h4><?php echo $total_ht;?></h4></th>
                            <th class="text-center py-3"><h4><?php echo $total_ttc;?></h4></th>
                            <th class="text-center py-3"><h4><?php echo $total_remise;?></h4></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        
        
            <div class="flex items-center mt-10">
                <a href="cgv_fo.php" class="ml-5 mr-5">J’ai lu et j’accepte les conditions générales de vente : </a>
                <input type="checkbox" class="cursor-pointer appearance-none w-10 h-10 border-4 border-vertClair rounded-md checked:bg-vertClair" name="case" id="case">
            </div>
            <div class="flex justify-center mt-10 mb-10">
                <a href="../fo/panier.php?idPanier=<?php echo $idPanier ;?>" class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5 mr-15">Annuler</a>
                <input type="hidden" name="idPanier" value="<?= $idPanier ?>">
                <input class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5" type="submit" name="valider" value="Valider">
            </div>
        </form>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>