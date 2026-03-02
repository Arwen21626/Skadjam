<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../php/fonctions.php";
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
            r.pourcentage_remise,
            pr.prix_ttc * c.quantite_par_produit as sous_total_ttc,
            pr.prix_ht * c.quantite_par_produit as sous_total_ht,
            pa.montant_total_ttc,
            pa.nb_produit_total,
            p.url_photo, 
            p.alt, 
            p.titre
        FROM sae3_skadjam._panier pa
        INNER JOIN sae3_skadjam._contient c
            ON c.id_panier = pa.id_panier
        INNER JOIN sae3_skadjam._produit pr
            ON pr.id_produit = c.id_produit
        INNER JOIN sae3_skadjam._vendeur v
            ON v.id_compte = pr.id_vendeur
        LEFT JOIN sae3_skadjam._reduit rd
            ON rd.id_produit = pr.id_produit
        LEFT JOIN sae3_skadjam._remise r
            ON r.id_remise = rd.id_remise
        INNER JOIN sae3_skadjam._montre m
            ON m.id_produit = pr.id_produit
        INNER JOIN sae3_skadjam._photo p
            ON p.id_photo = m.id_photo
        WHERE pa.id_panier = :id_panier
    ";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([':id_panier' => $idPanier]);
    $tabInfosPanier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tabInfosPanier)) {
        die("Erreur : panier vide");
    }

    //Déclaration variables : 
    $tabVendeur = [];
    $quantite_totale = $tabInfosPanier[0]['nb_produit_total'];
    $total_ttc = $tabInfosPanier[0]['montant_total_ttc'];
    $total_ht = 0;
    $sous_total_final = 0;
    $total_ligne = 0;
    $total_final = 0;

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
        header("Location : ./recapitulatif_commande?idPanier=$idPanier?case=$case");
        exit;
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
                
        <div class="flex items-center justify-between mt-10 px-5">
            <!---date de la commande--->
            <h3>Date : <?php echo date("d/m/Y"); ?></h3>
            <!-- bouton annuler version téléphone-->
            <a href="../fo/panier.php?idPanier=<?php echo $idPanier; ?>"
                class="md:hidden flex items-center justify-center border-2 border-vertClair rounded-xl
                w-25 h-10 cursor-pointer">Annuler
            </a>
        </div>


        <form action="recapitulatif_commande.php" method="post">
            <div class="flex flex-col items-center justify-center md:mt-10 mt-6">
                <!-- TABLEAU VERSION TABLETTE -->
                <table class="table-auto w-280 md:inline-table hidden">
                    <thead>
                        <tr>
                            <!---noms des colonnes--->
                            <th class="pr-3 w-24"></th>
                            <th class="pr-3"><h4 class="text-left">Article</h4></th>
                            <th class="pr-3"><h4>Prix unitaire HT</h4></th>
                            <th class="pr-3"><h4>Prix unitaire TTC</h4></th>
                            <th class="pr-3"><h4>Pourcentage remise</h4></th>
                            <th class="pr-3"><h4>Quantité</h4></th>
                            <th class="pr-3"><h4>Total</h4></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ligneIndex = 0;
                        foreach($tabVendeur as $vendeur){?>
                            <!---affichage du vendeur--->
                            <tr class="py-4 <?= ligneCouleur($ligneIndex)?> border-t-2 border-solid border-black">
                                <th colspan="6" class="text-left py-3 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                            </tr>
                            <?php foreach($tabInfosPanier as $ligne){ 
                                if($ligne['raison_sociale'] == $vendeur){?>
                                    <!---affichage des informations de chaque produit de la commande--->
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                        <td class="py-3 w-24 text-center">
                                            <img class="w-16 h-16 object-contain inline-block" 
                                                src="<?php echo $ligne['url_photo'];?>" 
                                                alt="<?php echo $ligne['alt'];?>" 
                                                title="<?php echo $ligne['titre'];?>">
                                        </td>
                                        <td class="text-left py-3 pl-3"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                        <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ht']);?>€</p></td>
                                        <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ttc']);?>€</p></td>
                                        <td class="text-center py-3"><p><?php echo $ligne['pourcentage_remise']*100;?>%</p></td>
                                        <td class="text-center py-3"><p><?php echo $ligne['quantite_par_produit'];?></p></td>
                                        <!---affichage du total de ligne (quantite et remise comprise)--->
                                        <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                            $total_ligne = $ligne['prix_remise'] * $ligne['quantite_par_produit'] ;    
                                        } 
                                        else{
                                            $total_ligne = $ligne['prix_ttc'] * $ligne['quantite_par_produit'] ;
                                        } 
                                        $prix = explode(".", "$total_ligne");?>
                                        <td class="text-center py-3 pr-3">
                                            <p><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€
                                        </p></td>
                                        <?php 
                                            //calcul du total ht de la commande
                                            $total_ht = $total_ht + $ligne['sous_total_ht'];
                                            //calcul du sous total final par vendeur
                                            $sous_total_final += $total_ligne;
                                        ?>
                                    </tr>
                                <?php } 
                            }?>
                            <!---sous-total par vendeur--->
                            <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                <th colspan="6" class="text-left w-90 pl-3"><p>Sous-total :</p></th>
                                <?php $prix = explode(".", "$sous_total_final");?>
                                <th class="text-center py-3 pr-3"><p>
                                    <?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€
                                </p></th>
                            </tr>
                            <?php
                                //calcul du total final de la commande remise(s) comprise(s)
                                $total_final += $sous_total_final;
                                $sous_total_final = 0;
                        } ?>
                    </tbody>
                    <tfoot>
                        <!---affichage des totaux de la commande--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex)?> border-t-2 border-solid border-black">
                            <th class="text-left w-90 pl-3" colspan="2"><h4>Total :</h4></th>
                            <!---total ht--->
                            <?php $prix = explode(".", "$total_ht");?>
                            <th class="text-center py-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                            <!---total ttc--->
                            <?php $prix = explode(".", "$total_ttc");?>
                            <th class="text-center py-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                            <th></th>
                            <!---quantité totale--->
                            <th class="text-center py-3"><h4><?php echo $quantite_totale;?></h4></th>
                            <!---total final--->
                            <?php $prix = explode(".", "$total_final");?>
                            <th class="text-center py-3 pr-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        </tr>
                    </tfoot>
                </table>


                <!-- VERSION TELEPHONE -->
                <table class="table-auto w-95 md:hidden block mt-6">
                    <tbody>
                        <?php $ligneIndex = 0;
                        foreach($tabVendeur as $vendeur){ ?>
                            <!---affichage du vendeur--->
                            <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                                <th colspan="2" class="text-left py-2 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                            </tr>
                            <?php foreach($tabInfosPanier as $ligne){ 
                                if($ligne['raison_sociale'] == $vendeur){ ?> 
                                    <!---affichage des informations de chaque produit de la commande---> 
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                        <th class="text-left py-2 pl-3"><h4 class="w-30">Article</h4></th>
                                        <td class="text-left">
                                            <div class="flex">
                                                <img class="w-16 h-16 object-contain inline-block" 
                                                    src="<?php echo $ligne['url_photo'];?>" 
                                                    alt="<?php echo $ligne['alt'];?>" 
                                                    title="<?php echo $ligne['titre'];?>">
                                                <p class="ml-3"><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p>
                                            </div>   
                                        </td>
                                    </tr>
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                        <th class="text-left py-2 pl-3"><h4>Prix HT</h4></th>
                                        <td class="text-left"><p><?php echo str_replace('.',',',$ligne['prix_ht']);?>€</p></td>
                                    </tr>
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                        <th class="text-left py-2 pl-3"><h4>Prix TTC</h4></th>
                                         <td class="text-left"><p><?php echo str_replace('.',',',$ligne['prix_ttc']);?>€</p></td>
                                    </tr>
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                        <th class="text-left py-2 pl-3"><h4>% remise</h4></th>
                                        <td class="text-left"><p><?php echo $ligne['pourcentage_remise']*100;?></p></td>
                                    </tr>
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                        <th class="text-left py-2 pl-3"><h4>Quantité</h4></th>
                                        <td class="text-left"><p><?php echo $ligne['quantite_par_produit'];?></p></td>
                                    </tr>
                                    <!---affichage du total du produit (quantite et remise comprise)--->
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-b-2 border-solid border-black">
                                        <th class="text-left py-2 pl-3"><h4>Total produit</h4></th>
                                        <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                            $total_ligne = $ligne['remise'] * $ligne['quantite_par_produit'] ;    
                                        } 
                                        else{
                                            $total_ligne = $ligne['prix_ttc'] * $ligne['quantite_par_produit'] ;
                                        } 
                                        $prix = explode(".", "$total_ligne");?>
                                        <td class="text-left"><p>
                                            <?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€
                                        </p></td>
                                    </tr>
   
                                    <?php 
                                        //calcul du sous-total par vendeur
                                        $sous_total_final += $total_ligne;        
                                } 
                            } ?>
                            <!---sous-total par vendeur--->
                            <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                <th class="text-left py-2 pl-3"><h4>Sous-total vendeur</h4></th>
                                <?php $prix = explode(".", "$sous_total_final");?>
                                <th class="text-left"><p>
                                    <?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€
                                </p></th>
                            </tr>
                            <?php
                                $sous_total_final = 0;
                        } ?>
                    </tbody>
                    <tfoot>
                        <!---affichage des totaux de la commande--->
                        <!---total ht--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                            <th class="text-left py-2 pl-3"><h4>Total HT : </h4></th>
                            <?php $prix = explode(".", "$total_ht");?>
                            <th class="text-left"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        </tr>
                        <!---total ttc--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                            <th class="text-left py-2 pl-3"><h4>Total TTC : </h4></th>
                            <?php $prix = explode(".", "$total_ttc");?>
                            <th class="text-left"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        </tr>
                        <!---quantité totale--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                            <th class="text-left py-2 pl-3"><h4>Quantité<br>totale : </h4></th>
                            <th class="text-left"><h4><?php echo $quantite_totale;?></h4></th>
                        </tr>
                        <!---total final--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                            <th class="text-left py-2 pl-3"><h4>Total Final : </h4></th>
                            <?php $prix = explode(".", "$total_final");?>
                            <th class="text-left"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        
            <!---case à cocher : acceptation des cgv--->
            <div class="flex flex-row flex-wrap justify-center mt-10 mb-2">
                <label for="case" class="underline! cursor-pointer hover:text-rouge">J'ai lu et j'acccepte les conditions <br class="md:hidden"> générales de vente</label>
                <input type="checkbox" id="case" name="case" required class="cursor-pointer ml-5 w-5 h-5">
                <span id="error-cgv" class="ml-2 text-rouge hidden">Vous devez accepter les CGV.</span>    
            </div>

            <!---boutons annuler et valider--->
            <div class="flex justify-center mt-10 mb-10">
                <!---bouton annuler--->
                <a href="../fo/panier.php?idPanier=<?php echo $idPanier ;?>" 
                   class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5 mr-15">Annuler</a>
                   <!---récupération de l'id panier--->
                <input type="hidden" name="idPanier" value="<?= $idPanier ?>">
                <!---bouton valider--->
                <input class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5" 
                       type="submit" name="valider" value="Valider" onclick="return verifierCGV()">

            </div>
        </form>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
    <script src="../../js/fo/recap_commande.js"></script>
</body>
</html>