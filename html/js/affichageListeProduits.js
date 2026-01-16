function afficherListe() {

    // TOUJOURS repartir de tabprod
    tableau = tabProd;

    // FILTRAGE
    filtre()
    
    // PAGINATION & AFFICHAGE
    let parent = document.getElementById("prod");
    parent.innerHTML = "";

    for (let i = first; i < first + numberOfItems && i < tableau.length; i++) {
        afficherProduit(i);
    }

    numPageInfo();
}