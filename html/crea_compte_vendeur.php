<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte vendeur</title>
</head>
<body>
    <h2>Inscription Vendeur</h2>
    <form action="creer_vendeur.php" method="POST">
        <h3>Informations vendeur :</h3>
        <label for="nom">Nom * :</label><br>
        <input type="text" id="nom" name="nom" required>
        <label for="prenom">Prénom * :</label><br>
        <input type="text" id="prenom" name="prenom" required>
        <label for="mail">Mail * :</label><br>
        <input type="email" id="mail" name="mail" required>
        <label for="tel">Numéro de téléphone * :</label><br>
        <input type="tel" id="tel" name="tel" required>

        <h3>Informations entreprise :</h3>
        <label for="denomination">Nom de l'entreprise * :</label><br>
        <input type="text" id="denomination" name="denomination" required>
        <label for="adresse">Adresse du siège social * :</label><br>
        <input type="text" id="adresse" name="adresse" required>
        <label for="siren">Numéro de SIREN * :</label><br>
        <input type="text" id="siren" name="siren" required>

        <h3>Mot de passe :</h3>
        <label for="mdp">Mot de passe * :</label><br>
        <input type="password" id="mdp" name="mdp" required>
        <p>1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
        <label for="verif">Vérification du mot de passe * :</label><br>
        <input type="password" id="verif" name="verif" required>

        <label for="cgu">J'ai lu et j'acccepte les conditions générales d'utilisation :</label>
        <input type="checkbox" required>

        <input type="reset" value="Annuler">
        <input type="submit" value="Valider">
    </form>
    <p>Vous avez déjà un compte ?</p>
    <a href="connexion_vendeur">Connectez vous</a>
</body>
</html>