/* Question prochain DM ou autre : est ce que le prix remise peut être tout le 
    temps utilisé pour calc le prix tot ?
*/

import * as Popup from "../popup.js";

const btnClosePopUp = document.getElementById("popup-modif-panier").querySelector("button");

btnClosePopUp.addEventListener("click", () => {

    Popup.closePopup("popup-modif-panier");
});

Popup.showPopUp("popup-modif-panier", 3000, "panierModif");

// Gestion de la sauvegarde des modifications dans la BDD

const formPanier = document.getElementsByClassName("valider-panier")[0];

if (formPanier) { //Chech si le formulaire de validation du panier (un élément de la page panier quand il n'est pas vide) est présent ou non
    // Variable utilisé dans la fonction ci-dessous pour vérifier si le panier a déjà été modifié ou pas encore
    let hasChanged = false;

    function UpdatePanier(idProd, newQuantity, price) {

        if (!hasChanged) {
            formPanier.querySelector('button').textContent = "Valider les modifications";
            formPanier.action = "/php/modifier_panier.php";
            formPanier.method = "post";
            hasChanged = true;
        }

        const inputName = 'produits[' + idProd + ']';

        let input = formPanier.querySelector('input[name="' + inputName + '[quantite]"]');

        if (!input) {

            input = document.createElement('input');
            input.type = 'hidden';
            input.name = inputName + '[quantite]';

            let inputPrix = document.createElement('input');
            inputPrix.type = 'hidden';
            inputPrix.name = inputName + '[prix]';
            inputPrix.value = price;

            formPanier.appendChild(input);
            formPanier.appendChild(inputPrix);
        }

        input.value = newQuantity;
        
    }


    // Sous total du panier
    let sousTotal = document.getElementById('conteneur-info_panier').querySelector('.sous-total').getElementsByTagName('p')[1];
    // Nombre de produit total contenu dans le panier
    let nbProdTot = document.getElementById('conteneur-info_panier').querySelector('.nb-prod-total').getElementsByTagName('p')[1];

    document.querySelectorAll('.produit').forEach(container => {
        
        // Récupères les éléments d'une carte produit
        let input = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('input'); // Input de la quantité
        let btnRetrait = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.retrait');
        let btnAjout = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.ajout');

        let btnSupprimer = container.querySelector('.prod-info').querySelector('.supprimer');

        let idProduit = container.id;

        // Récupères les éléments des prix d'un produit
        let prixTTC = Number(container.querySelector('.prod-info').querySelector('.prix').querySelector('.prix-u').textContent.replace('€', '').replace(',', '.'));
        let prixTot = container.querySelector('.prod-info').querySelector('.prix').querySelector('.prix-tot'); //Prix total d'un produit

        let lenClassList = container.querySelector('.prod-info').classList.length //Récupère la longueur de la liste des class de la div représentant les infos d'un produit
        let quantiteStock = Number(container.querySelector('.prod-info').classList[lenClassList - 1].split(':', 2)[1]);

        let oldValue = input.value;

        btnRetrait.addEventListener('click', () => {

            if (input.value > 1) {
                input.value = Number(input.value) - 1;
                prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
                nbProdTot.textContent = Number(nbProdTot.textContent) - 1;
                sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',', '.')) - prixTTC).toFixed(2) + "€").replace('.', ',');
                oldValue = input.value;

                UpdatePanier(idProduit, Number(input.value), prixTTC);
            }
        });

        btnAjout.addEventListener('click', () => {

            if (Number(input.value) < quantiteStock) {
                input.value = Number(input.value) + 1;
                prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
                nbProdTot.textContent = Number(nbProdTot.textContent) + 1;
                sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',', '.')) + prixTTC).toFixed(2) + "€").replace('.', ',');
                oldValue = input.value;

                UpdatePanier(idProduit, Number(input.value), prixTTC);
            }

        });

        btnSupprimer.addEventListener('click', () => {

            UpdatePanier(idProduit, 0, prixTTC);
            nbProdTot.textContent = Number(nbProdTot.textContent) - Number(input.value);
            sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',', '.')) - (Number(input.value) * prixTTC)).toFixed(2) + "€").replace('.', ',');
            container.remove();
        });

        input.addEventListener('input', () => {
            let filtre = input.value.replace(/[^0-9]*/g, ''); // Empêche la saisie de tout caractères autres que 0-9

            if (input.value != filtre) {
                input.value = filtre;
            }

            if (input.value !== '' && input.value[0] !== '0') {
                prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
            }
            else {
                prixTot.textContent = ("Prix total : " + prixTTC.toFixed(2) + "€").replace('.', ',');
            }

            if (Number(input.value) >= quantiteStock) {
                input.value = quantiteStock;
                prixTot.textContent = ("Prix total : " + (prixTTC * quantiteStock).toFixed(2) + "€").replace('.', ',');
            }
        });

        input.addEventListener('focus', () => {
            input.select();
        });

        input.addEventListener('keydown', e => {

            if (e.key === "Enter") {
                input.blur();
            }
        })

        input.addEventListener('blur', () => {

            if (input.value === '' || input.value[0] === '0') {
                input.value = oldValue;
                prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
            }
            else {
                nbProdTot.textContent = Number(nbProdTot.textContent) + (Number(input.value) - Number(oldValue));
                sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',', '.')) + ((Number(input.value) * prixTTC) - (Number(oldValue) * prixTTC))).toFixed(2) + "€").replace('.', ',');

                oldValue = input.value;

                UpdatePanier(idProduit, Number(input.value), prixTTC);
            }
        })
    });
}




