<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    include(__DIR__ . "/../../01_premiere_connexion.php");
    include(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];
    $idCompte = $_SESSION['idCompte'];
    
    // récupération des donnée de l'avis précédament donnée
    foreach($dbh->query("SELECT nb_etoile, contenu_commentaire, id_avis
                        FROM sae3_skadjam._avis 
                        WHERE id_produit = $idProd 
                            AND id_compte = $idCompte"
                        , PDO::FETCH_ASSOC) as $row){
        $note = $row['nb_etoile'];
        $commentaire = $row['contenu_commentaire'];
        $idAvis = $row['id_avis'];


        // Infos photo
        $tabPhoto = null;

        $reqPhoto = $dbh->prepare("SELECT ph.url_photo, ph.alt, ph.titre
                                FROM sae3_skadjam._appuie a
                                INNER JOIN sae3_skadjam._photo ph
                                    ON a.id_photo = ph.id_photo
                                WHERE a.id_avis = $idAvis");
        $reqPhoto->execute();
        $tabPhoto = $reqPhoto->fetch();
    }

    if(isset($_POST['note'])){
        //traitement des données et envoie vers la base de donnée
        try{
            $nouvNote = htmlentities($_POST['note']);
            $nouvCommentaire = htmlentities($_POST['commentaire']);

            if ($nouvNote>=0 && $nouvNote<=5){
                // si c'est l'ajout d'un nouvel avis
                if (!isset($note)){
                    $insertionAvis = $dbh->prepare("INSERT INTO sae3_skadjam._avis(nb_etoile, nb_pouce_haut, nb_pouce_bas, contenu_commentaire, id_produit, id_compte) 
                                                    VALUES (?, 0, 0, ?, ?, ?)");
                }
                // si c'est la modification d'un avis
                else{
                    $insertionAvis = $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_etoile = ?, contenu_commentaire = ?
                                                    WHERE id_produit = ? AND id_compte = ?");
                }
                $insertionAvis->execute([$nouvNote, $nouvCommentaire, $idProd, $idCompte]);
                header("location: ./details_produit.php?idProduit=$idProd&avisAjouter=1");
                exit();
            }
            else{
                echo "Erreur : la note entrée n'est pas correcte.";
            }
        } catch (PDOException $e) {
            print "Erreur lors de l'envoie des données vers la base de données";
            die();
        }
    }
    // suppresion d'un avis
    else if(isset($_GET['supr']) && $_GET['supr'] === 'true'){
        $supprReponse = $dbh->prepare("DELETE FROM sae3_skadjam._reponse WHERE id_avis = ?");
        $suprAsignaler = $dbh->prepare("DELETE FROM sae3_skadjam._a_signaler WHERE id_avis = ?");
        $suprPouces = $dbh->prepare("DELETE FROM sae3_skadjam._pouces WHERE id_avis = ?");
        $suprAvis = $dbh->prepare("DELETE FROM sae3_skadjam._avis WHERE id_produit = ? AND id_compte = ?");
        $supprReponse->execute([$idAvis]);
        $suprAsignaler->execute([$idAvis]);
        $suprPouces->execute([$idAvis]);
        $suprAvis->execute([$idProd, $idCompte]);
        
        header("location: ./details_produit.php?idProduit=$idProd&avisSupprimer=1");
        exit();
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

    <!---popup avis ajouté--->
    <div id="popup-overlay" class="right-12 md:right-40">
        <div id="popup-ajouter-avis" class="popup p-4 border-vertFonce shadow-xl">
            <p>Votre avis a bien été ajouté !</p>
            <div class="flex justify-around mt-2">
                <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
            </div>
        </div>
    </div>

    <main class="p-4 md:pl-8 pr-8 ">
        <h2 class="text-center"><?php echo $produit['libelle_produit'];?></h2>
        <form class="flex flex-col justify-start items-start m-10" action="./ajouter_avis.php?idProduit=<?php echo $produit['id_produit']?>" method="post">
            <!-- La note -->
            <label for="note">Note* :</label>
            <div class="flex flex-nowarp items-center justify-center">
                <input class="border-4 border-beige rounded-2xl p-1 pl-3 w-16" name="note" id="note" type="number" min="0" max="5" value="<?php if(isset($note)){echo $note;}?>" required>
                <img class=" w-7 ml-3" src="../../images/logo/bootstrap_icon/star-fill.svg">
            </div>
            <!-- Le commentaire -->
            <label class="mt-10" for="commentaire">Commentaire : </label>
            <textarea class="border-4 border-beige rounded-2xl w-full p-1 pl-3" name="commentaire" rows="10" cols="100"><?php if(isset($commentaire)){echo $commentaire;}?></textarea>

                           
            <!---ligne de boutons--->
            <div class="flex flex-col items-center md:flex-row md:mt-10 mt-5 md:justify-end w-full">
                <!-- Supression-->
                <?php if (isset($note) && $note !== null){ // on peut supprimer un avis que si on est entrain de la modifier ?>
                <button class="cursor-pointer border-2 border-rouge md:rounded-2xl rounded-xl md:w-40 w-36 md:h-14 h-10 p-2 m-1 md:mr-10 mb-3">
                    <a href="./ajouter_avis.php?idProduit=<?php echo $produit['id_produit']?>&supr=true">Supprimer</a>
                </button>        
                <?php }?>

                <!---Annuler--->
                <button class="cursor-pointer border-2 border-vertClair md:rounded-2xl rounded-xl md:w-40 w-36 md:h-14 h-10 p-2 m-1 md:mr-10 mb-3" type="button">
                    <a href="./details_produit.php?idProduit=<?php echo $idProd; ?>">Annuler</a>
                </button>
                
                <!---Valider--->
                <input class="cursor-pointer border-2 border-vertClair md:rounded-2xl rounded-xl md:w-40 w-36 md:h-14 h-10 p-2 m-1 md:mr-10" type="submit" name="submit" id="submit" value="Valider" >
            </div>
        </form>
        
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>

<script type="module">
    import * as Popup from "../../js/popup.js";

    const btnClosePopUp = document.getElementById("popup-ajouter-avis").querySelector("button");

    btnClosePopUp.addEventListener("click", () => {
        Popup.closePopup("popup-ajouter-avis");
    });

    Popup.showPopUp("popup-ajouter-avis", 5000, "avisAjouter");
</script>

</html>
<?php }?>