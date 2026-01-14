const numberOfItems = 24 //NB produits à afficher tablette
// const numberOfItemsPhone = 12 //NB produits à afficher
let first = 0
let actualPage

let tableau = []

var checkedCategories = []
var checkedNotes = []
var checkedTranches = []

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
    const  noteZeroE = document.getElementById("zeroE")
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
    
    categorieAlimentaire.addEventListener("click",function () {
        if(categorieAlimentaire.checked){
            checkedCategories.push("alimentaire")
            tableau = filtrageCategorieAlimentaire(tabProd);
            console.log("check")
            
        }else{
            checkedCategories.pop("alimentaire")
            console.log("uncheck")
        }
        
        console.log("tabAlim " + tableau)
        afficherListe()
    })

    
    categorieVetement.addEventListener("click",function () {
        if(categorieVetement.checked){
            checkedCategories.push("vetement")
            tableau = filtrageCategorieVetement(tabProd);
            console.log("check")
        }else{
            checkedCategories.pop("vetement")
            console.log("uncheck")
        }
        console.log("tabVet " + tableau)
        afficherListe()
    })

    
    categorieArtisanat.addEventListener("click",function () {
        if(categorieArtisanat.checked){
            checkedCategories.push("artisanat")
            tableau = filtrageCategorieArtisanat(tabProd);
            console.log("check")
        }else{
            checkedCategories.pop("artisanat")
            console.log("uncheck")
        }
        console.log("tabArt " + tableau)
        afficherListe()
    })

    
    categorieGoodies.addEventListener("click",function () {
        if(categorieGoodies.checked){
            checkedCategories.push("goodies")
            tableau = filtrageCategorieGoodies(tabProd);
            console.log("check")
        }else{
            checkedCategories.pop("goodies")
            console.log("uncheck")
        }
        console.log("tabGood " + tableau)
        afficherListe()
    })

    
    categorieSoin.addEventListener("click",function () {
        if(categorieSoin.checked){
            checkedCategories.push("soin")
            tableau = filtrageCategorieSoin(tabProd);
            console.log("check")
        }else{
            checkedCategories.pop("soin")
            console.log("uncheck")
        }
        console.log("tabSoin " + tableau)
        afficherListe()
    })
    
        // Note
    noteZeroE.addEventListener("click",function () {
    })

    noteUneE.addEventListener("click",function () {
    })

    noteDeuxE.addEventListener("click",function () {
    })

    noteTroisE.addEventListener("click",function () {
    })
    
    noteQuatreE.addEventListener("click",function () {
    })

    noteCinqE.addEventListener("click",function () {
    })

    // Tranche de prix
    tranchePrix1.addEventListener("click",function () {
    })

    tranchePrix2.addEventListener("click",function () {
    })

    tranchePrix3.addEventListener("click",function () {
    })
    
    tranchePrix4.addEventListener("click",function () {
    })

    tranchePrix5.addEventListener("click",function () {
    })

// EventListeners pour l'animation sidebar filtre et tri
    // Récupératiion des elements
    let boutonSidebar = document.getElementById("filtresTris")
    let sidebar = document.getElementsByTagName("aside")[0]
    let listeProd = document.getElementById("listeProduit")
    let prod = document.getElementById("prod")
    let fermerSidebar = document.getElementById("fermerSidebar")
    let changePage = document.getElementById("changePage")

    // Fonction ouverture
    boutonSidebar.addEventListener("click", function(){
        boutonSidebar.classList.add("hidden")
        prod.classList.add("content-end")
        sidebar.classList.remove("hidden")
        sidebar.classList.add("translate-x-0")
    })

    //Fonction fermeture
    fermerSidebar.addEventListener("click", function(){
        sidebar.classList.remove("translate-x-0")
        sidebar.classList.add("hidden")
        boutonSidebar.classList.remove("hidden")
        prod.classList.remove("content-end")
    })

    
}


function firstPage(){
    first = 0
    actualPage = 1
    afficherListe()
}

function lastPage(){
    first = (maxPages * numberOfItems)-numberOfItems;
    actualPage = maxPages;
    afficherListe(); 
}

function pagePrecedente(){
    if(first-numberOfItems >= 0){
        first-=numberOfItems
        actualPage --;
        afficherListe();
    }
}

function pageSuivante(){
    if(first+numberOfItems<tableau.length){
        first+=numberOfItems;
        actualPage ++;
        afficherListe();
    }
}

// function numPageInfo(){
//   document.getElementById('pageInfo').innerHTML = `
//     Page ${actualPage} / ${maxPages}
//   `
// }


