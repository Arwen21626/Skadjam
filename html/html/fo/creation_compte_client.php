<!DOCTYPE html>
<html lang="fr">
<?php include ("../../php/structure/head_front.php");?>
<head>
    <title>Création d'un compte client</title>
</head>

<body>
    <?php
    // Import du header
    include ("../../php/structure/header_front.php");     
    ?>

    <h2>Création du compte client</h2>

    <form action="../../php/traitement_donnees_compte_client.php" method="post"> 
        <label for="nom">Nom* :</label>
        <input type="text" name="nom" id="nom" required>

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="naissance">Date de naissance* :</label>
        <input type="date" name="naissance" id="naissance" required> 

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" required>

        <label for="mdp">Mot de passe* :</label>
        <input type="password" name="mdp" id="mdp" required>
        <p> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>

        <label for="verifMdp">Vérification du mot de passe* :</label>
        <input type="password" name="verifMdp" id="verifMdp" required>

        <p>Vous avez déjà un compte ?</p>
        <a href='se_connecter_compte_client.php'>Connectez vous</a>   <!-- lien à revoir en fonction du nom du fichier qui permet de se connecter -->

		<button type="button"><a href="index.php">Anuler</a></button>
        <input type="Submit" name="submit" id="submit" value="S'inscrire">
    </form>

    <?php 
    // Import du footer
    include ("../../php/structure/footer_front.php");
    ?>

</body>
</html>
