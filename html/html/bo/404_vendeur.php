<?php
    session_start();
    include(__DIR__ . '/../../php/verif_role_bo.php');
?>

<!DOCTYPE html>
<html lang="fr">

<?php require(__DIR__ . "/../../php/structure/head_back.php"); ?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>404</title>
</head>
<body>
    <?php require(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php require(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="flex flex-col justify-center p-4 min-h-[545px]">
        <h2 class="text-center self-center">ERREUR 404</h2>
        <p class="text-center self-center">La page n'a pas été trouvé.</p>

        <div class="flex justify-center mt-16">
            <a class="inline-block justify-center" href="/html/bo/index_vendeur.php"> 
                <h3 class="underline! inline-block text-center self-center hover:text-rouge ">Retour à l'accueil</h3>
            </a>
        </div>
        
    </main>
    

    <?php require(__DIR__ . "/../../php/structure/footer_back.php") ?>
</body>
</html>