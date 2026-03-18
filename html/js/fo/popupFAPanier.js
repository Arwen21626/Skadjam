import * as Popup from "../popup.js";
const urlParams = new URLSearchParams(window.location.search)

let popupAddPanier = document.getElementById("popup-ajouter-panier")
let popupRemovePanier = document.getElementById("popup-retirer-panier")
let popupAddFA = document.getElementById("popup-ajouter-fa")
let popupRemoveFA = document.getElementById("popup-retirer-fa")

let btnOK = ''

// Gestion de l'ouverture et de la fermeture des popups
if (urlParams.has("addPanier")){
    Popup.showPopUp("popup-ajouter-panier", 5000, "addPanier")
    btnOK = popupAddPanier.querySelector("button")

    btnOK.addEventListener("click", () => {
        Popup.closePopup("popup-ajouter-panier");
    })
}
else if (urlParams.has("removePanier")){
    Popup.showPopUp("popup-retirer-panier", 5000, "removePanier")

    btnOK = popupRemovePanier.querySelector("button")

    btnOK.addEventListener("click", () => {
        Popup.closePopup("popup-retirer-panier")
    })
}
else if (urlParams.has("addFA")){
    Popup.showPopUp("popup-ajouter-fa", 5000, "addFA")

    btnOK = popupAddFA.querySelector("button")

    btnOK.addEventListener("click", () => {
        Popup.closePopup("popup-ajouter-fa")
    })
}
else if (urlParams.has("removeFA")){
    Popup.showPopUp("popup-retirer-fa", 5000, "removeFA")

    btnOK = popupRemoveFA.querySelector("button")

    btnOK.addEventListener("click", () => {
        Popup.closePopup("popup-retirer-fa")
    })
}

//Récup de la fenetre a affiché
let contNbAddPanier = document.getElementById("contNbAddPanier")
let fondNbAddPanier = document.getElementById("fondNbAddPanier")
let formNbAddPanier = document.getElementById("formNbAddPanier")
let input = document.createElement("input")
let btnRetour = document.getElementById("btnRetour")
input.type = "hidden"

// Affichage dans la fenetre
let p = document.getElementById("valideRetrait")
let label = document.getElementById("validAjout")
let inputNb = document.getElementById("nbAddPanier")

//Récup des cartes produits
let carteProduit = document.querySelectorAll(".carteProduit")

//Variable pour la boucle
let btnPanier = ''
let id = -1 
let stock = -1

//Eventlistener sur le bouton retour
btnRetour.addEventListener('click', function(){
    contNbAddPanier.classList.add("hidden")
    fondNbAddPanier.classList.add("hidden")
})
// Dans AjoutEventListener() pour la page recherche

carteProduit.forEach(produit => {
    //Ajout de l'event listener sur le bouton
    btnPanier = produit.querySelector(".btnPanier")
    btnPanier.addEventListener('click', function () {

        id = produit.id
        stock = produit.querySelector("p").textContent

        if (stock > 0) {
            if (this.className.includes("cart-fill-vert-fonce.svg")) {
                p.classList.remove("hidden")
                label.classList.add("hidden")
                inputNb.classList.add("hidden")
                inputNb.required = false
            } else {
                p.classList.add("hidden")
                label.classList.remove("hidden")
                inputNb.classList.remove("hidden")
                inputNb.required = true
            }

            input.value = id
            input.name = "idProduit"
            formNbAddPanier.appendChild(input)

            contNbAddPanier.classList.remove("hidden")
            fondNbAddPanier.classList.remove("hidden")
        }
    })
})
