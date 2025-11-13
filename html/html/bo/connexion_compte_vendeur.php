<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . "/../../php/structure/head_back.php"?>
<head>
    
    <title>connexion vendeur</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_back.php"; ?>
    <main>
        <h2>Connexion</h2>
        <form method="POST">
            <div class="flex flex-col justify-items-center ml-10 mb-7 mr-10">
                <div class="flex flex-col mt-6 items-start">
                    <label for="adresse">Adresse mail :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3" type="text" name="adresse" id="adresse" required>
                </div>
                <div class="flex flex-col mt-6 items-start">
                    <label for="mdp">Mot de passe :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3" type="password" name="mdp" id="mdp" required>
                    <a href=""></a>
                    <p class="underline">mot de passe oublié ?</p>
                </div class="flex flex-col mt-6 items-start">
                <div class="buttons-form">
                    <input type="submit" value="Se connecter">
                </div>
                <p>Pas encore vendeur ? </p>
                <a href="crea_compte_vendeur.php">Créer un compte</a>
            </div>
        </form>
    </main>
</body>
</html>