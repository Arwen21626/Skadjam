<?php 
    session_start();

    include __DIR__ .'/../../01_premiere_connexion.php';
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Commandes</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main>
        <h2>Bientôt disponible ...</h2>
        <div class="flex justify-center mt-15 mb-15">
            <button class="border-2 border-vertFonce rounded-2xl w-50 h-14 px-7"><a href="../bo/index_vendeur.php">Retour</a></button>
        </div>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>