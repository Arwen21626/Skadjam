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

