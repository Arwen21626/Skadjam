<?php 
    session_start();
    require_once __DIR__ . '/../../php/verif_role_bo.php';

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
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[545px]">
        <div class="mt-30">
            <h2 class="text-center">Voulez-vous vraiment vider le catalogue ?</h2>
        </div>
        <div class="mt-30 flex flex-row justify-around mb-15">
            <button id="executerViderCatalogue" class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Oui</button>
            <a href="../bo/index_vendeur.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Non</button></a>
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

    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>

</html>
    