// Affichage
function afficherListe(){


    console.log("tableau")
    console.log(checkedCategories)  
    if(checkedCategories.length === 0 /*&& checkedNotes.length === 0 && checkedTranches.length === 0*/){
        console.log("rien n'est coché")
        tableau = tabProd
    }else{
        for(let i=0; i<checkedCategories.length; i++){
            if(checkedCategories[i] === "alimentaire"){
                tableau = tableau.concat(filtrageCategorieAlimentaire(tabProd));
            }
            if(checkedCategories[i] === "vetement"){
                tableau = tableau.concat(filtrageCategorieVetement(tabProd));
            }
            if(checkedCategories[i] === "artisanat"){
                tableau = tableau.concat(filtrageCategorieArtisanat(tabProd));
            }
            if(checkedCategories[i] === "goodies"){
                tableau = tableau.concat(filtrageCategorieGoodies(tabProd));
            }
            if(checkedCategories[i] === "soin"){
                tableau = tableau.concat(filtrageCategorieSoin(tabProd));
            }
        }
    }


    if(tableau.length === 0){
        tableau = tabProd
    }
    let parent = document.getElementById("prod")
    parent.innerHTML = ""
    for(let i = first; i < first + numberOfItems;i++){
        if(i<tableau.length){
            afficherProduit(i)
        }
    }
}


function afficherProduit(indice){
    let i = indice
    let idProduit = tableau[i]['id_produit']
    let parent = document.getElementById("prod")

    // Section   
    let produit = document.createElement("section")
    parent.appendChild(produit)
    produit.classList.add("bg-bleu", "grid", "grid-cols-[40%_60%]", "w-40", "h-120", "md:w-80", "p-2", "md:p-3", "m-2")
    parent = produit

    //Lien
    let lien = document.createElement("a")
    lien.href = "details_produit.php?idProduit="+idProduit
    lien.classList.add("col-span-2", "justify-self-center", "mb-3");
    parent.appendChild(lien)

    parent = lien

    // Image
    let image = document.createElement("img")
    image.src = tableau[i]['url_photo']
    image.alt = tableau[i]['alt']
    image.title = tableau[i]['title']
    image.classList.add("w-auto", "h-80", "w-50", "justify-self-center")
    parent.appendChild(image)

    // Nom produit
    let nom = document.createElement("p")
    nom.textContent = tableau[i]['libelle_produit']
    parent.appendChild(nom)
    nom.classList.add("col-span-2", "w-70")

    // Prix et note
    let contient = document.createElement("div")
    parent.appendChild(contient)
    contient.classList.add("flex", "justify-start", "items-center", "col-span-2")

    parent = contient

    // Prix
    let prix = document.createElement("p")
    prix.textContent = tableau[i]['prix_ttc'].replace(".", ",")+" €"
    parent.appendChild(prix)

    // Note
    let contientNote = document.createElement("div")
    parent.appendChild(contientNote)
    contientNote.classList.add("w-2/4", "ml-2", "md:ml-10", "flex")

    parent = contientNote

    let note = tableau[i]['note_moyenne']
    affichageNote(note, parent)

    //setTimeout(function(){console.log('Code waits for 1  second')}, 1000);
}

async function affichageNote(note, parent ){
    // fonction qui affiche une note avec des étoiles
    //affichage d'une note nulle
    let section = document.createElement("section")
    
    parent.appendChild(section)
    parent = section

    section.classList.add("flex", "flex-nowrap", "items-center")

    if (note == null){
        let p = document.createElement("p")
        p.textContent = "non noté"
        parent.appendChild(p)
    } 

    else {
        let entierPrec = parseInt(note)
        let entierSuiv = entierPrec +1
        let moitie = entierPrec + 0.5
        let noteFinale = 0
        let nbEtoileVides = 0

        //note arrondie à l'entier précédent
        if(note < entierPrec+0.3){
            noteFinale = entierPrec;
        }

        //note arrondie à 0.5
        else if((note < moitie) || (note < entierPrec+0.8)){
            noteFinale = moitie;
            nbEtoileVides = 5-entierPrec-1;
            //affichage d'une note et demie
            //boucle pour étoiles pleines
            for(i=0; i<entierPrec; i++){
                let etoileP = document.createElement("img")
                etoileP.src = "../../images/logo/bootstrap_icon/star-fill.svg"
                etoileP.alt = "étoile pleine"
                etoileP.title = "étoile pleine"
                parent.appendChild(etoileP)
            }
            // Demie étoile
            let etoileD = document.createElement("img")
            etoileD.src = "../../images/logo/bootstrap_icon/star-half.svg"
            etoileD.alt = "demie étoile"
            etoileD.title = "demie étoile"
            parent.appendChild(etoileD)
            
            // Boucle pour étoiles vides-->
            for(i=0; i<nbEtoileVides; i++){
                let etoileV = document.createElement("img")
                etoileV.src = "../../images/logo/bootstrap_icon/star.svg"
                etoileV.alt = "étoile vide"
                etoileV.title = "étoile vide"
                parent.appendChild(etoileV)
            }
        }
        
        //note arrondie à l'entier suivant
        else{
            noteFinale = entierSuiv;
        }

        //affichage d'une note entière :
        if(noteFinale != moitie){
            nbEtoileVides = 5-noteFinale;
            //boucle pour étoiles pleines
            for(i=0; i<noteFinale; i++){
                let etoilePF = document.createElement("img")
                etoilePF.src = "../../images/logo/bootstrap_icon/star-fill.svg"
                etoilePF.alt = "étoile pleine"
                etoilePF.title = "étoile pleine"
                parent.appendChild(etoilePF)
            }
            //boucle pour étoiles vides
            for(i=0; i<nbEtoileVides; i++){
                let etoileVF = document.createElement("img")
                etoileVF.src = "../../images/logo/bootstrap_icon/star.svg"
                etoileVF.alt = "étoile vide"
                etoileVF.title = "étoile vide"
                parent.appendChild(etoileVF)
            }
        }
    }
}

