<?php
    session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php require(__DIR__ . "/php/structure/head_front.php"); ?>
    <title>404</title>
</head>
<body>
    <?php require(__DIR__ . "/php/structure/header_front.php"); ?>
    <?php require(__DIR__ . "/php/structure/navbar_front.php"); ?>

    <main class="flex flex-col justify-center p-4 min-h-[545px]">
        <h2 class="text-center self-center">ERREUR 404</h2>
        <p class="text-center self-center">La page n'a pas été trouvé</p>

    </main>
    

    <?php require(__DIR__ . "/php/structure/footer_front.php") ?>
</body>
</html>