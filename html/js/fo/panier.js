// Sous total du panier
let sousTotal = document.getElementById('conteneur-info_panier').querySelector('.sous-total').getElementsByTagName('p')[1];
// Nombre de produit total contenu dans le panier
let nbProdTot = document.getElementById('conteneur-info_panier').querySelector('.nb-prod-total').getElementsByTagName('p')[1];

document.querySelectorAll('.produit').forEach(container => {
    // Récupères les éléments d'une carte produit
    let input = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('input'); // Input de la quantité
    let btnRetrait = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.retrait');
    let btnAjout = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.ajout');

    // Récupères les éléments des prix d'un produit
    let prixTTC = Number(container.querySelector('.prod-info').querySelector('.prix').querySelector('.prix-u').textContent.slice(17, -1).replace(',', '.'));
    let prixTot = container.querySelector('.prod-info').querySelector('.prix').querySelector('.prix-tot'); //Prix total d'un produit

    let oldValue = input.value;

    btnRetrait.addEventListener('click', e => {

        if (input.value > 1) {
            input.value = Number(input.value) - 1;
            prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
            nbProdTot.textContent = Number(nbProdTot.textContent) - 1;
            sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',','.')) - prixTTC).toFixed(2) + "€").replace('.', ',');
            oldValue = input.value;
        }
    });

    btnAjout.addEventListener('click', e => {
        input.value = Number(input.value) + 1;
        prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
        nbProdTot.textContent = Number(nbProdTot.textContent) + 1;
        sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',','.')) + prixTTC).toFixed(2) + "€").replace('.', ',');
        oldValue = input.value;
    });

    input.addEventListener('input', () => {
        let filtre = input.value.replace(/[^0-9]*/g, ''); // Empêche la saisie de tout caractères autres que 0-9

        if (input.value != filtre) {
            input.value = filtre;
        }

        if (input.value !== '' && input.value[0] !== '0'){
            prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
        }
        else
        {
            prixTot.textContent = ("Prix total : " + prixTTC.toFixed(2) + "€").replace('.', ',');
        }
    });

    input.addEventListener('focus', () => {
        input.select();
    });

    input.addEventListener('blur', () => {
        if (input.value === '' || input.value[0] === '0') {
            input.value = oldValue;
            prixTot.textContent = ("Prix total : " + (prixTTC * Number(input.value)).toFixed(2) + "€").replace('.', ',');
        }
        else {
            nbProdTot.textContent = Number(nbProdTot.textContent) + (Number(input.value) - Number(oldValue));
            sousTotal.textContent = ((Number(sousTotal.textContent.slice(0, -1).replace(',', '.')) + ((Number(input.value) * prixTTC) - (Number(oldValue) * prixTTC))).toFixed(2) + "€").replace('.', ',');

            oldValue = input.value;
        }
    })
});