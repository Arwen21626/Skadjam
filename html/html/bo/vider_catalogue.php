<?php 
    session_start();

    include __DIR__ .'/../../01_premiere_connexion.php';
    const PAGE_SIZE = 15;
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<?php require(__DIR__ . "/../../php/structure/head_back.php") ?>

<head>
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

    <main class="min-h-[545px]">
        <h2 class="mt-15">Voulez-vous vraiment vider le catalogue ?</h2>
        <div class="mt-15 flex flex-row justify-around mb-15">
            <button id="executerViderCatalogue" class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7">Oui</button>
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7"><a href="../bo/index_vendeur.php">Non</a></button>
        </div>

        <script>
            document.getElementById('executerViderCatalogue').addEventListener('click', () => {
                fetch('../../php/executer_vider_catalogue.php')
                .then(res => res.text())
                .then(data => {
                    window.location.href = "index_vendeur.php";
                });
        });
        </script>

    </main>

    <?php include("../../php/structure/footer_back.php"); ?>

</body>

</html>
    
