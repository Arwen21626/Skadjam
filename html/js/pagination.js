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
    console.log(tab.length)
    let parent = document.getElementById("listeProduit")

    // Si le tableau n'est pas vide
    if (tab.length > 0) {
        // Si les éléments n'existe pas encore on les créer
        if (!document.getElementById("premierePage") 
        && !document.getElementById("pagePrec") 
        && !document.getElementById("pageSuiv")
        && !document.getElementById("dernierePage")
        && !document.getElementById("pageInfo")){
            // Création des elts
            let changePage = document.createElement("div")
            let premPage = document.createElement("button")
            let pagePrec = document.createElement("button")
            let pageSuiv = document.createElement("button")
            let dernPage = document.createElement("button")
            let pageInfo = document.createElement("p")
            
            changePage.classList.add("flex", "flex-row", "justify-around", "w-96", "md:w-275", "m-3")
            parent.appendChild(changePage)
            parent = changePage

            // Ajout du contenu 
            premPage.textContent = "<<"
            premPage.id = 'premierePage'

            pagePrec.textContent = "|<"
            pagePrec.id = 'pagePrec'

            pageSuiv.textContent = ">|"
            pageSuiv.id = 'pageSuiv'

            dernPage.textContent = ">>"
            dernPage.id = 'dernierePage'

            pageInfo.id = 'pageInfo'

            // Ajout dans le document
            parent.appendChild(premPage)
            parent.appendChild(pagePrec)
            parent.appendChild(pageInfo)
            parent.appendChild(pageSuiv)
            parent.appendChild(dernPage)
        }
        // Sinon ils existent
        else{
            // S'ils sont cachés ont les réaffiches
            if (
            premPage.classList.contains("hidden") 
            && pagePrec.classList.contains("hidden")
            && pageSuiv.classList.contains("hidden")
            && dernPage.classList.contains("hidden")
            && pageInfo.classList.contains("hidden")
            ){
                premPage.classList.remove("hidden")
                pagePrec.classList.remove("hidden")
                pageSuiv.classList.remove("hidden")
                dernPage.classList.remove("hidden")
                pageInfo.classList.remove("hidden")
            } 
        }
    }
    // Sinon on affiche qu'il n'y a pas de produit
    else{
        // Si on trouve l'elt on enleve la classe hidden
        if (document.getElementById("aucunProd")) {
            aucunProd.classList.remove("hidden")
        }
        // Sinon on le créer
        else{
            // Création de l'affichage pour le cas ou il n'y a pas de produit
            let aucunProd = document.createElement("h2")
            aucunProd.id = 'aucunProd'
            aucunProd.textContent = "Aucun produit ne correspond à la recherche."
            parent.appendChild(aucunProd)
        }
        // Si la pagination est là on la cache
        console.log(premPage)
        if (premPage && pagePrec && pageSuiv && dernPage && pageInfo){
            premPage.classList.add("hidden")
            pagePrec.classList.add("hidden")
            pageSuiv.classList.add("hidden")
            dernPage.classList.add("hidden")
            pageInfo.classList.add("hidden")
        }
    }
}

