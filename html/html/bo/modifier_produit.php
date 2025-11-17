<?php 
include( __DIR__ . '/../../01_premiere_connexion.php');
require_once(__DIR__ . '/../../php/verification_formulaire.php');


$idProduit = $_GET['idProduit'];
$idProduit = 1;

foreach($dbh->query("SELECT * FROM sae3_skadjam._produit pr
                        INNER JOIN sae3_skadjam._categorie c
                            ON pr.id_categorie = c.id_categorie
                            /*Inner join promotion ?*/
                        INNER JOIN sae3_skadjam._montre m
                            ON m.id_produit = pr.id_produit
                        INNER JOIN sae3_skadjam._photo ph
                            ON m.id_photo = ph.id_photo
                            WHERE id_produit = $idProduit") as $produit){
    $nom = $produit['libelle_produit'];
    $description = $produit['description_produit'];
    $prixHT = $produit['prix_ht'];
    $enLigne = $produit['est_masque']; //A revoir
    $qteStock = $produit['quantite_stock'];
    $qteUnite = $produit['quantite_unite'];
    $unite = $produit['unite'];
    $nomCategorie = $produit['libelle_categorie'];
    $idCategorie = $produit['id_categorie'];
    $idPhoto = $produit['id_photo'];
    $urlPhoto = $produit['url_photo'];
    $altPhoto = $produit['alt'];
    $titrePhoto = $produit['titre'];
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="../../css/bo/general_back.css">
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
        <form class="grid grid-cols-[40%_60%] w-11/12 self-center" action="creation_produit.php" method="post">

            <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                <input type="file" id="photo" name="photo" class="hidden" required>
                <!-- label qui agit comme bouton -->
                <label for="photo" class="bg-beige w-60 h-60 rounded-xl" style="background-image: url('../../images/'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
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
                    <input value="<?php echo($qteUnite) ;?>" class="border-4 border-beige rounded-2xl w-75" type="number" name="qteUnite" id="qteUnite" min="0" required>
                </div>
            </div>

            
            <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                <div class="flex flex-row mr-4 ml-4">
                    <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                    <input value="<?php echo($enLigne) ;?>" class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige" type="checkbox" name="mettreEnLigne" id="mettreEnLigne">
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
                <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="../bo/index_vendeur.php">Retour</a></button>
                <input class="border-2 border-vertFonce rounded-2xl w-40 h-14" type="submit" value="Valider" href="../bo/details_produit.php?idProduit=<?php echo $idProd ;?>">
            </div>
        </form>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
</body>
</html>
