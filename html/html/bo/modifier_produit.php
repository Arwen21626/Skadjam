<?php 
include('../../../connections_params.php');
include('../../01_premiere_connexion.php');
require_once('../../php/verification_formulaire.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
    <link rel="stylesheet" href="../../css/output.css">
    <link rel="stylesheet" href="../../css/bo/general_back.css">
</head>
<body>
    <?php include('../../php/structure/header_back.php');?>
    <?php include('../../php/structure/navbar_back.php');?>
    <main>
        <h2>Modifier le produit</h2>
        <p>ID : à recupérer</p>
        <form class="grid grid-cols-[30/100-70/100] grid-rows-[10/100-15/100-15/100-15/100-auto]  w-11/12 self-center" action="creation_produit.php" method="post">
            <select class="col-end-1 row-end-1 bg-beige rounded m-2 p-2 w-40" name="categorie" id="categorie" required>
                <option value="0">Categorie</option>
                <?php foreach ($tab_categories as $categorie) {?>
                    <option value="<?php echo $categorie['id_categorie']?>"><?php echo $categorie['libelle_categorie']?></option>
                <?php } ?>
                
            </select>

            <div class="row-start-1 row-span-3 m-2 p-2 grid grid-rows-[2/3-1/3] items-center">
                <input type="file" id="photo" name="photo" class="hidden" required>
                <!-- label qui agit comme bouton -->
                <label for="photo" class="bg-beige w-60 h-60 rounded-xl" style="background-image: url('../../images/logo/bootstrap_icon/image.svg'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                <p>Ajouter une image</p>

            </div>
            

            <div class="col-start-1 row-start-1 flex flex-col w-200 m-2 p-2">
                <label for="nom">Nom produit *:</label>
                <input class=" border-4 border-beige rounded-2xl" type="text" name="nom" id="nom" required>
            </div>

            <div class="col-start-1 row-start-2 flex flex-row justify-between w-200 m-2 p-2">
                <div class="flex flex-col">
                    <label for="prix">Prix *:</label>
                    <input class="border-4 border-beige rounded-2xl w-75" type="number" name="prix" id="prix" min="0.0" step="0.5" required>
                </div>
                <div class="flex flex-col">
                    <label for="qteStock">Quantité en stock :</label>
                    <input class="border-4 border-beige rounded-2xl w-75" type="number" name="qteStock" id="qteStock" min="0" required>
                </div>
            </div>

            <div class="row-start-3 col-span-2 flex flex-row justify-around m-2 p-2">
                <div>
                    <label for="mettreEnLigne">Mettre en ligne</label>
                    <input type="checkbox" name="mettreEnLigne" id="mettreEnLigne">
                </div>
            
                <div>
                    <label for="mettreEnPromotion">Mettre en promotion</label>
                    <input type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">
                </div>
            </div>
            
            <div class="row-start-4 col-span-2 flex flex-col m-2 p-2 ">
                <label for="nom">Description *:</label>
                <textarea class="border-4 border-beige rounded-2xl w-3/4 self-center" name="description" id="description" cols="100" rows="10" required></textarea>
            </div>
            
            <div class="row-start-5 col-span-2 flex flex-row justify-around m-4">
                <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="../bo/index_vendeur.php">Retour</a></button>
                <input class="border-2 border-vertFonce rounded-2xl w-40 h-14" type="submit" value="Valider" href="../bo/details_produit.php">
            </div>
        </form>
    </main>
    <?php include('../../php/structure/footer_back.php');?>
</body>
</html>
