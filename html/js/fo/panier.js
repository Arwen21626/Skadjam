document.querySelectorAll('.produit').forEach(container => {
    // Récupères les éléments d'une carte produit
    const input = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('input');
    const btnRetrait = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.retrait');
    const btnAjout = container.querySelector('.prod-info').querySelector('.quantite-prod').querySelector('.ajout');

    const prixTTC = container.querySelector('.prod-info').querySelector('');

    btnRetrait.addEventListener('click', () => {

        if (input.value > 1) {
            input.value = input.value - 1;
        }
    });

    btnAjout.addEventListener('click', () => {

        input.value = Number(input.value) + 1;
    });

    let oldValue = input.value;

    input.addEventListener('input', () => {
        const filtered = input.value.replace(/\D/g, '');

        if (filtered === '' || filtered === '0') {
            input.value = oldValue;
        }
        else {
            input.value = filtered;
            oldValue = filtered;
        }
    });

    input.addEventListener('focus', () => {
        input.select();
    });
});