<?php 
    session_start();
    include __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ . "/../../01_premiere_connexion.php";

    print_r($_SESSION);

    $idVendeur = $_SESSION["idCompte"];

    echo $idVendeur;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats</title>
</head>

<?php include __DIR__ . "/../../php/structure/head_back.php"; ?>

<body>
    <?php 
        require_once __DIR__ . "/../../php/structure/header_back.php";
        require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>

    <main class="m-4">

        <h2>Mes Statistiques</h2>

        <div class="charts-containers flex justify-center items-center flex-col p-2">
            <div class="chart-container flex justify-center items-center relative m-4 w-[60vw] h-[50vh]">
                <canvas class="" id="testChart"></canvas>
            </div>
        </div>
    </main>
    
    <?php 
        require_once __DIR__ . "/../../php/structure/footer_back.php";
    ?>
</body>

<script src="/js/chart.umd.js"></script>
<script type="module" src="/js/bo/stats.js"></script>

</html>