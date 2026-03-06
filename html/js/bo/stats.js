// Définitions des variables utilisées
let dataStats = dataJson; // tableau des données sur les ventes tirés des commandes par requête PHP

let anneeSelection = document.getElementById("select-annee"); // Input select pour l'année

// Champs de données pour les diagrammes
let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
let dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

let currentAnnee = anneeSelection.value;

Object.keys(dataStats[currentAnnee]).forEach(mois => { // Récupères les données des ventes totales pour l'année en cours
    let i = Number(mois);

    dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]); // Insert les données dans le champ data du diagramme
});

// Définitions du graphique des ventes totales générales et de sa config
let allChart = document.getElementById("all-chart");

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
                text: 'Année' + " " + currentAnnee,
                color: '#000',
                font: {
                    size: 24
                }
            }
        }
    }
}

let chart = new Chart(allChart, testChartCfg);

// Modification du graphique selon l'année sélectionné

anneeSelection.addEventListener("change", function () {
    currentAnnee = anneeSelection.value;
    dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

    Object.keys(dataStats[currentAnnee]).forEach(mois => {
        let i = Number(mois);

        dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]);
    });

    testChartCfg.options.plugins.title.text = 'Année' + " " + currentAnnee;
    testChartCfg.data.datasets[0].data = dataVentes;

    chart.destroy();
    chart = new Chart(allChart, testChartCfg);
});


