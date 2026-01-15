import "./../variables.js";
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
    categorieAlimentaire.addEventListener("click",function () {
        if(categorieAlimentaire.checked){
            checkedCategories.push("alimentaire")
            tableau = filtrageCategorieAlimentaire(tabProd)
            actualPage = 1
            first = 0
            
        }else{
            checkedCategories.pop("alimentaire")
        }
        afficherListe()
    })

    
    categorieVetement.addEventListener("click",function () {
        if(categorieVetement.checked){
            checkedCategories.push("vetement")
            tableau = filtrageCategorieVetement(tabProd)
            actualPage = 1
            first = 0
        }else{
            checkedCategories.pop("vetement")
        }
        afficherListe()
    })

    
    categorieArtisanat.addEventListener("click",function () {
        if(categorieArtisanat.checked){
            checkedCategories.push("artisanat")
            tableau = filtrageCategorieArtisanat(tabProd)
            actualPage = 1
            first = 0
        }else{
            checkedCategories.pop("artisanat")
        }
        afficherListe()
    })

    
    categorieGoodies.addEventListener("click",function () {
        if(categorieGoodies.checked){
            checkedCategories.push("goodies")
            tableau = filtrageCategorieGoodies(tabProd)
            actualPage = 1
            first = 0
        }else{
            checkedCategories.pop("goodies")
        }
        afficherListe()
    })

    
    categorieSoin.addEventListener("click",function () {
        if(categorieSoin.checked){
            checkedCategories.push("soin")
            tableau = filtrageCategorieSoin(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedCategories.pop("soin")
        }
        afficherListe()
    })
    
        // Note

    noteNonNote.addEventListener("click",function () {
        if(noteNonNote.checked){
            checkedNotes.push("0")
            tableau = filtrageNoteNonNote(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedNotes.pop("0")
        }
        afficherListe()
    })

    noteUneE.addEventListener("click",function () {
        if(noteUneE.checked){
            checkedNotes.push("1")
            tableau = filtrageNote1(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedNotes.pop("1")
        }
        afficherListe()
    })

    noteDeuxE.addEventListener("click",function () {
        if(noteDeuxE.checked){
            checkedNotes.push("2")
            tableau = filtrageNote2(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedNotes.pop("2")
        }
        afficherListe()
    })

    noteTroisE.addEventListener("click",function () {
        if(noteTroisE.checked){
            checkedNotes.push("3")
            tableau = filtrageNote3(tabProd);
            actualPage = 1
            first = 0            
        }else{
            checkedNotes.pop("3")
        }
        afficherListe()
    })
    
    noteQuatreE.addEventListener("click",function () {
        if(noteQuatreE.checked){
            checkedNotes.push("4")
            tableau = filtrageNote4(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedNotes.pop("4")
        }
        afficherListe()
    })

    noteCinqE.addEventListener("click",function () {
        if(noteCinqE.checked){
            checkedNotes.push("5")
            tableau = filtrageNote5(tabProd);
            actualPage = 1
            first = 0
        }else{
            checkedNotes.pop("5")
        }
        afficherListe()
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

// Affichage
function afficherListe(){
    
    if(checkedCategories.length === 0 && checkedNotes.length === 0 /*&& checkedTranches.length === 0*/){
        tableau = tabProd
        actualPage = 1
        first = 0
    }else{
        
        for(let i=0; i<checkedCategories.length; i++){
            if(checkedCategories[i] === "alimentaire" && !ali){
                tableau = tableau.concat(filtrageCategorieAlimentaire(tabProd));
                ali = true
            }
            if(checkedCategories[i] === "vetement" && !vet){
                tableau = tableau.concat(filtrageCategorieVetement(tabProd));
                vet = true
            }
            if(checkedCategories[i] === "artisanat" && !art){
                tableau = tableau.concat(filtrageCategorieArtisanat(tabProd));
                art = true
            }
            if(checkedCategories[i] === "goodies" && !goo){
                tableau = tableau.concat(filtrageCategorieGoodies(tabProd));
                goo = true
            }
            if(checkedCategories[i] === "soin" && !soi){
                tableau = tableau.concat(filtrageCategorieSoin(tabProd));
                soi = true
            }
        }

        for(let j=0; j<checkedNotes.length; j++){
            if(checkedNotes[j] === "0" && !not0){
                tableau = tableau.concat(filtrageNoteNonNote(tabProd));
                not0 = true;
            }
            if(checkedNotes[j] === "1" && !not1){
                tableau = tableau.concat(filtrageNote1(tabProd));
                not1 = true;
            }
            if(checkedNotes[j] === "2" && !not2){
                tableau = tableau.concat(filtrageNote2(tabProd));
                not2 = true;
            }
            if(checkedNotes[j] === "3" && !not3){
                tableau = tableau.concat(filtrageNote3(tabProd));
                not3 = true;
            }
            if(checkedNotes[j] === "4" && !not4){
                tableau = tableau.concat(filtrageNote4(tabProd));
                not4 = true;
            }
            if(checkedNotes[j] === "5" && !not5){
                tableau = tableau.concat(filtrageNote5(tabProd));
                not5 = true;
            }
        }
    }

    let parent = document.getElementById("prod")
    parent.innerHTML = ""
    for(let i = first; i < first + numberOfItems;i++){
        if(i<tableau.length){
            afficherProduit(i)
        }
    }
    numPageInfo()
}


function afficherProduit(indice){
    
    let i = indice
    console.log(tableau[i])
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
function triPrixCroissant(tableau){
    return tableau.sort((a, b) => parseInt(a['prix_ttc']) - parseInt(b['prix_ttc']))
}

function triPrixDecroissant(tableau){
    return tableau.sort((a, b) => parseFloat(b['prix_ttc']) - parseFloat(a['prix_ttc']))
}

function triAz(tableau){
    return tableau.sort((a,b) => a['libelle_produit'].localeCompare(b['libelle_produit']))
}

function triZa(tableau){
    return tableau.sort((a,b) => b['libelle_produit'].localeCompare(a['libelle_produit']))
}

function triEtoileCroissant(tableau){
    return tableau.sort((a,b) => parseFloat(a['note_moyenne']) - parseFloat(b['note_moyenne']))
}

function triEtoileDecroissant(tableau){
    return tableau.sort((a,b) => parseFloat(b['note_moyenne']) - parseFloat(a['note_moyenne']))
}

// Filtres

// Alimentaire = 1
function filtrageCategorieAlimentaire(tableau){ 
    newTab = tableau.filter(tabProd => tabProd['id_categorie'] === 1)
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
function filtrageNoteNonNote(tableau){ 
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] === null)
    return newTab
}

// 0 à 1.99
function filtrageNote1(tableau){ 
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] >= "0.0" && tabProd['note_moyenne'] < "2")
    return newTab
}

// 2 à 2.99
function filtrageNote2(tableau){ 
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] >= "2.0" && tabProd['note_moyenne'] < "3")
    return newTab
}

// 3 à 3.99
function filtrageNote3(tableau){
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] >= "3.0" && tabProd['note_moyenne'] < "4")
    return newTab
}

// 4 à 4.99
function filtrageNote4(tableau){
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] >= "4.0" && tabProd['note_moyenne'] < "5")
    return newTab
}

// 5
function filtrageNote5(tableau){
    newTab = tableau.filter(tabProd => tabProd['note_moyenne'] === "5.0")
    return newTab
}


//TRANCHE DE PRIX

// 2.99 - 8.39
function filtrageTranchePrix1(tableau){ 

}

// 8.40 - 13.19
function filtrageTranchePrix2(tableau){ 

}

// 13.20 - 19.19
function filtrageTranchePrix3(tableau){ 

}

// 19.20 - 31.19
function filtrageTranchePrix4(tableau){ 

}

// 31.20 - 71.99
function filtrageTranchePrix5(tableau){ 

}



