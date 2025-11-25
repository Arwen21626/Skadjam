<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ .'/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../php/fonctions.php");
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Stock</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[545px]">
        <?php try {     
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
                                    WHERE v.id_compte = $idCompte
                                    ORDER BY libelle_produit ASC"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                } ?>

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
                                    <th scope="col"></th>
                                    <th scope="col"></th>
                                    <th scope="col"></th>
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

                                            <td class="text-center py-3">
                                                <?php if($valeurs['quantite_stock'] > 10){ ?>
                                                    <p><?php echo htmlentities($valeurs['quantite_stock']);?></p>
                                                <?php } 
                                                
                                                else{ ?>
                                                    <p class="text-rouge font-bold"><?php echo htmlentities($valeurs['quantite_stock']);?></p>
                                                <?php } ?>

                                            </td>
                                            <td class="text-center py-3">
                                                <form action="../../php/executer_ajouter_stock.php" method="POST">
                                                    <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                                    <button type="submit"><img src="../../images/logo/bootstrap_icon/plus-square.svg" alt="bouton ajouter 1 au stock" class="w-10 h-auto"></button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="../../php/executer_enlever_stock.php" method="POST">
                                                    <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                                    <input type="hidden" name="quantite" value="1">
                                                    <input type="hidden" name="stockActuel" value="<?php echo htmlentities($valeurs['quantite_stock']);?>">
                                                    <button type="submit"><img src="../../images/logo/bootstrap_icon/dash-square.svg" alt="bouton enlever 1 au stock" class="w-10 h-auto"></button>
                                                </form>
                                            </td>
                                            <td>
                                                <form action="../../php/executer_vider_stock.php" method="POST">
                                                    <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                                    <button type="submit"><img src="../../images/logo/bootstrap_icon/trash.svg" alt="bouton vider stock" class="w-10 h-auto"></button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </div>
      
                <?php }
                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>