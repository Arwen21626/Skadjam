<?php 
    session_start();
    $_SESSION['idCompte'] = 1;
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once(__DIR__ . "/../../php/fonctions.php");
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Stock</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main>
        <?php try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT *
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    WHERE v.id_compte = $idCompte"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                } ?>

                <h2>Stock</h2>
                
                <?php if($tabProduit == null){ ?>
                    <p>Votre catalogue de produit est vide, vous n'avez donc pas de stock.</p>
                <?php } 
                
                else{?>
                    <table class="table-auto w-200">
                        <thead>
                            <tr>
                                <th scope="col" class="text-left"><h3>Nom du produit</h3></th>
                                <th scope="col"><h3>Prix</h3></th>
                                <th scope="col"><h3>Note</h3></th>
                                <th scope="col"><h3>Stock</h3></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit']; ?>
                                <tr>
                                    <th scope="row" class="text-left"><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo htmlentities($valeurs['libelle_produit']); ?></a></th>
                                    <td class="text-center"><p><?php echo htmlentities($valeurs['prix_ttc']);?> €</p></td>
                                    <td><?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?></td>
                                    <td class="text-center"><p><?php echo htmlentities($valeurs['quantite_stock']);?></p></td>
                                    <td>
                                        <form action="../../php/executer_ajouter_stock.php" method="POST">
                                            <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                            <button type="submit"><img src="../../images/logo/bootstrap_icon/plus-square.svg" alt="bouton ajouter 1 au stock"></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="../../php/executer_enlever_stock.php" method="POST">
                                            <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                            <input type="hidden" name="quantite" value="1">
                                            <input type="hidden" name="stockActuel" value="<?php echo htmlentities($valeurs['quantite_stock']);?>">
                                            <button type="submit"><img src="../../images/logo/bootstrap_icon/dash-square.svg" alt="bouton enlever 1 au stock"></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form action="../../php/executer_vider_stock.php" method="POST">
                                            <input type="hidden" name="id" value="<?php echo htmlentities($idProduit);?>">
                                            <button type="submit"><img src="../../images/logo/bootstrap_icon/trash.svg" alt="bouton vider stock"></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php }?>
                        </tbody>
                    </table>
      
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