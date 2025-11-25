<?php 
    session_start();
    include(__DIR__ . "/../../01_premiere_connexion.php");
    include(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];

    

    if(isset($_POST['note'])){
        // Traitement données
        try{
            $note = htmlentities($_POST['note']);
            $commentaire = htmlentities($_POST['commentaire']);
            $idCompte = $_SESSION['idCompte'];
            if ($note>=0 && $note<=5){
                $insertionAvis = $dbh->prepare("INSERT INTO sae3_skadjam._avis(nb_etoile, nb_pouce_haut, nb_pouce_bas, contenu_commentaire, id_produit, id_compte) 
                                                    VALUES ($note, 0, 0, '$commentaire', $idProd, $idCompte)");
                $insertionAvis->execute();

                header("location: details_produit.php?idProduit=$idProd");
            }
            else{
                echo "Erreur : la note entrée n'est pas correcte.";
            }
        } catch (PDOException $e) {
            print "Erreur lors de l'envoie des données vers la base de données";
            die();
        }
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
    <title>Ajouter un avis au produit : <?php echo $idProd;?></title>
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
                <input class="border-4 border-beige rounded-2xl p-1 pl-3 w-16" name="note" id="note" type="number" min="0" max="5" required>
                <img class=" w-7 ml-3" src="../../images/logo/bootstrap_icon/star-fill.svg">
            </div>
            
            <!-- Le commantaire -->
            <label class="mt-10" for="commentaire">Commentaire : </label>
            <textarea class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" name="commentaire" rows="10" cols="100"></textarea>

            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="cursor-pointer  border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10 " type="button"><a href="./details_produit.php?idProduit=<?php echo $idProd; ?>">Annuler</a></button>
                <?php 
                    // tableau contenant tous les avis
                    $avis = [];
                    foreach($dbh->query("SELECT * FROM sae3_skadjam._avis a 
                                        INNER JOIN sae3_skadjam._client c 
                                            ON a.id_compte = c.id_compte 
                                        WHERE id_produit = $idProd", PDO::FETCH_ASSOC) as $row){
                        $avis[] = $row;
                    }

                    // savoir si le client a déjà donner son avis sur le produit
                    $dejaAvis = false;
                    foreach($avis as $row){
                        if ($_SESSION['idCompte'] == $row['id_compte']){
                            $dejaAvis = true;
                        }
                    }

                    if($dejaAvis == false){
                        $a = '';
                    }else{
                        $a = 'disabled';
                    }
                ?>
                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 md:mr-10" type="submit" name="submit" id="submit" value="Valider" <?php echo $a; ?> >
                <?php if($a == 'disabled'){echo 'Vous avez déjà mis un commentaire'; }?>
            </div>
        </form>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>
</html>
<?php }?>