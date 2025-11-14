<?php 
    session_start();
    //à retirer
    $_SESSION['idCompte'] = 1;

    include __DIR__ .'/../../01_premiere_connexion.php';
    const PAGE_SIZE = 15;
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Vider le catalogue</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>

<body>
    <!--header-->
    <?php include("../../php/structure/header_back.php"); ?>
    <?php include("../../php/structure/navbar_back.php"); ?>

    <main>
        <h2>Voulez-vous vraiment vider le catalogue ?</h2>
        <div class="mt-15 flex flex-row justify-around">
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7">Oui</button>
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7"><a href="../bo/index_vendeur.php">Non</a></button>
        </div>
    </main>

    <?php include("../../php/structure/footer_back.php"); ?>

</body>

</html>
    
