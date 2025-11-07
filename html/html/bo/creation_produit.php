<?php
include('../../connections_params.php');
include('../../01_premiere_connexion.php');
require_once('../../php/verification_formulaire.php');



if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description'])) {
    $categorie = htmlentities($_POST['categorie']);
    $nom = htmlentities($_POST['nom']);
    $prix = htmlentities($_POST['prix']);
    $qteStock = htmlentities($_POST['qteStock']);
    $enLigne = htmlentities($_POST['mettreEnLigne']);
    $enPromotion = htmlentities($_POST['mettreEnPromotion']);
    $description = htmlentities($_POST['description']);

    //OUBLIE PAS LA PHOTO
    //Il faut récupérer l'id du vendeur pour l'insertion
    if (verifiePrix($prix) && verifieQteStock($qteStock)) {
        try{
            $prix_ttc = $prix*1.2;
            echo "Avant l'insertion";
            $insertion_produit = $dbh -> prepare("INSERT INTO sae3_skadjam._produit (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, seuil_alerte, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
            VALUES ('$nom','$description', $prix, $prix_ttc, false, $qteStock, 0, 1,'kg',1, 1, 1)");
            echo "Après l'insertion 1";
            $insertion_produit -> execute();
            $dbh = null;
            echo "Après l'insertion 2";
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
</head>
<body>
    <header></header>
    <main>
        <h2>Création d'un produit</h2>
        <form action="creation_produit.php" method="post">
            <select name="categorie" id="categorie" required>
                <option value="0">Categorie</option>
                <option value="id dans la bdd">Boucle php sur la bdd</option>
            </select>
            
            <!-- <input type="file" name="photo" id="photo" required>
            <label for="photo">Ajouter des images</label> -->

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


