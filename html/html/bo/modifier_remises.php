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

    //traitement de la modification du pourcentage d'une remise
    if (isset($_POST['pourcentage']) && is_array($_POST['pourcentage'])) {

        // pour la supression d'une remise
        $deleteRemise = $dbh->prepare("
            DELETE FROM sae3_skadjam._remise
            WHERE id_remise = ?");
        $deleteReduit = $dbh->prepare("
            DELETE FROM sae3_skadjam._reduit
            WHERE id_remise = ? AND id_produit = ?");

        // pour créer une nouvelle remise
        $insertRemise = $dbh->prepare("
            WITH id_remise AS (
                INSERT INTO sae3_skadjam._remise(pourcentage_remise, date_debut_remise) 
                VALUES (?, ?) RETURNING id_remise
            )
            INSERT INTO sae3_skadjam._reduit(id_produit, id_remise) 
                SELECT ?, id_remise FROM id_remise");
            

        // pour la modification d'une remise
        $updateRemise = $dbh->prepare("
            UPDATE sae3_skadjam._remise
            SET pourcentage_remise = ?
            WHERE id_remise = ?");
        
        //mise à jour de la base de données
        foreach ($_POST['pourcentage'] as $idProduit => $pourcentage) {
            $pourcentage = ($pourcentage/100);
            $existe = false;  //si le produit a déjà une remise
            foreach($dbh->query("SELECT * FROM sae3_skadjam._reduit WHERE id_produit = $idProduit", PDO::FETCH_ASSOC) as $row){
                $existe = true;
                // modification d'une remise
                if (verifPourcentage($pourcentage) && $pourcentage != 0) {
                    $updateRemise->execute([$pourcentage, $row['id_remise']]);
                }
                // supression d'une remise
                elseif(verifPourcentage($pourcentage) && $pourcentage == 0){
                    $deleteReduit->execute([$row['id_remise'], $idProduit]);
                    $deleteRemise->execute([$pourcentage]);
                }
                else{
                    echo "le format du pourcentage n'est pas correcte";
                }
            }
            // insertion d'une remise
            if (!$existe && $pourcentage != 0){
                $date = date('d/m/Y'); 
                $insertRemise->execute([$pourcentage, $date, $idProduit]);
            }
        }
        
        header("Location: ./details_remises.php");
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
                <form action="modifier_remises.php" method="POST" enctype="multipart/form-data">
                    <table class="table-auto w-250">
                        <!-- entete du tableau -->
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
                            <?php $ligneIndex = 1;
                                foreach($tabProduit as $id => $valeurs){
                                    $idProduit = $valeurs['id_produit'];?>
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
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

                                        <td class="text-center py-3 flex items-center">          
                                            <input type="number"
                                                <?php //création d'un tableau associatif pour récupérer tous les id produits associés à leur remise?>
                                                   name="pourcentage[<?php echo $valeurs['id_produit']; ?>]"
                                                   value="<?php echo htmlentities($valeurs['pourcentage_remise'] === null)?0:($valeurs['pourcentage_remise']*100); ?>"
                                                   min="0" max="100" class="border-2 border-black rounded-lg w-30 h-10 p-2" required
                                            >
                                            <p class="ml-2">%</p>
                                        </td>
                                    </tr>
                            <?php }?>
                        </tbody>
                    </table>
                    <div class="flex justify-around">
                        <a href="../bo/details_remises.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer mt-15">Retour</a>
                        <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer mt-15" type="submit" value="Valider">
                    </div>
                </form>
            </div>
            

        <?php } ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>