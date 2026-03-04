function afficherListe(tab) {
    //Remise à 0 de la page
    let parent = document.getElementById("prod")
    parent.innerHTML = ""
    
    // Affichage de la liste de produit
    for (let i = first; i < first + numberOfItems && i < tab.length; i++) {
        afficherProduit(tab,i)
    }

    window.scrollTo(0, 0)
    numPageInfo(tab)
}