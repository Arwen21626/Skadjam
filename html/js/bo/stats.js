let testChart = document.getElementById("testChart");

let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

let testChartCfg = {
    type: 'line',
    data: {
        labels: periodes,
        datasets: [{
            label: "Ventes totales durant le mois",
            data: [110, 52, 63, 90, 30, 230, 400, 142, 60, 12, 147, 85],
            borderWidth: 2,
            barThickness: "flex",
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Total des ventes pour l\'année actuelle',
            }
        }
    }
}

new Chart(testChart, testChartCfg);