// CATEGORIE

// Alimentaire = 1
function filtrageCategorieAlimentaire(tabProd){ 
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 1)
    // console.log(newTab)
    return newTab
}

// Vetement = 2
function filtrageCategorieVetement(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 2)
    return newTab
}

// Artisanat = 3
function filtrageCategorieArtisanat(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 3)
    return newTab
}

// Goodies = 4
function filtrageCategorieGoodies(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 4)
    return newTab
}

// Soin = 5
function filtrageCategorieSoin(tabProd){
    newTab = tabProd.filter(tabProd => tabProd['id_categorie'] === 5)
    return newTab
}


// function filtrageCategorie(tab){
//     return tab
// }



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

function filtrageTranchePrix2(tabProd){ // 8.40 - 13.19

}

function filtrageTranchePrix3(tabProd){ // 13.20 - 19.19

}

function filtrageTranchePrix4(tabProd){ // 19.20 - 31.19

}

function filtrageTranchePrix5(tabProd){ // 31.20 - 71.99

}