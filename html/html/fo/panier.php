<?php 
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include("php/structure/head_front.php");?>
    <title>Panier</title>
</head>
<body>
    <?php include("php/structure/header_front.php") ?>
    <?php include("php/structure/navbar_front.php") ?>

    <main class="min-h-[480px] p-4 flex justify-center">
        <h2 class="text-center self-center">Votre panier est vide</h2>
    </main>

    <?php include("php/structure/footer_front.php") ?>
</body>
</html>