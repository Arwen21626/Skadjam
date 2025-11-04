<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Création d'un produit</title>
</head>
<body>
    <header></header>
    <main>
        <h2>Création d'un produit</h2>
        <form action="" method="post">
            <select name="categorie" id="categorie" required>Categories</select>
            <input type="file" name="photo" id="photo" required>
            <label for="nom">Nom produit *:</label>
            <input type="text" name="nom" id="nom">

            <label for="nom">Prix *:</label>
            <input type="text" name="prix" id="prix">

            <label for="nom">Remise :</label>
            <input type="text" name="remise" id="remise">

            <label for="">Quantité en stock :</label>
            <input type="button" value="-">
            <label for=""></label>
            <input type="button" value="+">

            <label for="appliquerRemise">Appliquer la remise</label>
            <input type="checkbox" name="appliquerRemise" id="appliquerRemise">

            <label for="mettreEnLigne">Mettre en ligne</label>
            <input type="checkbox" name="mettreEnLigne" id="mettreEnLigne">

            <label for="mettreEnPromotion">Mettre en promotion</label>
            <input type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">

            <label for="tags">Tags *:</label>

            <label for="nom">Description *:</label>
            <input type="text" name="remise" id="remise">
            
            <input type="button" value="Retour" href="">
            <input type="submit" value="Valider">
        </form>
    </main>
    <footer></footer>
</body>
</html>