let copieProd = JSON.parse(JSON.stringify(tabProd))

let triActuel = {
    type: null
};

function mettreAJourListe() {

    let copieProd = [...tabProd];

    // FILTRES
    copieProd = filtre(copieProd);

    // RECHERCHE
    const barreRecherche = document.getElementById("recherche").value;
    copieProd = barreDeRecherche(copieProd, barreRecherche);

    // TRI
    copieProd = appliquerTri(copieProd);

    afficherListe(copieProd);
}


function barreDeRecherche(original, mot) {
    first = 0;
    actualPage = 1;

    if (!mot || mot.trim() === "") {
        return original;
    }

    const recherche = mot.toLowerCase().trim();

    return original.filter(({ libelle_produit }) =>
        libelle_produit.toLowerCase().includes(recherche)
    );
}

function appliquerTri(tableau) {
    switch(triActuel.type) {
        case "prixAsc": return triPrixCroissant(tableau);
        case "prixDesc": return triPrixDecroissant(tableau);
        case "az": return triAz(tableau);
        case "za": return triZa(tableau);
        case "noteAsc": return triEtoileCroissant(tableau);
        case "noteDesc": return triEtoileDecroissant(tableau);
        case "stockAsc": return triStockCroissant(tableau);
        case "stockDesc": return triStockDecroissant(tableau);
    }
    return tableau;
}

function afficheNbProd(tab){
    let nbProduit = document.getElementById("nbProd")
    nbProduit.textContent = "Nbre produit(s): "+tab.length
}


// Ajout des eventListeners
function ajoutEventListener(){
// EventListener pour les boutons de changement de page
    // Barre de recherche
    let barreRecherche = document.getElementById("recherche")

    barreRecherche.addEventListener("input",function(){
        copieBarre = barreDeRecherche(copieProd, barreRecherche.value)
        console.log(copieBarre)
        mettreAJourListe(copieBarre)
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
        triActuel.type = "prixAsc";
        copieProd = triPrixCroissant(copieProd)
        mettreAJourListe(copieProd)
    })            

    prixTriDecroissant.addEventListener("change",function () {
        triActuel.type = "prixDesc";
        copieProd = triPrixDecroissant(copieProd)
        mettreAJourListe(copieProd)
    })

        // Ordre alphabétique
    alphaTriAZ.addEventListener("change",function () {
        triActuel.type = "az";
        copieProd = triAz(copieProd)
        mettreAJourListe(copieProd)
    })

    alphaTriZA.addEventListener("change",function () {
        triActuel.type = "za";
        copieProd = triZa(copieProd)
        mettreAJourListe(copieProd)
    })
    
        // Note
    noteTri51.addEventListener("change",function () {
        triActuel.type = "noteDesc";
        copieProd = triEtoileDecroissant(copieProd)
        mettreAJourListe(copieProd)
    })
    
    noteTri15.addEventListener("change", function () {
        triActuel.type = "noteAsc";
        copieProd = triEtoileCroissant(copieProd)
        mettreAJourListe(copieProd)
    })

    if(stockTriCroissant != null && stockTriDecroissant != null){
        stockTriCroissant.addEventListener("change",function () {
            triActuel.type = "stockAsc";
            copieProd = triStockCroissant(copieProd)
            mettreAJourListe(copieProd)
        })

        stockTriDecroissant.addEventListener("change",function () {
            triActuel.type = "stockDesc";
            copieProd = triStockDecroissant(copieProd)
            mettreAJourListe(copieProd)
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

    // Fonctions de filtres
        //Catégories
    categorieAlimentaire.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "alimentaire", this);
        mettreAJourListe(copieProd)
    });

    categorieVetement.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "vetement", this);
        mettreAJourListe(copieProd)
    });

    categorieArtisanat.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "artisanat", this);
        mettreAJourListe(copieProd)
    });

    categorieGoodies.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "goodies", this);
        mettreAJourListe(copieProd)
    });

    categorieSoin.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "soin", this);
        mettreAJourListe(copieProd)
    });

    
        // Note
    noteNonNote.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "0", this);
        mettreAJourListe(copieProd)
    });

    noteUneE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "1", this);
        mettreAJourListe(copieProd)
    });

    noteDeuxE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "2", this);
        mettreAJourListe(copieProd)
    });

    noteTroisE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "3", this);
        mettreAJourListe(copieProd)
    });

    noteQuatreE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "4", this);
        mettreAJourListe(copieProd)
    });

    noteCinqE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "5", this);
        mettreAJourListe(copieProd)
    });


        // Tranche de prix
    tranchePrix1.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix1", this);
        mettreAJourListe(copieProd)
    });

    tranchePrix2.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix2", this);
        mettreAJourListe(copieProd)
    });

    tranchePrix3.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix3", this);
        mettreAJourListe(copieProd)
    });

    tranchePrix4.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix4", this);
        mettreAJourListe(copieProd)
    });

    tranchePrix5.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix5", this);
        mettreAJourListe(copieProd)
    });

    // EventListeners pour l'animation sidebar filtre et tri
    // Récupératiion des elements
    let boutonSidebar = document.getElementById("filtresTris")
    let sidebar = document.getElementsByTagName("aside")[0]
    let fermerSidebar = document.getElementById("fermerSidebar")

    if(boutonSidebar != null){
        // Fonction ouverture
        boutonSidebar.addEventListener("click", function(){
            boutonSidebar.classList.add("hidden")
            sidebar.classList.remove("hidden")
        })

        //Fonction fermeture
        fermerSidebar.addEventListener("click", function(){
            sidebar.classList.add("hidden")
            boutonSidebar.classList.remove("hidden")
        })
    }
}