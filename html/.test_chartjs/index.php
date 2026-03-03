<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Chart.js</title>
</head>

<?php include __DIR__ . "/../php/structure/head_front.php"; ?>

<body>
    <header class="flex justify-center items-center">
        <h1 class="text-center bg-pink-950">Test</h1>
    </header>

    <div class="chart-container relative h-[40vh] w-[80vw]">
        <canvas class="" id="testChart"></canvas>
    </div>
</body>

<script src="/js/chart.umd.js"></script>
<script type="module" src="test.js"></script>

</html>