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
    afficherListe(appliquerTri(tab))

    document.getElementById("nosProduits").scrollIntoView();
}

function lastPage(tab){
    first = (nbPages(tab) * numberOfItems)-numberOfItems;
    actualPage = nbPages(tab);
    afficherListe(appliquerTri(tab));

    document.getElementById("nosProduits").scrollIntoView();
}

function pagePrecedente(tab){
    if(first-numberOfItems >= 0){
        first-=numberOfItems
        actualPage --;
        afficherListe(appliquerTri(tab));

        document.getElementById("nosProduits").scrollIntoView();
    }
}

function pageSuivante(tab){
    if(first+numberOfItems<tab.length){
        first+=numberOfItems;
        actualPage ++;
        afficherListe(appliquerTri(tab));
        
        document.getElementById("nosProduits").scrollIntoView();
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

        premPage = document.createElement("button")
        premPage.id = "premierePage"
        premPage.textContent = "<<"
        premPage.setAttribute("style","cursor: pointer;")

        pagePrec = document.createElement("button")
        pagePrec.id = "pagePrec"
        pagePrec.textContent = "|<"
        pagePrec.setAttribute("style","cursor: pointer;")

        pageInfo = document.createElement("p")
        pageInfo.id = "pageInfo"

        pageSuiv = document.createElement("button")
        pageSuiv.id = "pageSuiv"
        pageSuiv.textContent = ">|"
        pageSuiv.setAttribute("style","cursor: pointer;")

        dernPage = document.createElement("button")
        dernPage.id = "dernierePage"
        dernPage.textContent = ">>"
        dernPage.setAttribute("style","cursor: pointer;")

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
 
