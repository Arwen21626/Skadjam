// CATEGORIE

// Alimentaire = 1
// Vetement = 2
// Artisanat = 3
// Goodies = 4
// Soin = 5


function filtrageCategorieAlimentaire(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 1)
    // console.log(newTab)
    return newTab
}

function filtrageCategorieVetement(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 2)
    return newTab
}

function filtrageCategorieArtisanat(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 3)
    return newTab
}

function filtrageCategorieGoodies(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 4)
    return newTab
}

function filtrageCategorieSoin(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 5)
    return newTab
}


// function filtrageCategorie(tab){
//     return tab
// }


// NOTE

function filtrageNote1(tabProd){

}

function filtrageNote2(tabProd){

}

function filtrageNote3(tabProd){

}

function filtrageNote4(tabProd){

}

function filtrageNote5(tabProd){

}


//TRANCHE DE PRIX

function filtrageTranchePrix1(tabProd){ // 2.99 - 8.39

}

function filtrageTranchePrix2(tabProd){ // 8.40 - 13.19

}

function filtrageTranchePrix3(tabProd){ // 13.20 - 19.19

}

function filtrageTranchePrix4(tabProd){ // 19.20 - 31.19

}

function filtrageTranchePrix5(tabProd){ // 31.20 - 71.99

}