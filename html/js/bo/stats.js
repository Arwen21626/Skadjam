Chart.Tooltip.positioners.cursor = function(items, eventPosition) {
    // Permet de définir la position du curseur pour tout les graphiques, pour permettre à la tooltip d'être placé sur ce dernier
    return {
        x: eventPosition.x,
        y: eventPosition.y
    };
};

// Définitions des variables utilisées
let dataStats = dataJson; // tableau des données sur les ventes tirés des commandes par requête PHP

if (Object.keys(dataStats).length > 0) {

    let anneeSelection = document.getElementById("select-annee"); // Input select pour l'année
    let currentAnnee = anneeSelection.value;

    let currentLibelleProd = "Temp";

    let formatSelection = document.getElementById("select-format");
    let currentFormat = formatSelection.value;

    let divProdChart = document.getElementById("div-prod-chart");
    let containerProdChart = document.getElementById("container-prod-chart");
    let divTextChart = document.createElement("div");
    let textChart = document.createElement("h4");


    // Champs de données pour les diagrammes
    let periodes = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
    let categories = ["Alimentaire", "Vêtement", "Artisanat", "Goodies", "Soin"];

    let dataVentesVolume = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    let dataVentesMontant = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    let currentDataVentes = dataVentesVolume;

    let dataProdVentesVolume = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    let dataProdVentesMontant = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
    let currentDataProdVentes = dataProdVentesVolume;

    let dataCatVentesVolume = [0, 0, 0, 0, 0];
    let dataCatVentesMontant = [0, 0, 0, 0, 0];
    let currentDataCatVentes = dataCatVentesVolume;

    function updateChartFormat(){

        if (currentFormat == "volume") {
            
            currentDataVentes = dataVentesVolume;
            currentDataCatVentes = dataCatVentesVolume;
            currentDataProdVentes = dataProdVentesVolume;
        }
        else if (currentFormat == "euro") {

            currentDataVentes = dataVentesMontant;
            currentDataCatVentes = dataCatVentesMontant;
            currentDataProdVentes = dataProdVentesMontant;
        }
    }

    function arrangeYearAndCategDatas() {

        dataVentesVolume = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        dataProdVentesVolume = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        dataCatVentesVolume = [0, 0, 0, 0, 0];

        dataVentesMontant = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        dataProdVentesMontant = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        dataCatVentesMontant = [0, 0, 0, 0, 0];

        updateChartFormat();

        Object.keys(dataStats[currentAnnee]).forEach(mois => { // Récupères les données des ventes totales pour l'année en cours
            let i = Number(mois);

            dataVentesVolume[i - 1] = Number(dataStats[currentAnnee][mois]["nb_ventes_totales"]); // Insert les données dans le champ data du diagramme
            dataVentesMontant[i - 1] = Number(dataStats[currentAnnee][mois]["montant_total_ttc"]) // Insert les données en version montant

            Object.keys(dataStats[currentAnnee][mois]["produits"]).forEach(produit => { // Insert les données des catégories dans le champ data du diagramme
                let iCat = Number(dataStats[currentAnnee][mois]["produits"][produit]["id_categorie"]);
                
                dataCatVentesVolume[iCat - 1] += Number(dataStats[currentAnnee][mois]["produits"][produit]["nb_ventes_totales"])
                dataCatVentesMontant[iCat - 1] += Number(dataStats[currentAnnee][mois]["produits"][produit]["montant_total_ttc"])
            });
        });
    }

    function arrangeProdDatas(container) {

        dataProdVentesVolume = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        dataProdVentesMontant = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

        updateChartFormat();

        let nbProd = 0;

        Object.keys(dataStats[currentAnnee]).forEach(mois => {
            let i = Number(mois);
            
            if (dataStats[currentAnnee][mois]["produits"][container.id]) {
                dataProdVentesVolume[i - 1] = Number(dataStats[currentAnnee][mois]["produits"][container.id]["nb_ventes_totales"]);
                dataProdVentesMontant[i - 1] = Number(dataStats[currentAnnee][mois]["produits"][container.id]["montant_total_ttc"]);
                nbProd += Number(dataStats[currentAnnee][mois]["produits"][container.id]["nb_ventes_totales"]);
            }
            else {
                dataProdVentesVolume[i - 1] = 0;
                dataProdVentesMontant[i - 1] = 0;
            }
        });

        if (nbProd == 0){
            divProdChart.classList.add("hidden");
            divTextChart.classList.remove("hidden");
            textChart.classList.add("text-center");
            textChart.textContent = "Aucune statistique enregistrée pour ce produit en " + currentAnnee;

            divTextChart.classList.add("justify-center", "flex", "flex-col", "w-[60vw]", "h-[50vh]")

            divTextChart.appendChild(textChart);
            containerProdChart.appendChild(divTextChart);

            container.querySelector("div").textContent = "Aucune stat enregistrée";
        }
        else {
            divProdChart.classList.remove("hidden");
            divTextChart.classList.add("hidden");
            currentLibelleProd = container.querySelector("p").textContent;
        }
    }

    function updateDatasFields(){

        allChartCfg.options.plugins.title.text = 'Année' + " " + currentAnnee;
        allChartCfg.data.datasets[0].data = currentDataVentes;

        prodChartCfg.options.plugins.title.text = currentLibelleProd + ' - ' + 'Année' + ' ' + currentAnnee;
        prodChartCfg.data.datasets[0].data = currentDataProdVentes;

        cateChartCfg.options.plugins.title.text = 'Année' + " " + currentAnnee;
        cateChartCfg.data.datasets[0].data = currentDataCatVentes;

        if (currentFormat == "volume") {
            allChartCfg.data.datasets[0].label = "Ventes totales durant le mois en nombre";
            cateChartCfg.data.datasets[0].label = "Ventes totales en nombre";
            prodChartCfg.data.datasets[0].label = "Ventes totales durant le mois en nombre";
        }
        else if (currentFormat == "euro") {    
            allChartCfg.data.datasets[0].label = "Ventes totales durant le mois en €";
            cateChartCfg.data.datasets[0].label = "Ventes totales en €";
            prodChartCfg.data.datasets[0].label = "Ventes totales durant le mois en €";
        }
        
    }

    function updateCharts(){
        chart3.destroy();
        chart3 = new Chart(cateChart, cateChartCfg);

        chart2.destroy();
        chart2 = new Chart(prodChart, prodChartCfg);

        chart.destroy();
        chart = new Chart(allChart, allChartCfg);
    }

    arrangeYearAndCategDatas();

    // Définitions du graphique des ventes totales générales et de sa config
    let allChart = document.getElementById("all-chart");

    let allChartCfg = {
        type: 'line',
        data: {
            labels: periodes,
            datasets: [{
                label: "Ventes totales durant le mois en nombre",
                data: currentDataVentes,
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
                        size: 21
                    }
                },
                legend: {
                    labels: {
                        color: '#000'
                    }
                },
                tooltip: {
                    position: 'cursor'
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    }

    let chart = new Chart(allChart, allChartCfg);

    // Définitions du graphique et sa config pour les ventes totales par catégorie
    let cateChart = document.getElementById("categorie-chart");

    let cateChartCfg = {
        type: 'pie',
        data: {
            labels: categories,
            datasets: [{
                label: "Ventes totales en nombre",
                data: currentDataCatVentes,
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
                    text: 'Année' + ' ' + currentAnnee,
                    color: '#000',
                    font: {
                        size: 21
                    }
                },
                legend: {
                    labels: {
                        color: '#000'
                    }
                }
            }
        }
    }

    let chart3 = new Chart(cateChart, cateChartCfg);

    // Définitions du graphique et sa config pour les ventes totales d'un produit pour une année
    let prodChart = document.getElementById("prod-chart");

    let prodChartCfg = {
        type: 'line',
        data: {
            labels: periodes,
            datasets: [{
                label: "Ventes totales durant le mois en nombre",
                data: currentDataProdVentes,
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
                        size: 21
                    }
                },
                legend: {
                    labels: {
                        color: '#000'
                    }
                },
                tooltip: {
                    position: 'cursor'
                }
            },
            interaction: {
                mode: 'index',
                intersect: false
            }
        }
    }

    let chart2 = new Chart(prodChart, prodChartCfg);

    // Modification du graphique selon l'année sélectionné

    anneeSelection.addEventListener("change", function () {
        currentAnnee = anneeSelection.value;

        arrangeYearAndCategDatas();

        updateDatasFields();

        updateCharts();
    });

    // Modification selon le format choisi

    formatSelection.addEventListener("change", function () {
        currentFormat = formatSelection.value;
        updateChartFormat();
        updateDatasFields();
        updateCharts();
    });

    // Modifications selon le produit sélectionné

    let chartOpened = false;
    let overlayChart = document.querySelector("#overlay-chart");

    function showProdChart() {

        if (chartOpened == false) {

            overlayChart.classList.remove("hidden")
            containerProdChart.classList.remove("hidden");

            chartOpened = true;
        }
    }

    document.querySelectorAll(".produit").forEach(container => {

        // balise div pour pouvoir changer la taille du texte, 
        // car la définition dans le input pour les balises <p> écrasent tout même à la redéfinition
        let divInfoStatIsRegistered = document.createElement("div");
        divInfoStatIsRegistered.classList.add("text-center", "text-base", "mb-4", "mt-2");
        divInfoStatIsRegistered.textContent = "Stat enregistrée";

        container.appendChild(divInfoStatIsRegistered);

        arrangeProdDatas(container);

        container.addEventListener("click", function (){
            
            arrangeProdDatas(container);
            
            updateDatasFields();

            updateCharts();

            showProdChart();
        });
    });

    document.querySelector("#overlay-chart").addEventListener("click", function () {

        if (chartOpened == true) {
            overlayChart.classList.add("hidden")
            containerProdChart.classList.add("hidden");

            chartOpened = false;
        }
    });

    containerProdChart.addEventListener("click", function () {

        if (chartOpened == true) {
            overlayChart.classList.add("hidden")
            containerProdChart.classList.add("hidden");

            chartOpened = false;
        }
    });    
}




