<?php

include(__DIR__.'/../../01_premiere_connexion.php');
require_once('../../php/verification_formulaire.php');

session_start();
//Récupération id vendeur
$idVendeur = $_SESSION['idCompte'];

//Tableau pour les catégories de la base
$tab_categories = [];

//Tableau pour les unites
$tab_unite = ["Pièce", "Litre","cl","g","kg","S","M","L","XL","XXL","m","cm"];

//Gestion de la photo
$typePhoto = $_FILES['photo']['type'];
$nom_serv_photo = $_FILES['photo']['tmp_name'];

//Requete récupération categories
foreach($dbh->query('SELECT * from sae3_skadjam._categorie', PDO::FETCH_ASSOC) as $row) {
        $tab_categories[] = $row;
    }

if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description']) && isset($_POST['unite'])) {
    //Récupération des champs pour l'insertion
    $categorie = htmlentities($_POST['categorie']);
    $nom = htmlentities($_POST['nom']);
    $prixht = htmlentities($_POST['prix']);
    $qteStock = htmlentities($_POST['qteStock']);
    $enLigne = htmlentities($_POST['mettreEnLigne']);
    $enPromotion = htmlentities($_POST['mettreEnPromotion']);
    $description = htmlentities($_POST['description']);
    $unite = htmlentities($_POST['unite']);
    echo ($unite);
    $qteUnite = htmlentities($_POST['qteUnite']);
    

    if (isset($_POST['mettreEnLigne']) && isset($_POST['mettreEnPromotion'])) {
        $enPromotion = htmlentities($_POST['mettreEnPromotion']);
        $enLigne = htmlentities($_POST['mettreEnLigne']);
    }
    else{
        $enPromotion = false;
        $enLigne = false;
    }

    //OUBLIE PAS LA PHOTO
    //Il faut récupérer l'id du vendeur pour l'insertion
    if (verifPrix($prix) && verifQteStock($qteStock)){
        try{
            //Calcul prixTTC
            $prix_ttc = $prix*1.2; //A adapter en fonction de la categorie

            //Insertion du produit
            // $insertion_produit = $dbh -> prepare("INSERT INTO sae3_skadjam._produit (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
            // VALUES ('$nom','$description', $prix, $prix_ttc, $enLigne, $qteStock, 1,'$unite',$qteUnite, $idVendeur, 1)");

            echo('salut1');
            // $test = $dbh -> query("WITH id AS (
            //     INSERT INTO sae3_skadjam._produit 
            //     (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
            //     VALUES 
            //     ('$nom','$description', $prix, $prix_ttc, false, $qteStock, $qteUnite,'$unite',1, $idVendeur, 1)
            //     RETURNING id_produit)
            //     SELECT * FROM id;
            //     ");

            $test = $dbh -> query("WITH id AS (
                INSERT INTO sae3_skadjam._produit 
                (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
                VALUES 
                ('$nom','$description', 20, 12, true, $qteStock, $qteUnite,'$unite',1, 1, 1)
                RETURNING id_produit)
                SELECT * FROM id;
                ");

            echo('salut2');
            foreach ($test as $t) {
                echo('salut3');
                echo $t['id_produit'];
                $id_prod = $t['id_produit'];
            }
            echo('salut4');
            $test -> execute();
            
            //$insertion_produit -> execute();

            //Insertion de la photo
            //$insertion_photo = $dbh -> prepare("INSERT INTO sae3_skadjam._photo ('url_photo, alt, titre') VALUES ");

            //Récupération de l'id du produit pour accéder au details
            // $recup_id_produit = $dbh -> prepare("SELECT id_produit FROM sae3_skadjam._produit WHERE ");
            // $recup_id_produit -> execute();
        }
        catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'un produit</title>
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="../../css/bo/general_back.css">
    <style>
        button a:hover{
            color : black;
        }
    </style>
</head>
<body>
    <?php include('../../php/structure/header_back.php');?>
    <?php include('../../php/structure/navbar_back.php');?>
    <main>
        <h2>Création d'un produit</h2>
        <form class="grid grid-cols-[40%_60%] w-11/12 self-center" action="creation_produit.php" method="post">

            <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                <input type="file" id="photo" name="photo" class="hidden" required>
                <!-- label qui agit comme bouton -->
                <label for="photo" class="bg-beige w-60 h-60 rounded-xl" style="background-image: url('../../images/logo/bootstrap_icon/image.svg'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                <label for="photo">Ajouter une image</label>
            </div>
            

            <div class="col-start-2 row-start-1 flex flex-col w-200 m-2 p-2">
                <label for="nom">Nom produit *:</label>
                <input class=" border-4 border-beige rounded-2xl" type="text" name="nom" id="nom" required>
            </div>

            <div class="col-start-2 row-start-2 flex flex-row justify-between w-200 m-2 p-2">
                <div class="flex flex-col">
                    <label for="prix">Prix *(hors taxe):</label>
                    <input class="border-4 border-beige rounded-2xl w-75" type="number" name="prix" id="prix" min="0.0" step="0.5" required>
                </div>
                <div class="flex flex-col">
                    <label for="qteStock">Quantité en stock* :</label>
                    <input class="border-4 border-beige rounded-2xl w-75" type="number" name="qteStock" id="qteStock" min="0" required>
                </div>
            </div>
                
            <div class="col-start-2 row-start-3 col-span-2 flex flex-row justify-between w-200 m-2 p-2">
                <div class="flex flex-col">
                    <label for="categorie">Catégorie* :</label>
                    <select class=" border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14" name="categorie" id="categorie" required>
                        <option value="0">Choisir</option>
                        <?php foreach ($tab_categories as $categorie) {?>
                            <option value="<?php echo $categorie['id_categorie']?>"><?php echo $categorie['libelle_categorie']?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="flex flex-col">
                    <label for="unite">Unité* :</label>
                    <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14" name="unite" id="unite" required>
                    <option value="0">Choisir</option>
                    <?php foreach ($tab_unite as $unite) {?>
                        <option value="<?php echo $unite?>"><?php echo $unite?></option>
                    <?php } ?>
                </select>
                </div>
                <div class="flex flex-col">
                    <label for="qteUnite">Quantité unité :</label>
                    <input class="border-4 border-beige rounded-2xl w-75" type="number" name="qteUnite" id="qteUnite" min="0" required>
                </div>
            </div>

            
            <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                <div class="flex flex-row mr-4 ml-4">
                    <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                    <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnLigne" id="mettreEnLigne">
                </div>
            
                <div class="flex flex-row mr-4 ml-4">
                    <label class="mr-4" for="mettreEnPromotion">Mettre en promotion</label>
                    <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">
                </div>
            </div>
            
            <div class="col-start-1 col-span-2 row-start-5 flex flex-col m-2 p-2 ">
                <label for="description">Description *:</label>
                <textarea class="border-4 border-beige rounded-2xl w-3/4 self-center" name="description" id="description" cols="100" rows="10" required></textarea>
            </div>
            
            <div class="col-start-1 col-span-2 row-start-6 flex flex-row justify-around m-4">
                <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="../bo/index_vendeur.php">Retour</a></button>
                <input class="border-2 border-vertFonce rounded-2xl w-40 h-14" type="submit" value="Valider" href="../bo/details_produit.php">
            </div>
        </form>
    </main>
    <?php include('../../php/structure/footer_back.php');?>
</body>
</html>


