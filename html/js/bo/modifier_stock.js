let lignesTab = document.getElementsByTagName("tr");
let champsStock, champsAjouter, champsRetirer;
let stocks = [];

let rougeClaire = "#A70101";
let rouge = "#730D0D";
let orange = "#F8AC3E";
let gris = "#999";
let orangeClaire = "#FFD085";


for(let i = 1; i < lignesTab.length; i++){

    champsStock = lignesTab[i].children[4];
    champsAjouter = lignesTab[i].children[5];
    champsRetirer = lignesTab[i].children[6];

    //seuil d'alert atteint
    if (seuilAlertAtteint(champsStock.children[0].value, i)){
        changementSeuilAtteint(lignesTab[i], i);
    };

    // sauvegarde les toutes les valeurs de stocks
    stocks.push(champsStock.children[0].value);

    // ajout des eventListener pour savoir quand la valeur d'un stock à été modifier
    champsStock.addEventListener("keyup", () => {
        // si l'utilisateur ajoute du stock au clavier
        actionSurStock(document.getElementsByTagName("tr")[i], i);
    });
    
    champsStock.addEventListener("click", () => {
        // si l'utilisateur ajoute du stock avec les flèches de l'input
        actionSurStock(document.getElementsByTagName("tr")[i], i);
    });
    
    champsAjouter.addEventListener("keyup", () => {
        actionSurAjouterRetirer(document.getElementsByTagName("tr")[i], i);
    });

    champsAjouter.addEventListener("click", () => {
        actionSurAjouterRetirer(document.getElementsByTagName("tr")[i], i);
    });

    champsRetirer.addEventListener("keyup", () => {
        actionSurAjouterRetirer(document.getElementsByTagName("tr")[i], i);
    });

    champsRetirer.addEventListener("click", () => {
        actionSurAjouterRetirer(document.getElementsByTagName("tr")[i], i);
    });
}

function actionSurAjouterRetirer(ligne, idxLigne){

    // change la quantite en stock
    calculeNouvStock(ligne, idxLigne);

    // met le fond de la ligne en orange
    stockModifier(ligne, idxLigne);

    // met le fond en rouge s'il y a une erreur de saisie
    if(!(ligne.children[5].children[0].validity.valid)){
        erreurSaisie(ligne.children[5]);
    }
    if(!(ligne.children[6].children[0].validity.valid)){
        erreurSaisie(ligne.children[6]);
    }

    // grise le champs du stock si l'utilisateur ajout ou retire du stock par les champ dédier
    if ((!(ligne.children[5].children[0].validity.valid) || (/[1-9]/).test(ligne.children[5].children[0].value))
        || (!(ligne.children[6].children[0].validity.valid) || (/[1-9]/).test(ligne.children[6].children[0].value))){

        champActif(ligne.children[4].children[0], true);
    }
    else{
        champActif(ligne.children[4].children[0], false);
    }
}

function actionSurStock(ligne, idxLigne){
    // met le fond de la ligne en orange
    stockModifier(ligne, idxLigne);

    // met le fond en rouge s'il y a une erreur de saisie
    if(!ligne.children[4].children[0].value.match(/[0-9]/)){
        erreurSaisie(ligne.children[4]);
    }

    // grise les autres champ si le stock à été modifier
    if (stocks[idxLigne-1] !== ligne.children[4].children[0].value){
        champActif(ligne.children[5].children[0], true);
        champActif(ligne.children[6].children[0], true);
    }
    else{
        champActif(ligne.children[5].children[0], false);
        champActif(ligne.children[6].children[0], false);
    }
}

function erreurSaisie(cible){
    if(!(cible.children[0].validity.valid)){
        cible.children[0].style.backgroundColor = rougeClaire;
        cible.children[0].style.color = "white";

        cible.parentNode.children[7].style.backgroundImage = "url(/images/logo/bootstrap_icon/x-large.svg)"; 
        cible.parentNode.children[7].style.backgroundSize = "1.5em auto"; 
        cible.parentNode.children[7].style.backgroundRepeat = "no-repeat"; 
        cible.parentNode.children[7].style.backgroundPosition = "center center";
    }
}

