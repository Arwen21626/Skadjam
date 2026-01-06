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
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr
                            INNER join sae3_skadjam._montre m
                                ON pr.id_produit=m.id_produit
                            INNER JOIN sae3_skadjam._photo ph  
                                ON ph.id_photo = m.id_photo 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        } 
        
        //$qteStock = $row['quantite_stock'];

    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
       
    /*if(isset($_POST['qteStock']) && (verifQteStock($qteStock))){
        $qteStock = $_POST['qteStock'];
        $idProduit = $_POST['id'];

        $updateStock = $dbh->prepare("UPDATE sae3_skadjam._produit
                                    SET quantite_stock = $qteStock
                                    WHERE id_produit = $idProduit;") ;
        $updateStock->execute();
        echo $idProduit;

    }*/

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
        
        <?php if($tabProduit == null){ ?>
            <p>Votre catalogue de produit est vide, vous n'avez donc pas de stock.</p>
        <?php } 
        
        else{?>
            <div class="flex justify-center">
                <table class="table-auto w-250">
                    <thead>
                        <tr>
                            <th scope="col" class="text-left w-125 pl-3"><h3>Nom du produit</h3></th>
                            <th scope="col"><h3>Prix</h3></th>
                            <th scope="col"><h3>Note</h3></th>
                            <th scope="col"><h3>Stock</h3></th>
                            <!--<th scope="col"></th>
                            <th scope="col"></th>
                            <th scope="col"></th>-->
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $impair = 0;
                            $classe;
                            $classe1 = "py-4";
                            $classe2 = "py-4 bg-bleu";
                            foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit']; 
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

                                    <td class="text-center py-3"><p><?php echo htmlentities($valeurs['quantite_stock']); ?></p></td>
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <a href="../bo/modifier_stock.php?idCompte=<?php echo $idCompte ;?>" class="flex justify-end mr-60 mt-15"><button class="border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer">Modifier stocks</button></a>
            

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>