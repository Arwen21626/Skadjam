var checkedCategories = []
var checkedNotes = []
var checkedTranches = []
var checkedVendeurs = []
var checkedVendeursMap = []

function toggleFiltre(tab, valeur) {
    if (tab.includes(valeur)) {
        tab.splice(tab.indexOf(valeur), 1)
    } else {
        tab.push(valeur)
    }
    first = 0
    actualPage = 1
    tab = filtre()

    // Gestion nombre produit avec filtre
    if (document.getElementById("nbProd")) {
        let nbProduit = document.getElementById("nbProd")
        nbProduit.textContent = "Nbre produit(s): "+tab.length
    }
    return tab
}

function filtre(){

    tab = JSON.parse(JSON.stringify(tabProd))
    
    // FILTRE CATÉGORIES
    if (checkedCategories.length > 0) {
        tab = tab.filter(prod => {
            if (checkedCategories.includes("alimentaire") && prod.id_categorie === 1) return true
            if (checkedCategories.includes("vetement") && prod.id_categorie === 2) return true
            if (checkedCategories.includes("artisanat") && prod.id_categorie === 3) return true
            if (checkedCategories.includes("goodies") && prod.id_categorie === 4) return true
            if (checkedCategories.includes("soin") && prod.id_categorie === 5) return true
            return false
        })
    }

    // FILTRE NOTES
    if (checkedNotes.length > 0) {
        tab = tab.filter(prod => {
            let note = prod.note_moyenne

            if (note === null) return checkedNotes.includes("0")

            note = parseFloat(note);

            if (checkedNotes.includes("1") && note < "2") return true
            if (checkedNotes.includes("2") && note >= "2" && note < "3") return true
            if (checkedNotes.includes("3") && note >= "3" && note < "4") return true
            if (checkedNotes.includes("4") && note >= "4" && note < "5") return true
            if (checkedNotes.includes("5") && note == "5") return true

            return false
        })
    }

    // FILTRE TRANCHE DE PRIX
    if (checkedTranches.length > 0) {
        tab = tab.filter(prod => {
            let prix = parseFloat(prod.prix_ttc)

            if (checkedTranches.includes("prix1") && prix >= "2.99" && prix <= "8.39") return true
            if (checkedTranches.includes("prix2") && prix >= "8.40" && prix <= "13.19") return true
            if (checkedTranches.includes("prix3") && prix >= "13.20" && prix <= "19.19") return true
            if (checkedTranches.includes("prix4") && prix >= "19.20" && prix <= "31.19") return true
            if (checkedTranches.includes("prix5") && prix >= "31.20" && prix <= "71.99") return true
            return false
        })
    }

    // FILTRE VENDEURS
    if (checkedVendeurs.length > 0) {
        tab = tab.filter(vendeur => {
            for (let i = 0; i < checkedVendeurs.length; i++) {
                if (vendeur.id_compte == checkedVendeurs[i]) return true
            }
            return false
        })
    }

    // FILTRE MAP
    if (checkedVendeursMap.length > 0) {
        tab = tab.filter(vendeur => {
            if (vendeur.id_compte == checkedVendeursMap[i]) return true
            return false
        })
    }

    return tab
}