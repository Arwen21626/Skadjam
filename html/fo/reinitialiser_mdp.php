<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser mon mot de passe</title>
</head>
<body>
    <h2>Mot de passe oublié</h2>
    <form action="attendre_mail.php" method="post">
        <label>Adresse mail :</label>
        <br>
        <input type="email" name="mail" id="mail" required>
        <br>
        <br>
        <p>Si un compte à cette adresse existe, vous recevrez un mail contenant un lien pour la réinitialisation.</p>
        <br>
        <br>
        <input type="submit" value="Recevoir un mail">
    </form>
</body>
</html>
