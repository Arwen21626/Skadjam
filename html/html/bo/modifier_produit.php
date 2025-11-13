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
        <form action="" method="post">
            <select class="p-1 bg-beige rounded" name="categorie" id="categorie" required>
                <option value="0">Categorie</option>
                <option value="id dans la bdd">Boucle php sur la bdd</option>
            </select>
            
            <input type="file" name="photo" id="photo" required>
            <label for="photo">Modifier les images</label>

            <label for="nom">Nom produit *:</label>
            <input class="border-4 border-beige rounded-2xl" type="text" name="nom" id="nom" required>

            <label for="prix">Prix *:</label>
            <input class="border-4 border-beige rounded-2xl" type="number" name="prix" id="prix" min="0.0" step="0.5" required>

            <label for="qteStock">Quantité en stock :</label>
            <input class="border-4 border-beige rounded-2xl" type="number" name="qteStock" id="qteStock" min="0" required>

            <label for="mettreEnLigne">Mettre en ligne</label>
            <input type="checkbox" name="mettreEnLigne" id="mettreEnLigne">

            <label for="mettreEnPromotion">Mettre en promotion</label>
            <input type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">


            <label for="nom">Description *:</label>
            <input class="border-4 border-beige rounded-2xl" type="text" name="description" id="description" required>
            
            <input type="button" value="Retour" href="">
            <input type="submit" value="Valider">
        </form>
    </main>
    <?php include('../../php/structure/footer_back.php');?>
</body>
</html>
