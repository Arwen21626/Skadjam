<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ .'/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../php/fonctions.php");
    require_once(__DIR__ . '/../../php/verification_formulaire.php');
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits
        foreach($dbh->query("SELECT pr.id_produit, pr.libelle_produit, pr.prix_ttc, pr.note_moyenne, pr.quantite_stock
                            FROM sae3_skadjam._produit pr
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
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
                        <?php $ligneIndex = 1;
                            foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit'];?>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                    <!---informations des stocks--->
                                    <td scope="row" class="text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></td>
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
            <div class="flex justify-around mt-10">
                <!---bouton annuler--->
                <a href="index_vendeur.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer my-5">Retour</a>
                <!---bouton modifier stock--->
                <button class="border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer my-5">
                    <a href="../bo/modifier_stock.php?idCompte=<?php echo $idCompte ;?>" class="">Modifier le stock</a>
                </button>
            </div>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>