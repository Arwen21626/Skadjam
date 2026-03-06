let copieProd = JSON.parse(JSON.stringify(tabProd))

let triActuel = {
    type: null
} 

function mettreAJourListe() {

    let copieProd = [...tabProd] 

    // FILTRES
    copieProd = filtre()

    // RECHERCHE
    const barreRecherche = document.getElementById("recherche").value 
    copieProd = barreDeRecherche(copieProd, barreRecherche) 

    // TRI
    copieProd = appliquerTri(copieProd)
    afficherListe(copieProd)
    affichagePagination(copieProd)
}


function barreDeRecherche(original, mot) {
    first = 0 
    actualPage = 1 

    if (!mot || mot.trim() === "") {
        return original 
    }

    const recherche = mot.toLowerCase().trim() 

    return original.filter(({ libelle_produit }) =>
        libelle_produit.toLowerCase().includes(recherche)
    ) 
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
    // Barre de recherche
    let barreRecherche = document.getElementById("recherche")

    barreRecherche.addEventListener("input",function(){
        copieBarre = barreDeRecherche(copieProd, barreRecherche.value)
        mettreAJourListe()
    })

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
     

// EventListener pour les filtres
    // Récupération des elements
        //Catégories
    const  categorieVetement = document.getElementById("vetement")
    const  categorieArtisanat = document.getElementById("artisanat")
    const  categorieGoodies = document.getElementById("goodies")
    const  categorieSoin = document.getElementById("soin")
    const  categorieAlimentaire = document.getElementById("alimentaire")
        // Notes
    const noteNonNote = document.getElementById("nonNote")
    const  noteUneE = document.getElementById("uneE")
    const  noteDeuxE = document.getElementById("deuxE")
    const  noteTroisE = document.getElementById("troisE")
    const  noteQuatreE = document.getElementById("quatreE")
    const  noteCinqE = document.getElementById("cinqE")
        // Tranches de prix
    const  tranchePrix1 = document.getElementById("prix1")
    const  tranchePrix2 = document.getElementById("prix2")
    const  tranchePrix3 = document.getElementById("prix3")
    const  tranchePrix4 = document.getElementById("prix4")
    const  tranchePrix5 = document.getElementById("prix5")
        // filtre vendeur
    const  filtreVendeur = document.getElementsByClassName("vendeur")
    
    // Fonctions de filtres

        // Filtre vendeur
    for(let i = 0; i < filtreVendeur.length; i++){
        filtreVendeur[i].addEventListener("change", function () {
            copieProd = toggleFiltre(checkedVendeurs, this.value, this);
            mettreAJourListe()
        })
    }

        //Catégories
    categorieAlimentaire.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "alimentaire", this) 
        mettreAJourListe()
    }) 

    categorieVetement.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "vetement", this) 
        mettreAJourListe()
    }) 

    categorieArtisanat.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "artisanat", this) 
        mettreAJourListe()
    }) 

    categorieGoodies.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "goodies", this) 
        mettreAJourListe()
    }) 

    categorieSoin.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "soin", this) 
        mettreAJourListe()
    }) 

    
        // Note
    noteNonNote.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "0", this) 
        mettreAJourListe()
    }) 

    noteUneE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "1", this) 
        mettreAJourListe()
    }) 

    noteDeuxE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "2", this) 
        mettreAJourListe()
    }) 

    noteTroisE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "3", this) 
        mettreAJourListe()
    }) 

    noteQuatreE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "4", this) 
        mettreAJourListe()
    }) 

    noteCinqE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "5", this) 
        mettreAJourListe()
    }) 


        // Tranche de prix
    tranchePrix1.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix1", this) 
        mettreAJourListe()
    }) 

    tranchePrix2.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix2", this) 
        mettreAJourListe()
    }) 

    tranchePrix3.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix3", this) 
        mettreAJourListe()
    }) 

    tranchePrix4.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix4", this) 
        mettreAJourListe()
    }) 

    tranchePrix5.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix5", this) 
        mettreAJourListe()
    }) 


    if(document.getElementById("filtresTris")){
        // EventListeners pour l'animation sidebar filtre et tri
        // Récupératiion des elements
        let boutonSidebar = document.getElementById("filtresTris")
        let sidebar = document.getElementsByTagName("aside")[0]
        
        // Fonction ouverture
        boutonSidebar.addEventListener("click", function(){
            boutonSidebar.classList.add("hidden")
            sidebar.classList.remove("hidden")
        })

        if (document.getElementById("fermerSidebar")) {
            // Fonction fermeture
            let fermerSidebar = document.getElementById("fermerSidebar")
            fermerSidebar.addEventListener("click", function(){
                sidebar.classList.add("hidden")
                boutonSidebar.classList.remove("hidden")
            })
        }
        
    }
}