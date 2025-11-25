<?php
    include(__DIR__ . '/../../01_premiere_connexion.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css" >
    <title>Commandes</title>
</head>
<body>
    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="min-h-[600px]">
        <h2>Bientôt disponible ...</h2>
        <div class="flex justify-center mt-15 mb-18">
            <a href="../../index.php"><button class="bg-beige shadow rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        </div>
        <!--
        <button class="-indent-96 whitespace-nowrap
                    size-12 md:size-auto md:bg-none md:indent-0 overflow-visible md:whitespace-normal
                    bg-beige shadow rounded-2xl w-20 h-7 md:w-40 md:h-14 mt-4">
        <a href="../fo/index.php">Retour</a></button>-->
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>