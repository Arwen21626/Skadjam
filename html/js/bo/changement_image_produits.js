
// gestion du changement d'image
document.getElementById("photo").addEventListener("change", function(event) {

    // recupération du fichier
    const file = event.target.files[0];
    
    // si un fichier est sélectionné
    if (file){
        // recupération de l'url du fichier
        const urlImage = URL.createObjectURL(file);
        // mise a jour de l'image affichée
        let image = document.querySelector(".image-produit")
        image.style.backgroundImage = `url(${urlImage})`;
        image.style.backgroundSize = "100%";
    }    
});