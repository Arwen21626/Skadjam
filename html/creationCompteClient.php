<?php
require_once("../php/verificationFormulaire.php"); // fonctions qui vérifient les données des formulaires
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); // connextion à la base de données
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php

    // Traitement du formulaire seulement si toutes les données son saisie
    if(isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['telephone']) && isset($_POST['mdp']) && isset($_POST['verifMdp'])){
        // Vérification que toutes les données saisie son correcte
        if (verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['telephone']) && verifMail($_POST['mail']) && verifMotDePasse($_POST['mdp']) && ($_POST['mdp'] === $_POST['verifMdp'])){
            echo 'Salut !';
            // Traitement des données pour celle qui en on besoin
            $nom = strlower($_POST['nom']);
            $prenom = strlower($_POST['prenom']);
            $mail = strlower($_POST['mail']);
            // $mdp = ;  // à Hasher

            // Insertion des données dans la base de données
            $insertNouvDept = $dbh->prepare("INSERT INTO teste_sae3._compte(id_compte, nomdept, lieu) 
                                                VALUES('50', 'DEPARTEMENT1','Lannion')"); // lancer une requête sql
            $insertNouvDept->execute();

            // Redirection vers la page d'accueil

        }
        // Messages d'erreurs si l'un des champs ne correspond pas à ce qu'on attend
        else{ 
            echo "erreur";
        }
    }

    // Formulaire
    else{
        
    ?>

    <h2>Création du compte client</h2>

    <form action="creationCompteClient.php" method="post"> 
        <label for="nom">Nom* :</label>
        <input type="text" name="nom" id="nom" required>

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="+33604030201" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" required>

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

        <p>Vous avez déjà un compte ?</p>
        <a href='seConnecterCompteClient.php'>Connectez vous</a>   <!-- lien à revoir en fonction du nom du fichier qui permet de se connecter -->

<!--bouton annuler-->
        <input type="Submit" name="submit" id="submit" value="S'inscrire">
    </form>
    <?php }?>

</body>
</html>