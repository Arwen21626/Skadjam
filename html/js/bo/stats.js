let dataStats = dataJson;

let anneeSelection = document.getElementById("select-annee");

let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
let dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

let currentAnnee = anneeSelection.value;

Object.keys(dataStats[currentAnnee]).forEach(mois => {
    let i = Number(mois);

    dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]);
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
                text: 'Total des ventes pour l\'année' + " " + currentAnnee,
            }
        }
    }
}

let chart = new Chart(testChart, testChartCfg);

anneeSelection.addEventListener("change", function () {
    currentAnnee = anneeSelection.value;
    dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

    Object.keys(dataStats[currentAnnee]).forEach(mois => {
        let i = Number(mois);

        dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]);
    });

    testChartCfg.options.plugins.title.text = 'Total des ventes pour l\'année' + " " + currentAnnee;
    testChartCfg.data.datasets[0].data = dataVentes;

    chart.destroy();
    chart = new Chart(testChart, testChartCfg);
});


