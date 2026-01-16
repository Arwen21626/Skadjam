var checkedCategories = []
var checkedNotes = []
var checkedTranches = []

let tableau = []

// Ajout des eventListeners
function ajoutEventListener(){
    tableau = []
    
// EventListener pour les boutons de changement de page
    // Récupérations des elements
    let premierePage = document.getElementById("premierePage")
    let pagePrec = document.getElementById("pagePrec")
    let pageSuiv = document.getElementById("pageSuiv")
    let dernierePage = document.getElementById("dernierePage")

    // Fonctions pour les boutons
    premierePage.addEventListener("click",function(){
        firstPage()
    })
    
    pagePrec.addEventListener("click",function(){
        pagePrecedente()
    })

    pageSuiv.addEventListener("click",function(){
        pageSuivante()
    })

    dernierePage.addEventListener("click",function(){
        lastPage()
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
    prixTriCroissant.addEventListener("click",function () {
        afficherListe(triPrixCroissant(tableau))
    })            

    prixTriDecroissant.addEventListener("click",function () {
        afficherListe(triPrixDecroissant(tableau))
    })

        // Ordre alphabétique
    alphaTriAZ.addEventListener("click",function () {
        afficherListe(triAz(tableau))
    })

    alphaTriZA.addEventListener("click",function () {
        afficherListe(triZa(tableau))
    })
    
        // Note
    noteTri51.addEventListener("click",function () {
        afficherListe(triEtoileDecroissant(tableau))
    })
    
    noteTri15.addEventListener("click", function () {
        afficherListe(triEtoileCroissant(tableau))
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
        toggleFiltre(checkedCategories, "alimentaire", this);
    });

    categorieVetement.addEventListener("change", function () {
        toggleFiltre(checkedCategories, "vetement", this);
    });

    categorieArtisanat.addEventListener("change", function () {
        toggleFiltre(checkedCategories, "artisanat", this);
    });

    categorieGoodies.addEventListener("change", function () {
        toggleFiltre(checkedCategories, "goodies", this);
    });

    categorieSoin.addEventListener("change", function () {
        toggleFiltre(checkedCategories, "soin", this);
    });

    
        // Note

    noteNonNote.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "0", this);
    });

    noteUneE.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "1", this);
    });

    noteDeuxE.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "2", this);
    });

    noteTroisE.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "3", this);
    });

    noteQuatreE.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "4", this);
    });

    noteCinqE.addEventListener("change", function () {
        toggleFiltre(checkedNotes, "5", this);
    });


    // Tranche de prix
    tranchePrix1.addEventListener("change", function () {
        toggleFiltre(checkedTranches, "prix1", this);
    });

    tranchePrix2.addEventListener("change", function () {
        toggleFiltre(checkedTranches, "prix2", this);
    });

    tranchePrix3.addEventListener("change", function () {
        toggleFiltre(checkedTranches, "prix3", this);
    });

    tranchePrix4.addEventListener("change", function () {
        toggleFiltre(checkedTranches, "prix4", this);
    });

    tranchePrix5.addEventListener("change", function () {
        toggleFiltre(checkedTranches, "prix5", this);
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

function afficherProduit(indice){
    
    let i = indice
    
    let idProduit = tableau[i]['id_produit']
    let parent = document.getElementById("prod")

    // Section   
    let produit = document.createElement("section")
    parent.appendChild(produit)
    produit.classList.add("bg-bleu","flex", "flex-col", "w-40", "h-auto","p-2", "m-2", "md:w-80", "md:p-3")
    parent = produit

    //Lien
    let lien = document.createElement("a")
    lien.href = "details_produit.php?idProduit="+idProduit
    lien.classList.add("mb-3");
    parent.appendChild(lien)

    parent = lien

    // Image
    let image = document.createElement("img")
    image.src = tableau[i]['url_photo']
    image.alt = tableau[i]['alt']
    image.title = tableau[i]['title']
    image.classList.add("w-auto", "h-40", "md:h-80", "justify-self-center")
    parent.appendChild(image)

    // Nom produit
    let nom = document.createElement("p")
    nom.textContent = tableau[i]['libelle_produit']
    parent.appendChild(nom)

    // Prix et note
    let contientPrix = document.createElement("article")
    parent.appendChild(contientPrix)
    contientPrix.classList.add("flex","flex-row", "justify-between", "items-center")

    parent = contientPrix

    // Prix TTC
    let prixTTC = document.createElement("p")
    prixTTC.textContent = tableau[i]['prix_ttc'].replace(".", ",")+" € (TTC)"
    prixTTC.classList.add("line-through")
    parent.appendChild(prixTTC)

    let prixRemise = document.createElement("p")
    prixRemise.textContent = tableau[i]['prix_remise'].replace(".", ",")+" € (TTC)"
    parent.appendChild(prixRemise)

    if (tableau[i]['prix_remise'] == tableau[i]['prix_ttc']) {
        prixRemise.classList.add("hidden")
        prixTTC.classList.remove("line-through")
    }

    // Note
    let contientNote = document.createElement("div")
    parent = lien   
    parent.appendChild(contientNote)
    contientNote.classList.add("flex")

    parent = contientNote

    let note = tableau[i]['note_moyenne']
    affichageNote(note, parent)

    //setTimeout(function(){console.log('Code waits for 1  second')}, 1000);
}