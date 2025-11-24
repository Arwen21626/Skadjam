<?php

include( __DIR__ . '/../../01_premiere_connexion.php');
require_once(__DIR__ . '/../../php/verification_formulaire.php');
session_start();

$idProduit = $_GET['idProduit'];

//Tableau pour les catégories de la base
$tab_categories = [];

//Tableau pour les tva
$tab_tva = [];

//Tableau pour les unites
$tab_unite = ["Piece", "Litre","cl","g","kg","S","M","L","XL","XXL","m","cm"];

//Requete récupération categories
foreach($dbh->query('SELECT * from sae3_skadjam._categorie', PDO::FETCH_ASSOC) as $row) {
    $tab_categories[] = $row;
}

//Requete récupération TVA
foreach($dbh->query('SELECT * from sae3_skadjam._tva', PDO::FETCH_ASSOC) as $row) {
    $tab_tva[] = $row;
}

//Recuperation de toutes les informations du produit
foreach($dbh->query("SELECT *,est_masque::CHAR as est_masque_php 
                        FROM sae3_skadjam._produit pr
                        INNER JOIN sae3_skadjam._categorie c
                            ON pr.id_categorie = c.id_categorie
                        INNER JOIN sae3_skadjam._montre m
                            ON pr.id_produit = m.id_produit
                        INNER JOIN sae3_skadjam._photo ph
                            ON m.id_photo = ph.id_photo
                        WHERE pr.id_produit = $idProduit") as $produit){      

    //Récupération attribut de produit
    $nom = $produit['libelle_produit'];
    $description = $produit['description_produit'];
    $prixHT = $produit['prix_ht'];
    $enLigne = $produit['est_masque_php']; 
    $qteStock = $produit['quantite_stock'];
    $qteUnite = $produit['quantite_unite'];
    $unite = $produit['unite'];
    $nomCategorie = $produit['libelle_categorie'];
    $idCategorie = $produit['id_categorie'];

    if($enLigne == 'f'){
        //Si dans la BDD est_masque est a false, il faut mettre enLigne à true ou l'inverse
        $enLigne = 'true';
    }
    else{
        $enLigne = 'false';
    }

    //echo $enLigne;

    //Récupération attribut de photo
    $idPhoto = $produit['id_photo'];
    $urlPhoto = $produit['url_photo'];
    $altPhoto = $produit['alt'];
    $titrePhoto = $produit['titre'];

    
}

//Traitement du formulaire
if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description']) && isset($_POST['unite'])) {
    
    //Gestion de la photo
    $typePhoto = $_FILES['photo']['type'];
    $ext = explode('/',$typePhoto)[1];
    $nom_serv_photo = $_FILES['photo']['tmp_name'];

    //Déplacement et renommage du fichier photo
    $nom_explode = explode(' ',$nom)[0];
    $currentTime = time();
    $destination = __DIR__ . '/../../images/photo_importees';
    $nom_photo_finale = $nom_explode.$currentTime.'.'.$ext;
    move_uploaded_file($nom_serv_photo,$destination.'/'.$nom_photo_finale);


    //Récupération des champs pour l'insertion
    $idCategorie = htmlentities($_POST['categorie']);
    $nom = htmlentities($_POST['nom']);
    $prixHT = htmlentities($_POST['prix']);
    $qteStock = htmlentities($_POST['qteStock']);
    $enLigne = htmlentities($_POST['mettreEnLigne']);
    $enPromotion = htmlentities($_POST['mettreEnPromotion']);
    $description = htmlentities($_POST['description']);
    $unite = htmlentities($_POST['unite']);
    $qteUnite = htmlentities($_POST['qteUnite']);

    $enPromotion = htmlentities($_POST['mettreEnPromotion']);
    $enLigne = htmlentities($_POST['mettreEnLigne']);

    //Récupération du nom de la catégorie pour la gestion de la tva
    foreach ($tab_categories as $c) {
        if ($c['id_categorie'] == $idCategorie) {
            $nomCategorie = $c['libelle_categorie'];
        }
    }
    
    if ($_POST['mettreEnLigne'] == false) {
        //S'il n'est pas coché il faut mettre est_masque dans la BDD à true en chaine pour eviter les problèmes
        $enLigne = 'true';
    }
    else{
        $enLigne = 'false';
    }

    //Vérification du prix et du stock
    if (verifPrix($prix) && verifQteStock($qteStock)){
        try{
            if ($nomCategorie == 'Alimentaire') {
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'reduit') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }
            else{
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'normal') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }
            
            //Calcul prixTTC
            $prixTTC = $prixHT*(1+$pourcentageTVA);

            //Update du produit
            $updateProduit = $dbh -> query("
                UPDATE sae3_skadjam._produit SET 
                libelle_produit = '$nom', 
                description_produit = '$description', 
                prix_ht = $prixHT, 
                prix_ttc = $prixTTC, 
                est_masque = $enLigne, 
                quantite_stock = $qteStock, 
                quantite_unite = $qteUnite, 
                unite = '$unite', 
                id_categorie = $idCategorie, 
                id_vendeur = 1, 
                id_tva = $tva
                WHERE id_produit = $idProduit
                ;");
            

            //Update de la photo dans la table photo
            $updatePhoto = $dbh -> query("
                UPDATE sae3_skadjam._photo SET
                url_photo = '/images/photo_importees/$nom_photo_finale', 
                alt = '$nom', 
                titre = '$nom'
                WHERE id_photo = $idPhoto
                ;");

        }
        catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    else{
        echo ("Le prix ou la quantité saisi est incorrect.");
    }
    header("Location: ./details_produit.php?idProduit=$idProduit");
}
else { ?>

<!DOCTYPE html>
<html lang="fr">
    <?php include(__DIR__."/../../php/structure/head_back.php");?>
    <head>
        <title>Modifier un produit</title>
        <style>
            button a:hover{
                color : black;
            }
        </style>
    </head>
    <body>
        <?php include(__DIR__ . '/../../php/structure/header_back.php');?>
        <?php include(__DIR__ . '/../../php/structure/navbar_back.php');?>
        <main>
            <h2>Modifier un produit</h2>
            <form class="grid grid-cols-[40%_60%] w-11/12 self-center" action="modifier_produit.php?idProduit=<?php echo $idProduit;?>" method="post" enctype="multipart/form-data">

                <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                    <input type="file" id="photo" name="photo" class="hidden" required>
                    <!-- label qui agit comme bouton -->
                    <label id="labelImage" for="photo" class=" w-60 h-60 rounded-xl" style="background-image: url(' <?php echo $urlPhoto ?>'); background-repeat: no-repeat; background-position: center; background-size: 100%;"></label>
                    <label for="photo">Ajouter une image*</label>
                </div>
                

                <div class="col-start-2 row-start-1 flex flex-col w-200 m-2 p-2">
                    <label for="nom">Nom produit *:</label>
                    <input value="<?php echo($nom) ;?>" class=" border-4 border-beige rounded-2xl" type="text" name="nom" id="nom" required>
                </div>

                <div class="col-start-2 row-start-2 flex flex-row justify-between w-200 m-2 p-2">
                    <div class="flex flex-col">
                        <label for="prix">Prix *(hors taxe):</label>
                        <input value="<?php echo($prixHT) ;?>" class="border-4 border-beige rounded-2xl w-75" type="number" name="prix" id="prix" min="0.0" step="0.5" required>
                    </div>
                    <div class="flex flex-col">
                        <label for="qteStock">Quantité en stock* :</label>
                        <input value="<?php echo($qteStock) ;?>" class="border-4 border-beige rounded-2xl w-75" type="number" name="qteStock" id="qteStock" min="0" required>
                    </div>
                </div>
                    
                <div class="col-start-2 row-start-3 col-span-2 flex flex-row justify-between w-200 m-2 p-2">
                    <div class="flex flex-col">
                        <label for="categorie">Catégorie* :</label>
                        <select class=" border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14" name="categorie" id="categorie" required>
                            <option value="<?php echo($idCategorie) ;?>"><?php echo($nomCategorie) ;?></option>
                            <?php foreach ($tab_categories as $categorie) {?>
                                <option value="<?php echo $categorie['id_categorie']?>"><?php echo $categorie['libelle_categorie']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="unite">Unité* :</label>
                        <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14" name="unite" id="unite" required>
                        <option value="<?php echo($unite) ;?>"><?php echo($unite) ;?></option>
                        <?php foreach ($tab_unite as $unite) {?>
                            <option value="<?php echo $unite?>"><?php echo $unite?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="qteUnite">Quantité unité :</label>
                        <input value="<?php echo($qteUnite) ;?>" class="border-4 border-beige rounded-2xl w-75" type="number" name="qteUnite" id="qteUnite" min="0" required>
                    </div>
                </div>

                
                <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                        <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnLigne" id="mettreEnLigne" <?php echo ($enLigne == 'true')?'checked':'' ?>>
                    </div>
                
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnPromotion">Mettre en promotion</label>
                        <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">
                    </div>
                </div>
                
                <div class="col-start-1 col-span-2 row-start-5 flex flex-col m-2 p-2 ">
                    <label for="description">Description *:</label>
                    <textarea class="border-4 border-beige rounded-2xl w-3/4 self-center" name="description" id="description" cols="100" rows="10" required><?php echo($description) ;?></textarea>
                </div>
                
                <div class="col-start-1 col-span-2 row-start-6 flex flex-row justify-around m-4">
                    <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="../bo/details_produit.php?idProduit=<?php echo $idProduit ;?>">Retour</a></button>
                    <input class="border-2 border-vertFonce rounded-2xl w-40 h-14" type="submit" value="Valider">
                </div>
            </form>
        </main>
        <?php include(__DIR__ . '/../../php/structure/footer_back.php');?> 
    </body>
</html>
<?php } ?>

