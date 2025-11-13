<?php
session_start();
require_once "../php/verification_formulaire.php";
require_once "../connections_params.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/fo/general_front.css">
    <title>Nouveau mot de passe</title>
</head>
<body>
    <?php
    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Traitement du formulaire seulement si toutes les données son saisie
        if(isset($_POST['mdp']) && isset($_POST['verifMdp'])){
            // Vérification que toutes les données saisie son correcte
            if(verifMotDePasse($_POST['mdp']) && ($_POST['mdp'] === $_POST['verifMdp'])){
                echo "Congrats";
                $mdp = $_POST['mdp'];  // à Hasher
                // Redirection vers la page de connexion
                
            }else{ 
                echo "Erreur";
            }
        }else{ ?>
        <h2>Création du compte client</h2>

        <form action="nouveau_mdp.php" method="post"> 
            <label for="mdp">Mot de passe* :</label>
            <input type="password" name="mdp" id="mdp" required>
            <ul>
                <li>1 majuscule,</li>
                <li>1 minuscule,</li>
                <li>1 chiffre,</li>
                <li>1 caractère spécial,</li>
                <li>10 caractère minimum</li>
            </ul>

            <label for="verifMdp">Vérification du mot de passe* :</label>
            <input type="password" name="verifMdp" id="verifMdp" required>


            <input type="submit" value="Confirmer">
        </form>
        <?php }
    }catch(PDOException $e){
        print "Erreur : " . $e->getMessage() . "<br/>";
    } ?>
</body>
</html>
