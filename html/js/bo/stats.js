// Définitions des variables utilisées
let dataStats = dataJson; // tableau des données sur les ventes tirés des commandes par requête PHP

let anneeSelection = document.getElementById("select-annee"); // Input select pour l'année
let currentAnnee = anneeSelection.value;

let produitSelection = document.getElementById("select-produit");
let currentIdProd = produitSelection.value;
let currentLibelleProd = produitSelection.options[produitSelection.selectedIndex].textContent;

// Champs de données pour les diagrammes
let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
let dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

let dataProdVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

Object.keys(dataStats[currentAnnee]).forEach(mois => { // Récupères les données des ventes totales pour l'année en cours
    let i = Number(mois);

    dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]); // Insert les données dans le champ data du diagramme
});

Object.keys(dataStats[currentAnnee]).forEach(mois => {
    let i = Number(mois);

    if (dataStats[currentAnnee][mois]["produits"][currentIdProd]) {
        dataProdVentes[i - 1] = Number(dataStats[currentAnnee][mois]["produits"][currentIdProd]["nb_ventes_totales"]);
    }
    else {
        dataProdVentes[i - 1] = 0;
    }
});

// Définitions du graphique des ventes totales générales et de sa config
let allChart = document.getElementById("all-chart");

let allChartCfg = {
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
        scales: {
            y: {
                beginAtZero: true,
                grace: 1,
                ticks: {
                    stepSize: 1
                }
            }
        },
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

let chart = new Chart(allChart, allChartCfg);

// Définitions du graphique et sa config pour les ventes totales d'un produit pour une année
let prodChart = document.getElementById("prod-chart");

let prodChartCfg = {
    type: 'line',
    data: {
        labels: periodes,
        datasets: [{
            label: "Ventes totales durant le mois",
            data: dataProdVentes,
            borderWidth: 3,
            pointBorderWidth: 8,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grace: 1,
                ticks: {
                    stepSize: 1
                }
            }
        },
        plugins: {
            title: {
                display: true,
                text: currentLibelleProd + ' - ' + 'Année' + ' ' + currentAnnee,
                color: '#000',
                font: {
                    size: 24
                }
            }
        }
    }
}

let chart2 = new Chart(prodChart, prodChartCfg);

// Modification du graphique selon l'année sélectionné

anneeSelection.addEventListener("change", function () {
    currentAnnee = anneeSelection.value;

    dataVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]
    dataProdVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]


    Object.keys(dataStats[currentAnnee]).forEach(mois => {
        let i = Number(mois);

        dataVentes[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]);
    });

    Object.keys(dataStats[currentAnnee]).forEach(mois => {
        let i = Number(mois);

        if (dataStats[currentAnnee][mois]["produits"][currentIdProd]) {
            dataProdVentes[i - 1] = Number(dataStats[currentAnnee][mois]["produits"][currentIdProd]["nb_ventes_totales"]);
        }
        else {
            dataProdVentes[i - 1] = 0;
        }
    });

    allChartCfg.options.plugins.title.text = 'Année' + " " + currentAnnee;
    allChartCfg.data.datasets[0].data = dataVentes;

    prodChartCfg.options.plugins.title.text = currentLibelleProd + ' - ' + 'Année' + ' ' + currentAnnee;
    prodChartCfg.data.datasets[0].data = dataProdVentes;

    chart2.destroy();
    chart2 = new Chart(prodChart, prodChartCfg);

    chart.destroy();
    chart = new Chart(allChart, allChartCfg);
});

// Modifications selon le produit sélectionné

produitSelection.addEventListener("change", function () {
    currentIdProd = produitSelection.value;
    currentLibelleProd = produitSelection.options[produitSelection.selectedIndex].textContent;

    dataProdVentes = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0]

    Object.keys(dataStats[currentAnnee]).forEach(mois => {
        let i = Number(mois);

        if (dataStats[currentAnnee][mois]["produits"][currentIdProd]) {
            dataProdVentes[i - 1] = Number(dataStats[currentAnnee][mois]["produits"][currentIdProd]["nb_ventes_totales"]);
        }
        else {
            dataProdVentes[i - 1] = 0;
        }
    });

    prodChartCfg.options.plugins.title.text = currentLibelleProd + ' - ' + 'Année' + ' ' + currentAnnee;
    prodChartCfg.data.datasets[0].data = dataProdVentes;

    chart2.destroy();
    chart2 = new Chart(prodChart, prodChartCfg);
});


