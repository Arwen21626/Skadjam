/* 
    Gestion de la popup (fichier JS a importé dans le js de votre page)
    Syntaxe : import * as Popup from "chemin vers ce fichier"
    (Popup peut être remplacé par ce que vous voulez, 
    il s'agit d'un objet contenant les fonctions & variables etc du fichier) 

    Attention vous devez ajouter un attribut type="module" dans votre balise script sur la page 
    où vous utilisez un script JS qui utilise des fonctions/variables etc importé d'un autre fichier
*/

export function sleep(ms){
    // Fonction pour mettre une pause dans une exécution (par exemple pour attendre avant de fermer la popup)
    // à utiliser dans une fonction asynchrone (mot clé async) avec la syntaxe : await sleep(ms);
    return new Promise(resolve => setTimeout(resolve, ms));
}

export async function closePopup(idPopup) {
    // Ajoute la class à la popup qui lancera l'animation pour "fermer" la popup
    document.getElementById(idPopup).classList.add("desactive");
    await sleep(1000);
    document.getElementById("popup-overlay").classList.add("desactive");
}

export async function openPopUp(idPopup, ms){
    
    // Fonction asynchrone qui affiche la popup en "activant" l'overlay de la popup
    // Met une pause de millisecondes indiqué par ms
    // puis appel la fonction pour lancer l'animation pour "fermer" la popup

    document.getElementById("popup-overlay").classList.add("active");
    document.getElementById(idPopup).classList.add("active");
    await sleep(ms);
    closePopup(idPopup);
}

export function showPopUp(idPopup, ms, getAttribute = null) {

    /*  Si l'affichage de votre popup nécessite un attribut get 
        Par exemple vous faites un traitement qui redirige vers votre page, au moment de la redirection
        vous passez un attribut Get que vous indiquez dans cette fonction.
        si la fonction trouve bien votre attribut, elle affiche la popup

        Si votre popup est affiché différemment, getAttribute peut être ignorer
    */

    if (getAttribute !== null) {
        const urlParams = new URLSearchParams(window.location.search);

        if (urlParams.has(getAttribute)){
            openPopUp(idPopup, ms);
        } 

        //Permet de retirer l'attribut Get de l'URL pour empêcher de réafficher la popup 
        // si l'utilisateur rafraîchit la page

        const url = new URL(window.location.href); // Récupère l'url actuelle

        url.searchParams.delete(getAttribute); // Supprime seulement l'attribut utilisé pour afficher la popup

        history.replaceState(null, "", url.toString()); // Modifie l'url de la page pour empêcher le réaffichage de la popup en cas de réactualisation
    }
    else {
        // Ici vous pouvez ajoutez un fonctionnement d'affichage différent 
        // si vous ne passez pas par un attribut Get

    }
         
}