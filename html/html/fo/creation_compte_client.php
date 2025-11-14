<!DOCTYPE html>
<html lang="fr">
<?php include (__DIR__."/../../php/structure/head_front.php");?>
<head>
    <title>Création d'un compte client</title>
    <style>
        button a:hover {
            color: #000; 
        }
    </style>
</head>

<body>
    <?php
    // Import du header
    include (__DIR__."/../../php/structure/header_front.php");  
    ?>
    
    <main style="margin: 0" class="flex flex-col justify-center">
        <?php
        // Import de la bar de navigation
        include (__DIR__."/../../php/structure/navbar_front.php");    
        ?>

        <h2 class="flex justify-center text-center">Création du compte client</h2>

        <form class="flex flex-wrap p-15 pt-0 justify-around" action="../../php/traitement_donnees_compte_client.php" method="post"> 
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="nom">Nom* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="nom" id="nom" required>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="prenom">Prenom* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="prenom" id="prenom" required>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="pseudo">Pseudo* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="pseudo" id="pseudo" required>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="naissance">Date de naissance* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="date" name="naissance" id="naissance" required> 
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="telephone">Telephone* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" required>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mail">Adresse email* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="mail" name="mail" id="mail" required>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mdp">Mot de passe* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="password" name="mdp" id="mdp" required>
                <p style="font-size: 0.90em"> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
            </div>

            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="verifMdp">Vérification du mot de passe* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="password" name="verifMdp" id="verifMdp" required>
            </div>

            <div class="flex mt-10 flex-col w-1/1 items-start">
                <p>Vous avez déjà un compte ?</p>
                <a style="text-decoration-line: underline" href='connexion_client.php'>Connectez vous</a> 
            </div>
            
            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10 " type="button"><a href="index.php">Annuler</a></button>
                <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="S'inscrire">
            </div>
        </form>
    </main>

    <?php 
    // Import du footer
    include (__DIR__."/../../php/structure/footer_front.php");
    ?>

</body>
</html>
