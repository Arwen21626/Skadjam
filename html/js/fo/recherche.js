const numberOfItems = 24 //NB produits à afficher
let first = 0
let actualPage

let tableau = []

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
    let categorieVetement = document.getElementById("vetement")
    let categorieArtisanat = document.getElementById("artisanat")
    let categorieGoodies = document.getElementById("goodies")
    let categorieSoin = document.getElementById("soin")
    let categorieAlimentaire = document.getElementById("alimentaire")
        // Notes
    let noteZeroE = document.getElementById("zeroE")
    let noteUneE = document.getElementById("uneE")
    let noteDeuxE = document.getElementById("deuxE")
    let noteTroisE = document.getElementById("troisE")
    let noteQuatreE = document.getElementById("quatreE")
    let noteCinqE = document.getElementById("cinqE")
        // Tranches de prix
    let tranchePrix1 = document.getElementById("prix1")
    let tranchePrix2 = document.getElementById("prix2")
    let tranchePrix3 = document.getElementById("prix3")
    let tranchePrix4 = document.getElementById("prix4")
    let tranchePrix5 = document.getElementById("prix5")

    // Fonctions de filtres
        //Catégories
    categorieAlimentaire.addEventListener("click",function () {
        tableau = filtrageCategorieAlimentaire(tableau);
        afficherListe()
    })

    categorieVetement.addEventListener("click",function () {
        tableau = filtrageCategorieVetement(tableau);
        afficherListe()
    })

    categorieArtisanat.addEventListener("click",function () {
        tableau = filtrageCategorieArtisanat(tableau);
        afficherListe()
    })

    categorieGoodies.addEventListener("click",function () {
        tableau = filtrageCategorieGoodies(tableau);
        afficherListe()
    })

    categorieSoin.addEventListener("click",function () {
        tableau = filtrageCategorieSoin(tableau);
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
        // sidebar.classList.remove("translate-x-0")
        // sidebar.classList.add("hidden")
        // boutonSidebar.classList.remove("hidden")
        // prod.classList.remove("ml-4", "w-4/5")
        // listeProd.classList.remove("items-end")
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
    produit.classList.add("bg-bleu", "grid", "grid-cols-[40%_60%]", "w-40", "md:w-80", "h-auto", "p-2", "md:p-3", "m-2")
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
    parent.appendChild(image)

    // Nom produit
    let nom = document.createElement("p")
    nom.textContent = tableau[i]['libelle_produit']
    parent.appendChild(nom)
    nom.classList.add("col-span-2")

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




















//  Récupération des tris cochés
// function recupTri(){
//     let tabChecked = []
//     document.querySelectorAll('input[type="radio"]').forEach(radio => {
//         radio.addEventListener('change', () => {
//             // Si la valeur du radio bouton est 'croissant' et que 'decroissant' est déjà dans le tableau
//             // on le remplace
//             if ((radio.value == "croissant" && tabChecked.includes("decroissant"))) {
//                 tabChecked.splice(tabChecked.indexOf("decroissant"),1,radio.value)
//             }
//             // Si la valeur du radio bouton est 'decroissant' et que 'croissant' est déjà dans le tableau
//             // on le remplace
//             else if(radio.value == "decroissant" && tabChecked.includes("croissant")) {
//                 tabChecked.splice(tabChecked.indexOf("croissant"),1,radio.value)
//             }
//             // Si la valeur du radio bouton est 'az' et que 'za' est déjà dans le tableau
//             // on le remplace
//             else if(radio.value == "az" && tabChecked.includes("za")) {
//                 tabChecked.splice(tabChecked.indexOf("za"),1,radio.value)
//             }
//             // Si la valeur du radio bouton est 'za' et que 'az' est déjà dans le tableau
//             // on le remplace
//             else if(radio.value == "za" && tabChecked.includes("az")) {
//                 tabChecked.splice(tabChecked.indexOf("az"),1,radio.value)
//             }
//             // Si la valeur du radio bouton est '51' et que '15' est déjà dans le tableau
//             // on le remplace
//             else if(radio.value == "51" && tabChecked.includes("15")) {
//                 tabChecked.splice(tabChecked.indexOf("15"),1,radio.value)
//             }
//             // Si la valeur du radio bouton est '15' et que '51' est déjà dans le tableau
//             // on le remplace
//             else if(radio.value == "15" && tabChecked.includes("51")) {
//                 tabChecked.splice(tabChecked.indexOf("51"),1,radio.value)
//             }
//             // Sinon on ajoute la valeur
//             else{
//                 tabChecked.push(radio.value)
//             }
//             //console.log(tabChecked)
//             return tabChecked
//         });
        
//     });
// }

// // Récupération des filtres cochés
// function recupFiltre(){
//     let tabChecked = []
//     document.querySelectorAll('input[type="checkbox"]').forEach(box => {
//         box.addEventListener('change', () => {
//             // Si le filtre n'est pas dans la liste on l'ajoute
//             if ((!tabChecked.includes(box.value))) {
//                 tabChecked.push(box.value)
                
//             }
//             else{
//                 tabChecked.splice(tabChecked.indexOf(box.value))
//             }
//             console.log(tabChecked)
//             return tabChecked
//         });
//     });
// }

// // Récupération et modification de l'URL
// function envoiTriUrl(){
//     let tab = recupTri()
//     console.log(tab)
//     let envoi = []

//     if (tab.includes("croissant")){
//         envoi.push("croissant=true")
//     }
//     else if (tab.includes("decroissant")){
//         envoi.push("decroissant=true")
//     }

//     if (tab.includes("az")){
//         envoi.push("az=true")
//     }
//     else if (tab.includes("za")){
//         envoi.push("za=true")
//     }

//     if (tab.includes("15")){
//         envoi.push("15=true")
//     }
//     else if (tab.includes("51")){
//         envoi.push("51=true")
//     }
//     return envoi
// }

// function ecrireURL(){
    
//     let tab = envoiTriUrl()
//     //console.log(tab)

//     if (tab.length === 0){ return ""}

//     let rep ="?" + tab[0]

//     if(tab.length > 1){
//         for (let i = 1; i < tab.length; i++) {
//             rep += "&"+tab[i]
//         }
//     }
//     window.history.pushState({}, "", rep)
//     return rep
// }

// function recupURL(){
//     let params = []

//     var parts = window.location.search.substr(1).split("&");
//     for (var i = 0; i < parts.length; i++) {
//         var attribut = parts[i].split("=");
//         params.push(attribut)
//         //console.log(params)
//     }
    
//     return params
// }

// Uilisation de l'URL pour cocher les cases d'une page à l'autre


// Changement de pages
// function calculNbPages(tableau){
//     //initialisation du numéro de page
//     const PAGE_SIZE = 15
//     let pageNumber
//     // Récupération dans l'url
//     page = recupURL()
    

//     if(page[0] == ''){
//         pageNumber = 1 
//     }else{
//         pageNumber = page[1]
//     }

//     //console.log($_GET['id']); // Affiche la valeur du paramètre 'id'   

//     let maxPage = (tableau.length)/PAGE_SIZE

//     let start = (pageNumber - 1) * PAGE_SIZE;
//     let end = pageNumber * PAGE_SIZE;

//     let lignes = tableau.slice(start, end);
    
//     return [lignes, pageNumber, maxPage]
// }

// function changementPage(){
//     // Passage d'une page à l'autre
//     parent = document.getElementsByTagName("main")[0]
//     // Pour avoir seulement le main et pas le tableau renvoyé
//     let pageChangement = document.createElement("div")
//     pageChangement.classList.add("flex", "flex-row", "space-x-4", "justify-center")
//     parent.appendChild(pageChangement)

//     parent = pageChangement

//     // let temp = calculNbPages(tableau)[0]
    
//     let pageNumber = calculNbPages(tableau)[1]
//     let maxPage = calculNbPages(tableau)[2]
    
//     if(pageNumber > 1){
//         let pagePrec = document.createElement("a")
//         pagePrec.href = "recherche.php?page="+(pageNumber-1)+"#nosProduits"

//         pagePrec.textContent = "Page précédente"
//         pagePrec.classList.add("lienPage","hover:text-rouge")

//         parent.appendChild(pagePrec)

//         pagePrec.addEventListener("click", ecrireURL)
//     }

//     if (pageNumber < maxPage){
//         let pageSuiv = document.createElement("a")
//         let pageSup = parseInt(pageNumber)+1
//         pageSuiv.href = "recherche.php?page="+(pageSup)+"#nosProduits"

//         pageSuiv.textContent = "Page suivante"
//         pageSuiv.classList.add("lienPage","hover:text-rouge")

//         parent.appendChild(pageSuiv)

//         pageSuiv.addEventListener("click", ecrireURL)
//     }
// }