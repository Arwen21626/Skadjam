<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    include(__DIR__ . "/../../01_premiere_connexion.php");
    include(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];
    $idCompte = $_SESSION['idCompte'];
    
    // récupération des donnée de l'avis précédament donnée
    foreach($dbh->query("SELECT nb_etoile, contenu_commentaire 
                        FROM sae3_skadjam._avis 
                        WHERE id_produit = $idProd 
                            AND id_compte = $idCompte"
                        , PDO::FETCH_ASSOC) as $row){
        $note = $row['nb_etoile'];
        $commentaire = $row['contenu_commentaire'];
    }

    if(isset($_POST['note'])){
        //traitement des données et envoie vers la base de donnée
        try{
            $nouvNote = htmlentities($_POST['note']);
            $nouvCommentaire = htmlentities($_POST['commentaire']);

            if ($nouvNote>=0 && $nouvNote<=5){
                // si c'est l'ajout d'un nouvel avis
                if ($note == null){
                    $insertionAvis = $dbh->prepare("INSERT INTO sae3_skadjam._avis(nb_etoile, nb_pouce_haut, nb_pouce_bas, contenu_commentaire, id_produit, id_compte) 
                                                    VALUES ($nouvNote, 0, 0, '$nouvCommentaire', $idProd, $idCompte)");
                }
                // si c'est la modification d'un avis
                else{
                    $insertionAvis = $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_etoile = $nouvNote, contenu_commentaire = '$nouvCommentaire'
                                                    WHERE id_produit = $idProd AND id_compte = $idCompte");
                }
                $insertionAvis->execute();
                header("location: details_produit.php?idProduit=$idProd");
            }
            else{
                echo "Erreur : la note entrée n'est pas correcte.";
            }
        } catch (PDOException $e) {
            print "Erreur lors de l'envoie des données vers la base de données";
            echo $e;
            die();
        }
    }
    else if(isset($_GET['supr']) && $_GET['supr'] === 'true'){
        $suprAvis = $dbh->prepare("DELETE FROM sae3_skadjam._avis WHERE id_produit = $idProd AND id_compte = $idCompte");
        $suprAvis->execute();

        header("location: details_produit.php?idProduit=$idProd");
    }
    else{
        // Récupération des données du produit
        foreach($dbh->query("SELECT *, est_masque::char AS est_masque_char
                            FROM sae3_skadjam._produit pr
                            WHERE pr.id_produit = $idProd"
                            , PDO::FETCH_ASSOC) as $row){
            $produit = $row;
        }
        
        

?> 


<!-- Formulaire -->

<!DOCTYPE html>
<html lang="fr">
<?php require(__DIR__ . "/../../php/structure/head_front.php") ?>
<head>
    <title>Modifier un avis au produit : <?php echo $idProd;?></title>
</head>
<body>
    <?php require(__DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php require(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="p-4 md:pl-8 pr-8 ">
        <h2 class="text-center"><?php echo $produit['libelle_produit'];?></h2>
        <form class="flex flex-col justify-start items-start m-10" action="./ajouter_avis.php?idProduit=<?php echo $produit['id_produit']?>" method="post">
            <!-- La note -->
            <label for="note">Note* :</label>
            <div class="flex flex-nowarp items-center justify-center">
                <input class="border-4 border-beige rounded-2xl p-1 pl-3 w-16" name="note" id="note" type="number" min="0" max="5" value="<?php echo $note;?>" required>
                <img class=" w-7 ml-3" src="../../images/logo/bootstrap_icon/star-fill.svg">
            </div>
            
            <!-- Le commantaire -->
            <label class="mt-10" for="commentaire">Commentaire : </label>
            <textarea class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" name="commentaire" rows="10" cols="100"><?php echo $commentaire;?></textarea>

            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="cursor-pointer  border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10 " type="button"><a href="./details_produit.php?idProduit=<?php echo $idProd; ?>">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 md:mr-10" type="submit" name="submit" id="submit" value="Valider" >
            </div>
        </form>
        <!-- Supression -->
        <?php if ($note !== null){ // on peut supprimer un avis que si on est entrain de la modifier ?>
            <a class="ml-10 flex justify-center mb-5 md:inline-block" href="./ajouter_avis.php?idProduit=<?php echo $produit['id_produit']?>&supr=true">Supprimer mon avis</a>
        <?php }?>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>
</html>
<?php }?>