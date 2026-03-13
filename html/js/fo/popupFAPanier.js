import * as Popup from "../popup.js";
const urlParams = new URLSearchParams(window.location.search)

let popupAddPanier = document.getElementById("popup-ajouter-panier")
let popupRemovePanier = document.getElementById("popup-retirer-panier")
let popupAddFA = document.getElementById("popup-ajouter-fa")
let popupRemoveFA = document.getElementById("popup-retirer-fa")

let btnOK = ''

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
