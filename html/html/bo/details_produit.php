<?php 
include('../../connections_params.php');
//Récupération des données sur le produit

// $dbh = new PDO("$driver:host=$server;dbname=$dbname");
// echo "Avant select";
// $recuperation = $dbh->query("SELECT * FROM sae3_skadjam._produit", PDO::FETCH_ASSOC);
// $recuperation -> execute();
// $dbh = null;
// echo "Apres select";


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails</title>
</head>
<body>
    <header></header>
    <main>
        <h2>Nom produit à récupérer</h2>
        <p>NB étoiles à récupérer</p>
        <p>Catégorie : à récupérer</p>
        <p>carroussel d'image</p>
        <p>Prix à récupérer</p>
        <p>En stock : à récupérer</p>
        <button type="button">Modifier</button>
        <button type="button">Masquer</button>
        <button type="button">Extraire</button>

        <h2>Description détaillée</h2>

        <p>Description à récupérer</p>

        <p>Partie sur les avis à voir plus tard</p>

    </main>
    <footer></footer>
</body>
</html>
