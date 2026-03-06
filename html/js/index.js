let copieProd = JSON.parse(JSON.stringify(tabProd))

let triActuel = {
    type: null
} 

function mettreAJourListe() {

    let copieProd = [...tabProd]

    // TRI
    copieProd = appliquerTri(copieProd)
    afficherListe(copieProd)
    affichagePagination(copieProd)
}

function appliquerTri(tableau) {
    switch(triActuel.type) {
        case "prixAsc": return triPrixCroissant(tableau) 
        case "prixDesc": return triPrixDecroissant(tableau) 
        case "az": return triAz(tableau) 
        case "za": return triZa(tableau) 
        case "noteAsc": return triEtoileCroissant(tableau) 
        case "noteDesc": return triEtoileDecroissant(tableau) 
        case "stockAsc": return triStockCroissant(tableau) 
        case "stockDesc": return triStockDecroissant(tableau) 
    }
    return tableau 
}

// Ajout des eventListeners
function ajoutEventListener(){
// EventListener pour les boutons de changement de page

    // Récupérations des elements
    let premierePage = document.getElementById("premierePage")
    let pagePrec = document.getElementById("pagePrec")
    let pageSuiv = document.getElementById("pageSuiv")
    let dernierePage = document.getElementById("dernierePage")

    // Fonctions pour les boutons
    premierePage.addEventListener("click",function(){
        firstPage(copieProd)
    })
    
    pagePrec.addEventListener("click",function(){
        pagePrecedente(copieProd)
    })

    pageSuiv.addEventListener("click",function(){
        pageSuivante(copieProd)
    })

    dernierePage.addEventListener("click",function(){
        lastPage(copieProd)
    })
// EventListener pour les tris
    // Récupération des elements
    let prixTriCroissant = document.getElementById("prixTriCroissant")
    let prixTriDecroissant = document.getElementById("prixTriDecroissant")
    let alphaTriAZ = document.getElementById("alphaTriAZ")
    let alphaTriZA = document.getElementById("alphaTriZA")
    let noteTri51 = document.getElementById("noteTri51")
    let noteTri15 = document.getElementById("noteTri15")
    let stockTriCroissant = document.getElementById("stockTriCroissant")
    let stockTriDecroissant = document.getElementById("stockTriDecroissant")

    // Fonctions de tri
        // Prix
    prixTriCroissant.addEventListener("change",function () {
        triActuel.type = "prixAsc" 
        copieProd = triPrixCroissant(copieProd)
        mettreAJourListe()
    })            

    prixTriDecroissant.addEventListener("change",function () {
        triActuel.type = "prixDesc" 
        copieProd = triPrixDecroissant(copieProd)
        mettreAJourListe()
    })

        // Ordre alphabétique
    alphaTriAZ.addEventListener("change",function () {
        triActuel.type = "az" 
        copieProd = triAz(copieProd)
        mettreAJourListe()
    })

    alphaTriZA.addEventListener("change",function () {
        triActuel.type = "za" 
        copieProd = triZa(copieProd)
        mettreAJourListe()
    })
    
        // Note
    noteTri51.addEventListener("change",function () {
        triActuel.type = "noteDesc" 
        copieProd = triEtoileDecroissant(copieProd)
        mettreAJourListe()
    })
    
    noteTri15.addEventListener("change", function () {
        triActuel.type = "noteAsc" 
        copieProd = triEtoileCroissant(copieProd)
        mettreAJourListe()
    })

    if(stockTriCroissant != null && stockTriDecroissant != null){
        stockTriCroissant.addEventListener("change",function () {
            triActuel.type = "stockAsc" 
            copieProd = triStockCroissant(copieProd)
            mettreAJourListe()
        })

        stockTriDecroissant.addEventListener("change",function () {
            triActuel.type = "stockDesc" 
            copieProd = triStockDecroissant(copieProd)
            mettreAJourListe()
        })
    }
}