const numberOfItems = 24
let first = 0
let actualPage = 1

function nbPages(){
    let maxPages = Math.ceil((tableau.length)/numberOfItems)
    return maxPages
}

function firstPage(){
    first = 0
    actualPage = 1
    afficherListe()
}

function lastPage(){
    first = (nbPages() * numberOfItems)-numberOfItems;
    actualPage = nbPages();
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

function numPageInfo(){
    let pageInfo = document.getElementById("pageInfo")
    pageInfo.textContent = (actualPage+"/"+nbPages())
}