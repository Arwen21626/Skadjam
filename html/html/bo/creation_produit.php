<?php

include( __DIR__ . '/../../01_premiere_connexion.php');
require_once(__DIR__ . '/../../php/verification_formulaire.php');

session_start();

//Récupération id vendeur
$idVendeur = $_SESSION['idCompte'];

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

if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description']) && isset($_POST['unite'])) {
    //Récupération des champs pour l'insertion
    $idCategorie = htmlentities($_POST['categorie']);
    $nom = htmlentities($_POST['nom']);
    $prixHT = htmlentities($_POST['prix']);
    $qteStock = htmlentities($_POST['qteStock']);
    $description = htmlentities($_POST['description']);
    $unite = htmlentities($_POST['unite']);
    $qteUnite = htmlentities($_POST['qteUnite']);

    // $enPromotion = htmlentities($_POST['mettreEnPromotion']);
    if(isset($_POST['mettreEnLigne'])){
        $enLigne = htmlentities($_POST['mettreEnLigne']);
    }
    

    //Récupération du nom de la catégorie pour la gestion de la tva
    foreach ($tab_categories as $c) {
        if ($c['id_categorie'] == $idCategorie) {
            $nomCategorie = $c['libelle_categorie'];
        }
    }
    
    if(isset($_POST['mettreEnLigne'])){
        if($_POST['mettreEnLigne'] == false) {
            //S'il n'est pas coché il faut mettre est_masque dans la BDD à true en chaine pour eviter les problèmes
            $enLigne = 'true';
        }
        else{
            $enLigne = 'false';
        }
    }
    

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
    
    if (verifPrix($prixHT) && verifQteStock($qteStock) && $idCategorie != 0 && $unite != 0){
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

            //Insertion du produit
            $insertionProduit = $dbh -> query("WITH id AS (
                INSERT INTO sae3_skadjam._produit 
                (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
                VALUES 
                ('$nom','$description', $prixHT, $prixTTC, $enLigne, $qteStock, $qteUnite, '$unite', $idCategorie, $idVendeur, $tva)
                RETURNING id_produit)
                SELECT * FROM id;
                ");
            
            foreach ($insertionProduit as $t) {
                $idProd = $t['id_produit'];
            }

            //Insertion de la photo dans la table photo
            $insertionPhoto = $dbh -> query("WITH id AS (
                INSERT INTO sae3_skadjam._photo 
                (url_photo, alt, titre)
                VALUES 
                ('/images/photo_importees/$nom_photo_finale','$nom','$nom')
                RETURNING id_photo)
                SELECT * FROM id;
                ");

            foreach ($insertionPhoto as $t) {
                $idPhoto = $t['id_photo'];
            }

            $insertionMontre = $dbh -> query("INSERT INTO sae3_skadjam._montre VALUES ($idPhoto,$idProd);");

            header("Location: ./details_produit.php?idProduit=".$idProd);

        }
        catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    else{
        echo ("Le prix ou la quantité saisi est incorrect ou la catégorie n'a pas été entré.");
    }
}
else { ?>

<!DOCTYPE html>
<html lang="fr">
    <?php include(__DIR__."/../../php/structure/head_back.php");?>
    <head>
        <title>Création d'un produit</title>
        <style>
            button a:hover{
                color : black;
            }
        </style>
    </head>
    <body>
        <?php include(__DIR__ . '/../../php/structure/header_back.php');?>
        <?php include(__DIR__ . '/../../php/structure/navbar_back.php');?>
        <main class="flex flex-col items-center">
            <h2>Création d'un produit</h2>
            <form class="grid grid-cols-[40%_60%] w-4/5 self-center" action="creation_produit.php" method="post" enctype="multipart/form-data">

                <!-- Image -->
                <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                    <input type="file" id="photo" name="photo" class="hidden" required>
                    <!-- label qui agit comme bouton -->
                    <label for="photo" class="bg-beige w-60 h-60 rounded-2xl image-produit" style="background-image: url('../../images/logo/bootstrap_icon/image.svg'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                    <label class="cursor-pointer" for="photo">Ajouter une image*</label>
                </div>

                <!-- Nom produit -->
                <div class="col-start-2 row-start-1 flex flex-col w-200 m-2 p-2">
                    <label for="nom">Nom produit *:</label>
                    <input placeholder="Confiture fraises des bois 200g" class=" border-4 border-beige rounded-2xl placeholder-gray-500" type="text" name="nom" id="nom" required>
                </div>

                <div class="col-start-2 row-start-2 flex flex-row justify-between w-200 m-2 p-2">
                    <!-- Prix ht -->
                    <div class="flex flex-col">
                        <label for="prix">Prix *(hors taxe):</label>
                        <input placeholder="3.99" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="prix" id="prix" min="0.0" step="0.01" required>
                    </div>
                    <!-- Quantite en stock -->
                    <div class="flex flex-col">
                        <label for="qteStock">Quantité en stock* :</label>
                        <input placeholder="50" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="qteStock" id="qteStock" min="0" required>
                    </div>
                </div>
                    
                <div class="col-start-2 row-start-3 col-span-2 flex flex-row justify-between w-200 m-2 p-2">
                    <!-- Catégorie -->
                    <div class="flex flex-col">
                        <label for="categorie">Catégorie* :</label>
                        <select class=" border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="categorie" id="categorie" required>
                            <option value="0">Choisir</option>
                            <?php foreach ($tab_categories as $categorie) {?>
                                <option value="<?php echo $categorie['id_categorie']?>"><?php echo $categorie['libelle_categorie']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- Unité -->
                    <div class="flex flex-col">
                        <label for="unite">Unité* :</label>
                        <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="unite" id="unite" required>
                        <option value="0">Choisir</option>
                        <?php foreach ($tab_unite as $unite) {?>
                            <option value="<?php echo $unite?>"><?php echo $unite?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <!-- Quantité unité -->
                    <div class="flex flex-col">
                        <label for="qteUnite">Quantité unité :</label>
                        <input placeholder="200" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="qteUnite" id="qteUnite" min="0" required>
                    </div>
                </div>

                
                <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                    <!-- Mettre en ligne -->
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                        <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige cursor-pointer" type="checkbox" name="mettreEnLigne" id="mettreEnLigne">
                    </div>
                
                    <!-- Mettre en promotion -->
                    <!-- <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnPromotion">Mettre en promotion</label>
                        <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">
                    </div> -->
                </div>
                
                <!-- Description -->
                <div class="col-start-1 col-span-2 row-start-5 flex flex-col m-2 p-2 ">
                    <label for="description">Description *:</label>
                    <textarea placeholder="Pot de confiture de fraises des bois" class="border-4 border-beige rounded-2xl w-3/4 self-center placeholder-gray-500" name="description" id="description" cols="100" rows="10" required></textarea>
                </div>
                
                <!-- Validation -->
                <div class="col-start-1 col-span-2 row-start-6 flex flex-row justify-around m-4">
                    <a href="../bo/index_vendeur.php">
                        <button class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer">Retour</button>
                    </a>
                    
                    <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer" type="submit" value="Valider">
                </div>
            </form>
        </main>
        <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
        <script src="../../js/bo/changement_image_produits.js"></script>
    </body>
</html>

<?php } ?>
