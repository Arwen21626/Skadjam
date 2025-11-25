<?php 
    session_start();

    include __DIR__ .'/../../01_premiere_connexion.php';
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . "/../../php/structure/head_back.php"; ?>
<head>
    <title>Commandes</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[600px]">
        <h2>Bientôt disponible ...</h2>
        <div class="flex justify-center mt-15 mb-15">
            <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-50 h-14 px-7"><a href="../bo/index_vendeur.php">Retour</a></button>
        </div>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>