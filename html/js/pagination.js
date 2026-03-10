const numberOfItems = 24
let first = 0
let actualPage = 1


function nbPages(tab){
    let maxPages = Math.ceil((tab.length)/numberOfItems)
    return maxPages
}

function firstPage(tab){
    first = 0
    actualPage = 1
    afficherListe(tab)
}

function lastPage(tab){
    first = (nbPages(tab) * numberOfItems)-numberOfItems;
    actualPage = nbPages(tab);
    afficherListe(tab); 
}

function pagePrecedente(tab){
    if(first-numberOfItems >= 0){
        first-=numberOfItems
        actualPage --;
        afficherListe(tab);
    }
}

function pageSuivante(tab){
    if(first+numberOfItems<tab.length){
        first+=numberOfItems;
        actualPage ++;
        afficherListe(tab);
    }
}

function numPageInfo(tab){
    let pageInfo = document.getElementById("pageInfo")
    pageInfo.textContent = (actualPage+"/"+nbPages(tab))
}

function affichagePagination(tab){

    let parent = document.getElementById("listeProduit")

    // On récupère les éléments s'ils existent
    let aucunProd = document.getElementById("aucunProd")
    let changePage = document.getElementById("changePage")

    let premPage = document.getElementById("premierePage")
    let pagePrec = document.getElementById("pagePrec")
    let pageSuiv = document.getElementById("pageSuiv")
    let dernPage = document.getElementById("dernierePage")
    let pageInfo = document.getElementById("pageInfo")

    // Création des éléments si nécessaire

    if (!aucunProd){
        aucunProd = document.createElement("h2")
        aucunProd.id = "aucunProd"
        aucunProd.textContent = "Aucun produit ne correspond à la recherche."
        parent.appendChild(aucunProd)
    }

    if (!changePage){

        changePage = document.createElement("div")
        changePage.id = "changePage"
        changePage.classList.add("flex", "flex-row", "justify-around", "w-96", "md:w-275", "m-3")
        parent.appendChild(changePage)

        // <<
        premPagebouton = document.createElement("button")
        premPage = document.createElement("img")
        premPage.id = "premierePage"
        premPage.src = '../../images/logo/bootstrap_icon/chevron-double-left.svg';
        premPage.alt = "Première page"
        premPage.title = "Première page"
        premPage.setAttribute("style", "cursor: pointer")
        premPage.classList.add("w-5", "h-5")
        premPagebouton.appendChild(premPage)

        // |<
        pagePrecbouton = document.createElement("button")
        pagePrec = document.createElement("img")
        pagePrec.id = "pagePrec"
        pagePrec.src = '../../images/logo/bootstrap_icon/chevron-bar-left.svg';
        pagePrec.alt = "Page précédente"
        pagePrec.title = "Page précédente"
        pagePrec.setAttribute("style","cursor: pointer")
        pagePrec.classList.add("w-5", "h-5")
        pagePrecbouton.appendChild(pagePrec)

        // x/x numéro de page
        pageInfo = document.createElement("p")
        pageInfo.id = "pageInfo"

        // >|
        pageSuivbouton = document.createElement("button")
        pageSuiv = document.createElement("img")
        pageSuiv.id = "pageSuiv"
        pageSuiv.src = '../../images/logo/bootstrap_icon/chevron-bar-right.svg';
        pageSuiv.alt = "Page suivante"
        pageSuiv.title = "Page suivante"
        pageSuiv.setAttribute("style","cursor: pointer")
        pageSuiv.classList.add("w-5", "h-5")
        pageSuivbouton.appendChild(pageSuiv)

        // >>
        dernPagebouton = document.createElement("button")
        dernPage = document.createElement("img")
        dernPage.id = "dernierePage"
        dernPage.src = '../../images/logo/bootstrap_icon/chevron-double-right.svg';
        dernPage.alt = "Dernière page"
        dernPage.title = "Dernière page"
        dernPage.setAttribute("style","cursor: pointer")
        dernPage.classList.add("w-5", "h-5")
        dernPagebouton.appendChild(dernPage)

        // ajout à la page
        changePage.appendChild(premPage)
        changePage.appendChild(pagePrec)
        changePage.appendChild(pageInfo)
        changePage.appendChild(pageSuiv)
        changePage.appendChild(dernPage)
    }

    // S'il y a des produits on affiche la pagination et on cache le message
    if (tab.length > 0){
        aucunProd.classList.add("hidden")
        changePage.classList.remove("hidden")
    } 
    // Sinon on fait l'inverse
    else {
        aucunProd.classList.remove("hidden")
        changePage.classList.add("hidden")
    }
}
 
