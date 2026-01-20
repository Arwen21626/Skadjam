import * as Popup from "../popup.js";

const conteneurProd = document.getElementById("conteneur-produit");

if (conteneurProd) { //Chech si un élément de la page panier  est présent ou non pour éviter d'exécuter le script JS pour rien si la page est vide (cas panier vide)

    // Variable utilisé dans la fonction ci-dessous pour vérifier si le panier a déjà été modifié ou pas encore
    let hasChanged = false;

    //Définitions des fonctions
    function UpdatePanier(idProd, newQuantity, price) {

        const formPanier = document.getElementsByClassName("valider-panier")[0]; // Récupère directement le formulaire pour valider le panier
        const divFormPanier = document.getElementsByClassName("valider-panier-div")[0]; // Récupère la div remplaçant le formulaire

        if (formPanier) {

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
        else if (divFormPanier) {


            if (!hasChanged) {
                let btnForm = divFormPanier.querySelector('button');
                btnForm.textContent = "Valider les modifications";
                btnForm.classList.remove("bg-rouge");
                btnForm.classList.remove("text-gray-300")
                btnForm.classList.add("bg-beige");

                let formModifPanier = document.createElement('form');
                formModifPanier.action = "/php/modifier_panier.php";
                formModifPanier.method = "post";

                divFormPanier.removeChild(btnForm);
                formModifPanier.appendChild(btnForm);
                divFormPanier.appendChild(formModifPanier);

                hasChanged = true;
            }

            let formModifPanier = divFormPanier.querySelector('form');

            const inputName = 'produits[' + idProd + ']';

            let input = formModifPanier.querySelector('input[name="' + inputName + '[quantite]"]');

            if (!input) {

                input = document.createElement('input');
                input.type = 'hidden';
                input.name = inputName + '[quantite]';

                let inputPrix = document.createElement('input');
                inputPrix.type = 'hidden';
                inputPrix.name = inputName + '[prix]';
                inputPrix.value = price;

                formModifPanier.appendChild(input);
                formModifPanier.appendChild(inputPrix);
            }

            input.value = newQuantity;
        }
        
        
    }

    function showPopUpErreurPanier(idPopup, ms) {
        Popup.showPopUp(idPopup, ms);
    }

    /* Gestion de l'affichage des popups */

    // Affichage de la popup d'erreur de validation du panier
    const btnValiderPanier = document.getElementById("btnValiderPanier");

    if (btnValiderPanier) {
        
        btnValiderPanier.addEventListener("click", () => {
            showPopUpErreurPanier("popup-erreur-valider-panier", 5000);
        });
    }
    

    // Ajout des fonctions sur les boutons pour fermer les popups
    const btnClosePopUpInfo = document.getElementById("popup-modif-panier").querySelector("button");
    const btnClosePopUpErr = document.getElementById("popup-erreur-valider-panier").querySelector("button");

    btnClosePopUpInfo.addEventListener("click", () => {

        Popup.closePopup("popup-modif-panier");
    });

    btnClosePopUpErr.addEventListener("click", () => {
        Popup.closePopup("popup-erreur-valider-panier");
    })

    // Affiche la popup
    Popup.showPopUp("popup-modif-panier", 3000, "panierModif");

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




