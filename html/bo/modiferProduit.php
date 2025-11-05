<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un produit</title>
</head>
<body>
    <header></header>
    <main>
        <h2>Modifier le produit</h2>
        <form action="" method="post">
            <select name="categorie" id="categorie" required>
                <option value="0">Categorie</option>
                <option value="id dans la bdd">Boucle php sur la bdd</option>
            </select>
            
            <input type="file" name="photo" id="photo" required>
            <label for="photo">Modifier les images</label>

            <label for="nom">Nom produit *:</label>
            <input type="text" name="nom" id="nom" required>

            <label for="prix">Prix *:</label>
            <input type="number" name="prix" id="prix" min="0.0" step="0.5" required>

            <label for="qteStock">Quantité en stock :</label>
            <input type="number" name="qteStock" id="qteStock" min="0" required>

            <label for="mettreEnLigne">Mettre en ligne</label>
            <input type="checkbox" name="mettreEnLigne" id="mettreEnLigne">

            <label for="mettreEnPromotion">Mettre en promotion</label>
            <input type="checkbox" name="mettreEnPromotion" id="mettreEnPromotion">


            <label for="nom">Description *:</label>
            <input type="text" name="description" id="description" required>
            
            <input type="button" value="Retour" href="">
            <input type="submit" value="Valider">
        </form>
    </main>
    <footer></footer>
</body>
</html>