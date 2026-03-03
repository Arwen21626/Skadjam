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
        <h1 class="text-center">Test</h1>
    </header>

    <div class="charts-containers flex justify-center items-center flex-col p-2">
        <div class="chart-container flex justify-center items-center relative m-4 w-[90vw] h-[40vh] md:w-[60vw] md:h-[50vh]">
            <canvas class="" id="testChart"></canvas>
        </div>
    </div>
    
</body>

<script src="/js/chart.umd.js"></script>
<script type="module" src="test.js"></script>

</html>