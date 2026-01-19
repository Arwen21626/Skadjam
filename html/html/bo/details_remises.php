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
        foreach($dbh->query("SELECT p.id_produit, p.libelle_produit, p.prix_ttc, p.prix_remise, p.note_moyenne, r.pourcentage_remise 
                                FROM sae3_skadjam._produit p
                                INNER JOIN sae3_skadjam._vendeur v 
                                    ON p.id_vendeur = v.id_compte
                                LEFT JOIN sae3_skadjam._reduit rd
                                    ON rd.id_produit = p.id_produit
                                LEFT JOIN sae3_skadjam._remise r
                                    ON rd.id_remise = r.id_remise
                                WHERE v.id_compte = $idCompte AND p.est_supprime = false
                                ORDER BY libelle_produit ASC;"
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
    <title>Remises</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[545px]">
        <h2>Remises</h2>
        
        <?php if($tabProduit == null){ ?>
            <p>Votre catalogue de produit est vide.</p>
        <?php } 
        
        else{?>
            <div class="flex justify-center">
                <table class="table-auto w-250">
                    <!-- en tête du tableau-->
                    <thead>
                        <tr>
                            <th scope="col" class="text-left w-125 pl-3"><h3>Nom du produit</h3></th>
                            <th scope="col"><h3>Prix</h3></th>
                            <th scope="col"><h3>Prix remisé</h3></th>
                            <th scope="col"><h3>Note</h3></th>
                            <th scope="col"><h3>Remise</h3></th>
                        </tr>
                    </thead>
                    <!-- corps du tableau -->
                    <tbody>
                        <?php $ligneIndex = 0;
                            foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit'];?>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th scope="row" class="text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></th>
                                    <td class="text-center py-3"><p><?php echo htmlentities($valeurs['prix_ttc']);?> €</p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($valeurs['prix_remise']);?> €</p></td>
                                    <td class="text-center py-3">
                                        <div class="flex justify-center items-center">
                                            <?php 
                                                $note = $valeurs['note_moyenne'];
                                                affichageNote($note); 
                                            ?>
                                        </div>
                                    </td>

                                    <td class="text-center py-3"><p><?php echo htmlentities(($valeurs['pourcentage_remise'] === null)?"0 %":($valeurs['pourcentage_remise']*100).' %'); ?></p></td>
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <!-- modifier les remises remises -->
            <div class="flex justify-center mt-15">
                <button class="border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer">
                    <a href="../bo/modifier_remises.php" class="">Modifier remises</a>
                </button>
            </div>
            

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>