let dataStats = dataJson;

let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
let dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

let currentAnnee = '2026';

console.log(Object.keys(dataStats[currentAnnee]))

Object.keys(dataStats).forEach(mois => {
    let i = Number(mois);

    dataVentes[i - 1] = Number(dataStats[mois]["nb_ventes_totales"]);
});

let testChart = document.getElementById("testChart");

let testChartCfg = {
    type: 'line',
    data: {
        labels: periodes,
        datasets: [{
            label: "Ventes totales durant le mois",
            data: dataVentes,
            borderWidth: 3,
            pointBorderWidth: 8,
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

