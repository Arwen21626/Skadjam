let testChart = document.getElementById("testChart");

let testChartCfg = {
    type: 'bar',
    data: {
        labels: ['Janvier', 'Février', 'Mars', 'Avril'],
        datasets: [{
            label: "Ventes totaux",
            data: [110, 52, 63, 90],
            borderWidth: 2,
            barThickness: 100,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: 'Mon graphique',
            }
        }
    }
}

new Chart(testChart, testChartCfg);