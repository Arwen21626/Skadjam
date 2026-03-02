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

function triStockCroissant(tableau){
    return tableau.sort((a,b) => a['quantite_stock'] - b['quantite_stock'])
}

function triStockDecroissant(tableau){
    return tableau.sort((a,b) => b['quantite_stock'] - a['quantite_stock'])
}