<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ .'/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../php/fonctions.php");
    require_once(__DIR__ . '/../../php/verification_formulaire.php');
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits et photos
        foreach($dbh->query("SELECT pr.id_produit, pr.libelle_produit, pr.prix_ttc, pr.note_moyenne, pr.quantite_stock, p.url_photo, p.alt, p.titre
                            FROM sae3_skadjam._produit pr 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._montre m
                                ON m.id_produit = pr.id_produit
                            INNER JOIN sae3_skadjam._photo p
                                ON p.id_photo = m.id_photo
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        } 
        
        $qteStock = $row['quantite_stock'];
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
       
    //traitement de la modification de la quantite_stock
    if (isset($_POST['qteStock']) && is_array($_POST['qteStock'])) {
        //màj base de données
        $updateStock = $dbh->prepare("
            UPDATE sae3_skadjam._produit
            SET quantite_stock = :stock
            WHERE id_produit = :id");

        foreach ($_POST['qteStock'] as $idProduit => $qteStock) {
            $variationQte = 0;

            //si le vendeur ajouter ou retire une certaine quantite du stock
            if ((isset($_POST["qteAajouter"][$idProduit]) || isset($_POST["qteAretirer"][$idProduit])) 
                && ($_POST["qteAajouter"][$idProduit] > 0 || $_POST["qteAretirer"][$idProduit] > 0)){

                $aAjouter = 0;
                $aRetirer = 0;

                $aAjouter = isset($_POST["qteAajouter"][$idProduit])?intval($_POST["qteAajouter"][$idProduit]):0;
                $aRetirer = isset($_POST["qteAretirer"][$idProduit])?intval($_POST["qteAretirer"][$idProduit]):0;

                $variationQte = $aAjouter - $aRetirer;
            }

            // mise à jour de la base de donnée
            if (preg_match("/^-{0,1}[0-9]*$/", $qteStock)) {
                
                $nouvQte = $qteStock + $variationQte;

                $updateStock->bindParam(':stock', $nouvQte, PDO::PARAM_INT);
                $updateStock->bindParam(':id', $idProduit, PDO::PARAM_INT);
                $updateStock->execute();
            }
        }
        header("Location: ./stock.php?idCompte=$idCompte");
    } 
?>

<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[545px]">
        <h2>Stock</h2>
        
        <!---affichage si catalogue vide--->
        <?php if($tabProduit == null){ ?>
            <p>Votre catalogue de produit est vide, vous n'avez donc pas de stock.</p>
            <a href="index_vendeur.php" class="flex justify-center md:mt-15 md:mb-15 mt-5 mb-5"><button class="border-vertFonce border-2 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php } 
        
        else{?>
            <div class="flex justify-center">
                <!---tableau liste des stocks--->
                <form action="modifier_stock.php?idCompte=<?php echo $idCompte;?>" method="POST" enctype="multipart/form-data">
                    <table class="table-auto w-250">
                        <thead>
                            <tr>
                                <!---noms des colonnes--->
                                <th scope="col"></th>
                                <th scope="col"><h3 class="text-left">Produit</h3></th>
                                <th scope="col"><h3>Prix</h3></th>
                                <th scope="col"><h3>Note</h3></th>
                                <th scope="col"><h3>Stock</h3></th>
                                <th scope="col"><h3>Ajouter</h3></th>
                                <th scope="col"><h3>Retirer</h3></th>
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
                                        <td scope="row" class="text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></td>
                                        <td class="text-center py-3"><p><?php echo str_replace('.',',',$valeurs['prix_ttc']);?> €</p></td>
                                        <td class="text-center py-3">
                                            <div class="flex justify-center items-center">
                                                <?php 
                                                    $note = $valeurs['note_moyenne'];
                                                    affichageNote($note); 
                                                ?>
                                            </div>
                                        </td>

                                        <td class="text-center py-3">          
                                            <input type="number"
                                                   name="qteStock[<?php echo $valeurs['id_produit']; ?>]"
                                                   value="<?php echo $valeurs['quantite_stock']; ?>"
                                                   class="border-2 border-black rounded-lg w-30 h-10 p-2" required
                                            >
                                        </td>
                                        <td><input type="number" name="qteAajouter[<?php echo $valeurs['id_produit']; ?>]" value="0" min="0" class="border-2 border-black rounded-lg w-30 h-10 p-2"></td>
                                        <td><input type="number" name="qteAretirer[<?php echo $valeurs['id_produit']; ?>]" value="0" min="0" class="border-2 border-black rounded-lg w-30 h-10 p-2"></td>
                                    </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="flex justify-around mt-10">
                        <!---bouton annuler--->
                        <a href="../bo/stock.php?idCompte=<?php echo $idCompte ;?>" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer my-5">Annuler</a>
                        <!---bouton valider--->
                        <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer my-5" type="submit" value="Valider">
                    </div>
                </form>
            </div>
            

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>