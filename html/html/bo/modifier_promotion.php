<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ .'/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../php/fonctions.php");
    require_once(__DIR__ . '/../../php/verification_formulaire.php');
    require_once(__DIR__ . "/../../php/modification_variable.php");
    $idCompte = $_SESSION['idCompte'];


    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits et photos
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._montre m
                                ON m.id_produit = pr.id_produit
                            INNER JOIN sae3_skadjam._photo p
                                ON p.id_photo = m.id_photo
                            INNER JOIN sae3_skadjam._promu pmu
                                ON pr.id_produit = pmu.id_produit
                            INNER JOIN sae3_skadjam._promotion pmn
                                ON pmn.id_promotion = pmu.id_promotion
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        }
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
       
    //traitement de la modification de la quantite_stock
    if (!empty($_POST["dateDebutPromotion"])) {

        foreach($tabProduit as $produit){

            $idProd = $produit['id_produit'];

            $dateDebut = formatDate($_POST["dateDebutPromotion"]);
            $dateFin = $_POST["dateFinPromotion"] != null ? formatDate($_POST["dateFinPromotion"]) : null;
            $label = $_POST["labelPromo"] ?? null;
            $checkbox = $_POST["checkbox_$idProd"] ?? "off";

            // si la checkbox est cochée -> UPDATE
            if($checkbox == "on"){

                $sql = "UPDATE sae3_skadjam._promotion pmn
                        SET date_debut_promotion = :dateDebut,
                            date_fin_promotion = :dateFin,
                            label = :label
                        FROM sae3_skadjam._promu pmu
                        WHERE pmn.id_promotion = pmu.id_promotion
                        AND pmu.id_produit = :idProduit";

                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    ':dateDebut' => $dateDebut,
                    ':dateFin' => $dateFin,
                    ':label' => $label,
                    ':idProduit' => $idProd
                ]);

            }else{
                // suppression de la promotion si décoché
                $dbh->prepare("DELETE FROM sae3_skadjam._promu WHERE id_produit = :id")
                    ->execute([':id' => $idProd]);
            }
        }

        header("Location: ./promotion_vendeur.php?idCompte=$idCompte");
        exit();
    } 
?>

<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits promus</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[545px]">
        <h2>Vos produits promus</h2>
        
        <!---affichage si catalogue vide--->
        <?php if($tabProduit == null){ ?>
            <p>Vous n'avez pas de produits en promotion.</p>
            <a href="index_vendeur.php" class="flex justify-center md:mt-15 md:mb-15 mt-5 mb-5"><button class="border-vertFonce border-2 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php } 
        
        else{?>
            <div class="flex justify-around items-center pl-20 pr-10 mb-14">
                <!---tableau liste des promotion--->
                <form action="modifier_promotion.php?idCompte=<?php echo $idCompte;?>" method="POST" enctype="multipart/form-data" class="flex justify-center flex-row-reverse w-1/1">
                    
                    <div class="flex flex-col sticky top-1/4 h-50">
                        <!---bouton annuler--->
                        <a href="../bo/promotion_vendeur.php?idCompte=<?php echo $idCompte ;?>" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer">Annuler</a>
                        <!---bouton valider--->
                        <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer mt-7" type="submit" value="Valider">
                    </div>

                    <table class="table-auto w-7xl mr-[2em]">
                        <thead>
                            <tr>
                                <!---noms des colonnes--->
                                <th scope="col"></th>
                                <th scope="col"><h3 class="text-left">Produit</h3></th>
                                <th scope="col"><h3>Début</h3></th>
                                <th scope="col"><h3>Fin</h3></th>
                                <th scope="col"><h3>Libellé</h3></th>
                                <th scope="col"><h3>En promotion</h3></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $ligneIndex = 1;
                                foreach($tabProduit as $id => $valeurs){
                                    $idProduit = $valeurs['id_produit']; 
                                    $qteStock = $valeurs['quantite_stock'];?>
                                    <!---informations des stocks--->
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                        <td class="py-3 w-24 text-center">
                                            <img class="w-16 h-16 object-contain inline-block" 
                                                src="<?php echo $valeurs['url_photo'];?>" 
                                                alt="<?php echo $valeurs['alt'];?>" 
                                                title="<?php echo $valeurs['titre'];?>">
                                        </td>
                                        <td scope="row" class="w-100 text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></td>
                                        <td class="text-center py-3"><input class="border-2 border-black rounded-lg w-43 h-10 p-2" type="date" name="dateDebutPromotion" id="dateDebutPromotion" value="<?php echo formatDate($valeurs['date_debut_promotion']); ?>"></td>
                                        <td class="text-center py-3"><input class="border-2 border-black rounded-lg w-43 h-10 p-2" type="date" name="dateFinPromotion" id="dateFinPromotion" value="<?php echo formatDate($valeurs['date_fin_promotion']); ?>"></td>
                                        <td class="text-center py-3"><input maxlength="20" class="border-2 border-black rounded-lg w-30 h-10 p-2" type="text" name="labelPromo" id="labelPromo" value="<?php echo $valeurs['label'];?>"></td>
                                        <td class="text-center py-3"><input class="size-5" type="checkbox" name="checkbox_<?= $valeurs["id_produit"]; ?>" id="checkbox_<?= $valeurs["id_produit"]; ?>" checked></td>
                                    </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </form>
            </div>
            <?php if(isset($_POST['dateDebutPromotion']) && $_POST['dateDebutPromotion'] == null){ ?>
                <p class="text-rouge text-center">Une promotion doit avoir une date de début</p>
            <?php } ?>

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
    <script src="../../js/bo/modifier_stock.js"></script>
</body>
</html>