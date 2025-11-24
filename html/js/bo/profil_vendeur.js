//initialisation des variables
let valider = false;
let ancienEtatValider = false;
let nbModifActive = 0;
let globaleEtatChamp = false;
let newEtatChamp = false;
let ancienTexte = "";
let texte = "";
let imageEstSupprimee = false;

let imagesSrc = [];
let imageFile = [];

//initialisation des éléments
const boutonValider = document.getElementById("valider");
const imageVendeurInput = document.getElementById("image");
const containerImage = document.querySelector(".container-image");
const boutonAnnulerImage = document.querySelector(".bouton-annuler-image");
const boutonSupprimerImage = document.querySelector(".bouton-poubelle");
const imageVendeur = document.querySelector(".image-vendeur");
const srcImageVide = "../../images/logo/bootstrap_icon/image.svg";
const form = document.getElementById('form-profil-vendeur');
const estConteneurVide = containerImage.classList.contains("vide");

imagesSrc.push(imageVendeur.src);
imageFile.push("");

//désactivation du bouton valider
desactiverValider();


// creer un input avec l'etat de la photo
form.addEventListener('submit', function(e) {
    let input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'imageSupprimee';
    input.value = (imageEstSupprimee) ? "true" : "false";
    form.appendChild(input);
});


boutonSupprimerImage.addEventListener("click", supprimerImage);

boutonAnnulerImage.addEventListener("click", annulerImage);

//gestion du changement d'image
imageVendeurInput.addEventListener("change", ajouterImage);

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
            desactiverValider();
        }

        //garder en mémoire l'ancien texte
        ancienTexte = paragraph.textContent;

        champInput(paragraph, champ, boutonModifier, groupeBouton);
        
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
        champParagraphe(paragraph, champ, groupeBouton, boutonModifier);
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
        if((boutonValider.disabled && newEtatChamp) || imageEstModifier()){
            globaleEtatChamp = newEtatChamp;
            if(nbModifActive === 0){
                activerValider();
            }
            if (!ancienEtatValider && nbModifActive !=0) ancienEtatValider=true;
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
        if((ancienEtatValider && nbModifActive === 0) || imageEstModifier()){
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


function validerEstActive(){
    return !boutonValider.disabled;
}

function activerValider(){
    boutonValider.disabled = false;
    boutonValider.classList.remove("bg-gray-400");
    boutonValider.classList.add("bg-beige", "cursor-pointer");
}

function desactiverValider(){
    boutonValider.disabled = true;
    boutonValider.classList.add("bg-gray-400");
    boutonValider.classList.remove("bg-beige", "cursor-pointer");
}

function annulerEstActive(){
    return !boutonAnnulerImage.classList.contains("hidden");
}

function activerAnnuler(){
    boutonAnnulerImage.classList.remove("hidden");
}

function desactiverAnnuler(){
    boutonAnnulerImage.classList.add("hidden");
}

function supprimerEstActive(){
    return !boutonSupprimerImage.classList.contains("hidden");
}

function activerSupprimer(){
    boutonSupprimerImage.classList.remove("hidden");
}

function desactiverSupprimer(){
    boutonSupprimerImage.classList.add("hidden");
}

function imageEstModifier(){
    return imagesSrc.length>1;
}

function ajouterImage(event){
    const file = event.target.files[0];
    if (file){
        let n = imagesSrc.length;
        if (imageFile[n-1]=="" && imageEstModifier()){
            imageFile.pop();
            imagesSrc.pop();
        }
        imageEstSupprimee = false;
        //recupération de l'url du fichier
        const url = URL.createObjectURL(file);
    
        //mise a jour de l'image affichée
        imageVendeur.src = url;
        imagesSrc.push(url);
        imageFile.push(file);
        containerImage.classList.remove("bg-beige", "h-80");
        containerImage.classList.add("border-2", "border-solid", "border-beige");
        if (!supprimerEstActive()) activerSupprimer();
        if (!annulerEstActive()) activerAnnuler();
        if (!validerEstActive()) activerValider();
    }

}

function supprimerImage(){
    imagesSrc.push(srcImageVide);
    imageVendeur.src = srcImageVide;
    imageVendeurInput.value = "";
    imageFile.push("");
    imageVendeur.classList.add("bg-beige")
    imageEstSupprimee = true;
    if (supprimerEstActive()) desactiverSupprimer();
    if (!annulerEstActive()) activerAnnuler();
    if (estConteneurVide) desactiverAnnuler();
    if (!validerEstActive()) activerValider();
    if (estConteneurVide) desactiverValider();
}

function annulerImage(){
    let n = imagesSrc.length;
    imageEstSupprimee = false;
    if (imageEstModifier()){
        imagesSrc = imagesSrc.slice(0,1);
        imageFile = imageFile.slice(0,1);

        imageVendeur.src = imagesSrc[0];
        imageVendeurInput.value = imageFile[0]
        console.log(imageVendeur.src);
    }
    desactiverSupprimer();
    desactiverAnnuler();
    desactiverValider();
    
}

function champParagraphe(paragraph, champ, groupeBouton, boutonModifier){
    paragraph.classList.toggle("hidden");

    champ.classList.toggle("hidden");
    champ.classList.toggle("block");

    groupeBouton.classList.toggle("hidden");
    groupeBouton.classList.toggle("flex");  

    boutonModifier.classList.toggle("hidden");
    boutonModifier.classList.toggle("block");
}

function champInput(paragraph, champ, groupeBouton, boutonModifier){
    paragraph.classList.toggle("hidden");

    champ.classList.toggle("hidden");
    champ.classList.toggle("block");

    groupeBouton.classList.toggle("hidden");
    groupeBouton.classList.toggle("flex");  

    boutonModifier.classList.toggle("hidden");
    boutonModifier.classList.toggle("block");
}