// Tris
function triPrixCroissant(tab){
    let temp = tab.sort((a, b) => parseInt(a['prix_ttc']) - parseInt(b['prix_ttc']))
    console.log(temp)
    return tab.sort((a, b) => parseInt(a['prix_ttc']) - parseInt(b['prix_ttc']))
}

function triPrixDecroissant(tab){
    let temp = tab.sort((a, b) => parseFloat(b['prix_ttc']) - parseFloat(a['prix_ttc']))
    console.log(temp)
    return tab.sort((a, b) => parseFloat(b['prix_ttc']) - parseFloat(a['prix_ttc']))
}

function triAz(tab){
    let temp = tab.sort((a,b) => a['libelle_produit'].localeCompare(b['libelle_produit']))
    console.log(temp)
    return tab.sort((a,b) => a['libelle_produit'].localeCompare(b['libelle_produit']))
}

function triZa(tab){
    let temp = tab.sort((a,b) => b['libelle_produit'].localeCompare(a['libelle_produit']))
    console.log(temp)
    return tab.sort((a,b) => b['libelle_produit'].localeCompare(a['libelle_produit']))
}

function triEtoileCroissant(tab){
    let temp = tab.sort((a,b) => parseFloat(a['note_moyenne']) - parseFloat(b['note_moyenne']))
    console.log(temp)
    return tab.sort((a,b) => parseFloat(a['note_moyenne']) - parseFloat(b['note_moyenne']))
}

function triEtoileDecroissant(tab){
    let temp = tab.sort((a,b) => parseFloat(b['note_moyenne']) - parseFloat(a['note_moyenne']))
    console.log(temp)
    return tab.sort((a,b) => parseFloat(b['note_moyenne']) - parseFloat(a['note_moyenne']))
}



// Filtres

// Alimentaire = 1
function filtrageCategorieAlimentaire(tableau){ 
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 1)
    // console.log(newTab)
    return newTab
}

// Vetement = 2
function filtrageCategorieVetement(tableau){
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 2)
    return newTab
}

// Artisanat = 3
function filtrageCategorieArtisanat(tableau){
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 3)
    return newTab
}

// Goodies = 4
function filtrageCategorieGoodies(tableau){
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 4)
    return newTab
}

// Soin = 5
function filtrageCategorieSoin(tableau){
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 5)
    return newTab
}


// NOTE

// non noté
function filtrageNoteNonNote(tabProd){ 

}

// 0 à 1.99
function filtrageNote1(tabProd){ 

}

// 2 à 2.99
function filtrageNote2(tabProd){ 

}

// 3 à 3.99
function filtrageNote3(tabProd){ 

}

// 4 à 4.99
function filtrageNote4(tabProd){ 

}

// 5
function filtrageNote5(tabProd){ 

}


//TRANCHE DE PRIX

// 2.99 - 8.39
function filtrageTranchePrix1(tabProd){ 

}

// 8.40 - 13.19
function filtrageTranchePrix2(tabProd){ 

}

// 13.20 - 19.19
function filtrageTranchePrix3(tabProd){ 

}

// 19.20 - 31.19
function filtrageTranchePrix4(tabProd){ 

}

// 31.20 - 71.99
function filtrageTranchePrix5(tabProd){ 

}



