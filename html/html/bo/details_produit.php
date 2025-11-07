<?php 
include('../../connections_params.php');
include('../../01_premiere_connexion.php');
//Récupération des données sur le produit
$tab_produit = [];

foreach($dbh->query('SELECT * from sae3_skadjam._produit', 
                        PDO::FETCH_ASSOC) 
                as $row) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
        
        $tab_produit[] = $row;
    }
print_r($tab_produit);


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bo/general_back.css">
    <title>Détails</title>
</head>
<body>
    <?php include('../../php/header_back.php');?>
    <main>
        <h2><?php echo($tab_produit['libelle_produit']); ?></h2>
        <p>NB étoiles à récupérer</p>
        <p><?php echo($tab_produit['id_categorie']); ?></p>
        <p>carroussel d'image</p>
        <p> <?php echo($tab_produit['prix_ttc']); ?></p>
        <p><?php echo($tab_produit['quantite_stock']); ?></p>
        <button type="button">Modifier</button>
        <button type="button">Masquer</button>
        <button type="button">Extraire</button>

        <h2>Description détaillée</h2>

        <p>Description à récupérer</p>

        <p>Partie sur les avis à voir plus tard</p>

    </main>
    <?php include('../../php/footer_back.php');?>
</body>
</html>
