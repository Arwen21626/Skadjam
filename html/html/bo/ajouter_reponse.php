<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ . "/../../01_premiere_connexion.php");
    include(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];
    $idCompte = $_SESSION['idCompte'];
    
    if (isset($_GET['idAvis'])){
        $idAvis = $_GET['idAvis'];
        
        // récupération des donnée de la réponse précédament donnée
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._reponse 
                            WHERE id_avis = $idAvis"
                            , PDO::FETCH_ASSOC) as $row){
            $commentaire = $row['contenu_reponse'];
            $dejaReponse = true;
            $idVendeur = $row['id_compte'];
        }
        if(isset($_POST['reponse'])){
            //traitement des données et envoie vers la base de donnée
            try{
                $nouvReponse = htmlentities($_POST['reponse']);
                // si c'est l'ajout d'un nouvel avis
                if (!isset($dejaReponse)){
                    $insertionAvis = $dbh->prepare("INSERT INTO sae3_skadjam._reponse(contenu_reponse, id_compte, id_avis) 
                                                    VALUES (?, ?, ?)");
                }
                // si c'est la modification d'un avis
                else{
                    $insertionAvis = $dbh->prepare("UPDATE sae3_skadjam._reponse SET contenu_reponse = ?
                                                    WHERE id_compte = ? AND id_avis = ?");
                }
                $insertionAvis->execute([$nouvReponse, $idCompte, $idAvis]);
                header("location: details_produit.php?idProduit=$idProd");
            
            } catch (PDOException $e) {
                print "Erreur lors de l'envoie des données vers la base de données";
                die();
            }
        }
        // suppresion d'un avis
        else if(isset($_GET['supr']) && $_GET['supr'] === 'true'){
            $suprReponse = $dbh->prepare("DELETE FROM sae3_skadjam._reponse WHERE id_avis = ?");
            $suprReponse->execute([$idAvis]);

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
<?php require(__DIR__ . "/../../php/structure/head_back.php") ?>
<head>
    <title>Modifier un avis au produit : <?php echo $idProd;?></title>
</head>
<body>
    <?php require(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php require(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="p-4 md:pl-8 pr-8 ">
        <h2 class="text-center"><?php echo $produit['libelle_produit'];?></h2>
        <form class="flex flex-col justify-start items-start m-10" action="./ajouter_reponse.php?idProduit=<?php echo $produit['id_produit']?>&idAvis=<?php echo $_GET['idAvis']?>" method="post">
            <!-- La réponse -->
            <label class="mt-10" for="reponse">Réponse : </label>
            <textarea class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" name="reponse" rows="10" cols="100" required><?php echo $commentaire;?></textarea>

            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="cursor-pointer  border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10 " type="button"><a href="./details_produit.php?idProduit=<?php echo $idProd; ?>">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 md:mr-10" type="submit" name="submit" id="submit" value="Valider" >
            </div>
        </form>
        <!-- Supression -->
        <?php if ($dejaReponse){ // on peut supprimer une réponse que si on est entrain de la modifier ?>
            <a class="ml-10 flex justify-center mb-5 md:inline-block" href="./ajouter_reponse.php?idProduit=<?php echo $produit['id_produit']?>&idAvis=<?php echo $idAvis;?>&supr=true">Supprimer mon avis</a>
        <?php }?>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_back.php") ?>
</body>
</html>
<?php }
}?>