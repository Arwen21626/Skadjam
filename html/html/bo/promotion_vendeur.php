<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . '/../../php/verification_formulaire.php';
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits
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

    }catch(PDOException $e){
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . "/../../php/structure/head_back.php";?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits promus</title>
</head>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[545px]">
        <h2>Vos produits promus</h2>

        <?php if($tabProduit == null){ ?>
            <p class="text-center">Votre catalogue de promotions est vide, vous n'avez donc pas de produits en promotion.</p>
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
        <?php } ?>
    </main>

    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_back.php"; ?>
</body>
</html>