var promoCheck = document.getElementById('promoCheck');
var seuilCheck = document.getElementById('seuilCheck');

const form = document.querySelector("form");
const inputDateDebut = document.getElementById("dateDebutPromotion");
const inputDateFin = document.getElementById("dateFinPromotion");
const erreurDebPromo = document.getElementById("erreurDebPromo");
const erreurFinPromo = document.getElementById("erreurFinPromo");
const formProduit = document.getElementById("formProduit");


// affichage des inputs
togglePromotionInputs();
toggleSeuilInput();

// Affichage des inputs selon les cases cochées
promoCheck.addEventListener('click', ()=>{togglePromotionInputs()});
seuilCheck.addEventListener('click', ()=>{toggleSeuilInput()});

// Validation du formulaire
form.addEventListener("keyup", function(e) {
    let valid = true;
    erreurDebPromo.classList.add("hidden");
    erreurFinPromo.classList.add("hidden");
    const promoCheck = document.getElementById("promoCheck");


    // Vérifier date de début
    if(inputDateDebut.value === '') {
        erreurDebPromo.textContent = "La date de début est obligatoire.";
        erreurDebPromo.classList.remove("hidden");
        valid = false;
    } 
    else if(formProduit !== null && estDateDansLePasse(inputDateDebut.value)) {
        erreurDebPromo.textContent = "La date de début ne peut pas être dans le passé.";
        erreurDebPromo.classList.remove("hidden");
        valid = false;
    }

    // Vérifier date de fin si renseignée
    if(inputDateFin.value !== '') {
        if(estDateDansLePasse(inputDateFin.value)) {
            erreurFinPromo.textContent = "La date de fin ne peut pas être dans le passé.";
            erreurFinPromo.classList.remove("hidden");
            valid = false;
        }

        // Date fin >= date début
        const debut = new Date(inputDateDebut.value);
        const fin = new Date(inputDateFin.value);
        if(fin < debut){
            erreurFinPromo.textContent = "La date de fin doit être après la date de début.";
            erreurFinPromo.classList.remove("hidden");
            valid = false;
        }
        
    }

    if(!valid){
        e.preventDefault();
    }

});

if(formProduit !== null){
    // gestion des erreur de la photo
    formProduit.addEventListener("submit", function(e) {
        const inputPhoto = document.getElementById("photo");
        const erreur = document.getElementById("erreurImage");
        if(inputPhoto.files.length === 0){
            e.preventDefault();
            erreur.classList.remove("hidden");
        }
    });

}

// Fonction pour afficher/cacher les inputs de promotion
function togglePromotionInputs(){
    var promoCheck = document.getElementById('promoCheck');
    var promoInputs = document.getElementById('promoInputs');
    var dateDebut = document.getElementById('dateDebutPromotion');

    if(promoCheck.checked && !promoCheck.disabled){
        promoInputs.style.display = 'flex';
        dateDebut.setAttribute("required", true);
    } else {
        promoInputs.style.display = 'none';
        dateDebut.removeAttribute("required");

    }
}

// Fonction pour afficher/cacher input de seuil d'alerte
function toggleSeuilInput(){
    var seuilCheck = document.getElementById('seuilCheck');
    var seuilInput = document.getElementById('seuilInput');
    var seuilAlerte = document.getElementById('seuilAlerte');

    if(seuilCheck.checked){
        seuilInput.style.display = 'flex';
        seuilAlerte.required = true;
    } else {
        seuilInput.style.display = 'none';
        seuilAlerte.required = false;
    }
}

// Fonction pour vérifier si une date est passée
function estDateDansLePasse(dateStr) {
    const today = new Date();
    today.setHours(0,0,0,0); // ignore l'heure
    const date = new Date(dateStr);
    return date < today;
}

