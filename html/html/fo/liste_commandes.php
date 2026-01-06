<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commandes</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px]">
        <h2>Bientôt disponible ...</h2>
        <div class="flex justify-center mt-15 mb-18">
            <a href="../../index.php"><button class="bg-beige shadow rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        </div>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>