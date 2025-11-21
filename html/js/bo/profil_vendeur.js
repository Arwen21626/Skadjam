//initialisation des variables
let valider = false;
let ancienEtatValider = false;
let nbModifActive = 0;
let globaleEtatChamp = false;
let newEtatChamp = false;
let ancienTexte = "";
let texte = "";

//initialisation des éléments
const boutonValider = document.getElementById("valider");
const labelImage = document.querySelector(".label-image");
const imageVendeur = document.getElementById("image");

//initilisation de l'état du bouton valider
boutonValider.disabled = !valider;

//désactivation du bouton valider
boutonValider.classList.add("bg-gray-400");
boutonValider.classList.remove("bg-beige", "cursor-pointer");

//gestion du changement d'image
document.getElementById("image").addEventListener("change", function(event) {

    //recupération du fichier
    const file = event.target.files[0];
    
    //si un fichier est sélectionné
    if (file){
        //mise a jour de l'état du champ
        newEtatChamp = true;

        //recupération de l'url du fichier
        const url = URL.createObjectURL(file);
        //mise a jour de l'image affichée
        document.querySelectorAll(".image-vendeur").forEach(img => {
            img.src = url;
        });

        //activation du bouton valider
        //si aucun autre modification n'est en cours
        if(nbModifActive === 0){
            activerValider();
        }
    }    
});

//gestion des evenements des boutons modifier
//changement des styles des éléments concernés
document.querySelectorAll(".modif-attribut .bouton-modifier").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        const boutonModifier = container.querySelector(".bouton-modifier"); // bouton modidier
        const groupeBouton = container.querySelector(".groupe-bouton"); // groupe de bouton valider/annuler

        //on incrémente le nombre de modification en cours
        nbModifActive += 1;

        //désactivation du bouton valider si il est activé
        if(!boutonValider.disabled){
            //garder en mémoire l'état du bouton valider avant modification
            ancienEtatValider = true;
            //désactivation du bouton valider
            boutonValider.disabled = true;
            boutonValider.classList.add("bg-gray-400");
            boutonValider.classList.remove("bg-beige", "cursor-pointer");
        }

        //garder en mémoire l'ancien texte
        ancienTexte = paragraph.textContent;
        
        paragraph.classList.toggle("hidden");

        champ.classList.toggle("hidden");
        champ.classList.toggle("block");

        groupeBouton.classList.toggle("hidden");
        groupeBouton.classList.toggle("flex");  
        
        boutonModifier.classList.toggle("hidden");
        boutonModifier.classList.toggle("block");
    });
});

//gestion des boutons valider/annuler
document.querySelectorAll(".modif-attribut .bouton-valider, .modif-attribut .bouton-annuler").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        const boutonModifier = container.querySelector(".bouton-modifier"); // bouton modidier
        const groupeBouton = container.querySelector(".groupe-bouton"); // groupe de bouton valider/annuler
        
        //on décrémente le nombre de modification en cours
        nbModifActive -= 1;

        //modification du style des boutons concernés
        paragraph.classList.toggle("hidden");

        champ.classList.toggle("hidden");
        champ.classList.toggle("block");

        groupeBouton.classList.toggle("hidden");
        groupeBouton.classList.toggle("flex");  
        
        boutonModifier.classList.toggle("hidden");
        boutonModifier.classList.toggle("block");
    });
});

//gestion des evenements de validations
document.querySelectorAll(".modif-attribut .bouton-valider").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea

        //activation du bouton valider
        //si le bouton est désactivé et que le champ a été modifié
        //globaleEtatChamp passe a true et le bouton valider est activé si aucune autre modification n'est en cours
        if(boutonValider.disabled && newEtatChamp){
            globaleEtatChamp = newEtatChamp;
            if(nbModifActive === 0){
                activerValider();
            }
        }
        
        //mise a jour du texte affiché
        texte = champ.value;
        paragraph.textContent = texte;

    });
});

//gestion des evenements d'annulation
document.querySelectorAll(".modif-attribut .bouton-annuler").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea

        //restauration de l'état du bouton valider
        newEtatChamp = globaleEtatChamp

        //si le bouton valider était activé avant modification et qu'aucune autre modification n'est en cours
        if(ancienEtatValider && nbModifActive === 0){
            activerValider();
        }

        //restauration de l'ancien texte
        champ.value = ancienTexte;

    });
});

//gestion des evenements de modification des champs
document.querySelectorAll('.modif-attribut .champ-text').forEach(input => {
    input.addEventListener('input', () => {
        newEtatChamp = true
    });
});

//fonction d'activation du bouton valider
function activerValider(){
    boutonValider.disabled = false;
    boutonValider.classList.remove("bg-gray-400");
    boutonValider.classList.add("bg-beige", "cursor-pointer");
}
