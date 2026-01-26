<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . '/../../php/verification_formulaire.php';
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits et photos
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._promu pm
                                ON pr.id_produit = pm.id_produit
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
            if (verifQteStock($qteStock)) {
                $updateStock->bindParam(':stock', $qteStock, PDO::PARAM_INT);
                $updateStock->bindParam(':id', $idProduit, PDO::PARAM_INT);
                $updateStock->execute();
            }
        }
        header("Location: ./promotion_vendeur.php?idCompte=$idCompte");
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
        
        <?php if($tabProduit == null){ ?>
            <p class="text-center">Votre catalogue de promotions est vide, vous n'avez donc pas de produits en promotion.</p>
        <?php } 
        
        else{?>
            <div class="flex justify-center">
                <form action="modifier_promotion.php?idCompte=<?php echo $idCompte;?>" method="POST" enctype="multipart/form-data">
                    <table class="table-auto w-250">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left w-125 pl-3"><h3>Nom du produit</h3></th>
                                <th scope="col"><h3>Prix</h3></th>
                                <th scope="col"><h3>Note</h3></th>
                                <th scope="col"><h3>Stock</h3></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                                //pour changer la classe de css une ligne sur 2
                                $impair = 0;
                                $classe;
                                $classe1 = "py-4";
                                $classe2 = "py-4 bg-bleu";
                                foreach($tabProduit as $id => $valeurs){
                                    $idProduit = $valeurs['id_produit']; 
                                    $qteStock = $valeurs['quantite_stock'];
                                    $impair ++;
                                    if(fmod($impair, 2) == 0){
                                        $classe = $classe1;
                                    }
                                    else{
                                        $classe = $classe2;
                                    }?>
                                    <tr class="<?php echo $classe; ?>">
                                        <th scope="row" class="text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></th>
                                        <td class="text-center py-3"><p><?php echo htmlentities($valeurs['prix_ttc']);?> €</p></td>
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
                                                <?php //création d'un tableau associatif pour récupérer tous les id produits associés à leur stock?>
                                                   name="qteStock[<?php echo $valeurs['id_produit']; ?>]"
                                                   value="<?php echo $valeurs['quantite_stock']; ?>"
                                                   min="0" class="border-2 border-black rounded-lg w-30 h-10 p-2" required
                                            >
                                        </td>
                                    </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="flex justify-around">
                        <a href="../bo/promotion_vendeur.php?idCompte=<?php echo $idCompte ;?>" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer my-5">Retour</a>
                        <input class="border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer my-5" type="submit" value="Valider">
                    </div>
                </form>
            </div>
            

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>