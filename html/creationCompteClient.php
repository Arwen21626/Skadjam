<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h2>Création du compte client</h2>

    <form action="creationCompteClient.php" method="post"> 
        <label for="nom">Nom* :</label>
        <input type="text" name="nom" id="nom">

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom">

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone">

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail">

        <label for="mdp">Mot de passe* :</label>
        <input type="password" name="mdp" id="mdp">
        <ul>
            <li>1 majuscule,</li>
            <li>1 minuscule,</li>
            <li>1 chiffre,</li>
            <li>1 caractère spécial,</li>
            <li>10 caractère minimum</li>
        </ul>

        <label for="verifMdp">Vérification du mot de passe* :</label>
        <input type="password" name="verifMdp" id="verifMdp">

        <p>Vous avez déjà un compte ?</p>
        <a href='seConnecterCompteClient.php'>Connectez vous</a>   <!-- lien à revoir en fonction du nom du fichier qui permet de se connecter -->

<!--bouton annuler-->
        <input type="Submit" name="submit" id="submit">
    </form>

</body>
</html>