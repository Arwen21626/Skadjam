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
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required>
        <label for="mail">Mail :</label>
        <input type="email" id="mail" name="mail" required>
        <label for="tel">Numéro de téléphone :</label>
        <input type="tel" id="tel" name="tel" required>

        <h3>Informations entreprise :</h3>
        <label for="denomination">Nom de l'entreprise :</label>
        <input type="text" id="denomination" name="denomination" required>
        <label for="adresse">Adresse du siège social :</label>
        <input type="text" id="adresse" name="adresse" required>
        <label for="siren">Numéro de SIREN :</label>
        <input type="text" id="siren" name="siren" required>

        <h3>Mot de passe :</h3>
        <label for="mdp">Mot de passe :</label>
        <input type="password" id="mdp" name="mdp" required>
        <label for="verif">Vérification du mot de passe :</label>
        <input type="password" id="verif" name="verif" required>

        <label for="cgu">J'ai lu et j'acccepte les conditions générales d'utilisation :</label>
        <input type="checkbox" required>

        <input type="hiden" value="Annuler">
        <input type="submit" value="Valider">
    </form>
</body>
</html>