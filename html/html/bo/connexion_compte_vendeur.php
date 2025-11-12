<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/html/css/bo/general_back.css">
    <link rel="stylesheet" href="/html/css/style_form_creation_vendeur.css">
    <title>connexion vendeur</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_back.php" ?>
    <main>
        <form method="POST">
            <div class="zone_form">
                <div class="case_form">
                    <label for="adresse">Adresse mail :</label>
                    <input type="text" name="adresse" id="adresse" required>
                </div>
                <div class="case_form">
                    <label for="mdp">Mot de passe :</label>
                    <input type="password" name="mdp" id="mdp" required>
                    <p class="underline">mot de passe oublié ?</p>
                </div class="case_form">
                <div class="buttons-form">
                    <input type="submit" value="Se connecter">
                </div>
            </div>
        </form>
    </main>
</body>
</html>