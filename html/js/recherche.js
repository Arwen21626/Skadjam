let copieProd = JSON.parse(JSON.stringify(tabProd))

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

    // Fonctions de tri
        // Prix
    prixTriCroissant.addEventListener("change",function () {
        copieProd = triPrixCroissant(copieProd)
        afficherListe(copieProd)
    })            

    prixTriDecroissant.addEventListener("change",function () {
        copieProd = triPrixDecroissant(copieProd)
        afficherListe(copieProd)
    })

        // Ordre alphabétique
    alphaTriAZ.addEventListener("change",function () {
        copieProd = triAz(copieProd)
        afficherListe(copieProd)
    })

    alphaTriZA.addEventListener("change",function () {
        copieProd = triZa(copieProd)
        afficherListe(copieProd)
    })
    
        // Note
    noteTri51.addEventListener("change",function () {
        copieProd = triEtoileDecroissant(copieProd)
        afficherListe(copieProd)
    })
    
    noteTri15.addEventListener("change", function () {
        copieProd = triEtoileCroissant(copieProd)
        afficherListe(copieProd)
    })

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
        afficherListe(copieProd)
    });

    categorieVetement.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "vetement", this);
        afficherListe(copieProd)
    });

    categorieArtisanat.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "artisanat", this);
        afficherListe(copieProd)
    });

    categorieGoodies.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "goodies", this);
        afficherListe(copieProd)
    });

    categorieSoin.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedCategories, "soin", this);
        afficherListe(copieProd)
    });

    
        // Note
    noteNonNote.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "0", this);
        afficherListe(copieProd)
    });

    noteUneE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "1", this);
        afficherListe(copieProd)
    });

    noteDeuxE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "2", this);
        afficherListe(copieProd)
    });

    noteTroisE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "3", this);
        afficherListe(copieProd)
    });

    noteQuatreE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "4", this);
        afficherListe(copieProd)
    });

    noteCinqE.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedNotes, "5", this);
        afficherListe(copieProd)
    });


        // Tranche de prix
    tranchePrix1.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix1", this);
        afficherListe(copieProd)
    });

    tranchePrix2.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix2", this);
        afficherListe(copieProd)
    });

    tranchePrix3.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix3", this);
        afficherListe(copieProd)
    });

    tranchePrix4.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix4", this);
        afficherListe(copieProd)
    });

    tranchePrix5.addEventListener("change", function () {
        copieProd = toggleFiltre(checkedTranches, "prix5", this);
        afficherListe(copieProd)
    });

// EventListeners pour l'animation sidebar filtre et tri
    // Récupératiion des elements
    let boutonSidebar = document.getElementById("filtresTris")
    let sidebar = document.getElementsByTagName("aside")[0]
    let fermerSidebar = document.getElementById("fermerSidebar")

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