function stockModifier(ligne, idxLigne){
    // modifi la couleur du fond de la ligne qui à été modifier

    // si la modification change la quantite en stock
    if (stocks[idxLigne-1] !== ligne.children[4].children[0].value){

        if (idxLigne%2 === 0){
            ligne.style.backgroundColor = orangeClaire;
        }
        else{
            ligne.style.backgroundColor = orange;
        }

        ligne.children[7].style.backgroundImage = "url(/images/logo/bootstrap_icon/pencil.svg)"; 
        ligne.children[7].style.backgroundSize = "1.5em auto"; 
        ligne.children[7].style.backgroundRepeat = "no-repeat"; 
        ligne.children[7].style.backgroundPosition = "center center";

        ligne.children[4].children[0].style.backgroundColor = "white";
        ligne.children[5].children[0].style.backgroundColor = "white";
        ligne.children[6].children[0].style.backgroundColor = "white";

        ligne.style.color = "";

    }
    else{
        ligne.style.backgroundColor = "";

        ligne.children[7].style.backgroundImage = ""; 
        ligne.children[7].style.backgroundSize = ""; 
        ligne.children[7].style.backgroundRepeat = ""; 
        ligne.children[7].style.backgroundPosition = "";

        ligne.children[4].children[0].style.color = "";
        ligne.children[5].children[0].style.color = "";
        ligne.children[6].children[0].style.color = "";

        if(seuilAlertAtteint(ligne.children[4].children[0].value)){
            changementSeuilAtteint(ligne, idxLigne);
        }

        ligne.children[4].children[0].style.backgroundColor = "";
        ligne.children[5].children[0].style.backgroundColor = "";
        ligne.children[6].children[0].style.backgroundColor = "";

    }

}

function seuilAlertAtteint(stock){
    let seuil = 0;

    if (stock <= seuil){
        return true;
    }
    return false;
}

function changementSeuilAtteint(ligne, idxLigne){
    if(idxLigne%2 === 0){
        ligne.style.backgroundColor = rougeClaire;
    }
    else{
        ligne.style.backgroundColor = rouge;
    }
    ligne.style.color = "white";

    ligne.children[7].style.backgroundImage = "url(/images/logo/bootstrap_icon/exclamation-triangle.svg)"; 
    ligne.children[7].style.backgroundSize = "1.5em auto"; 
    ligne.children[7].style.backgroundRepeat = "no-repeat"; 
    ligne.children[7].style.backgroundPosition = "center center";
}


function champActif(cible, deactiver){
    // active ou désactive un champ et change sa couleur
    cible.disabled = deactiver;

    if (deactiver){
        cible.style.backgroundColor = gris;

        if (cible.name.match(/^qteStock/)){
            nouvInput = document.createElement("input");

            nouvInput.name = cible.name;
            nouvInput.value = cible.value;
            nouvInput.type = "hidden";

            cible.parentNode.appendChild(nouvInput);
        }
    }
    else{
        cible.style.backgroundColor = "";

        if (cible.name.match(/^qteStock/)){

            if (cible.nextSibling !== null){
                cible.parentNode.removeChild(cible.nextSibling);

            }
        }
    }
}

function calculeNouvStock(ligne, idxLigne){
    // calcule et modifie la quantite en stock d'une ligne en fonction de ce qui y est ajouter ou retirer
    
    let ajout = parseInt(ligne.children[5].children[0].value);
    let retrait = parseInt(ligne.children[6].children[0].value);

    if(isNaN(ajout)){
        ajout = 0;
    }
    if(isNaN(retrait)){
        retrait = 0
    }

    ligne.children[4].children[0].value = parseInt(stocks[idxLigne-1]) + (ajout - retrait);
}
