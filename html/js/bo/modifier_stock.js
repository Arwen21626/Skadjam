let lignesTab = document.getElementsByTagName("tr");
let champsStock, champsAjouter, champsRetirer;
let stocks = [];


for(let i = 1; i < lignesTab.length; i++){

    champsStock = lignesTab[i].children[4]
    champsAjouter = lignesTab[i].children[5]
    champsRetirer = lignesTab[i].children[6]

    // sauvegarde les toutes les valeurs de stocks
    stocks.push(champsStock.children[0].value);

    // ajout des event litener pour savoir quand la valeur d'un stock à été modifier
    champsStock.addEventListener("keyup", (e) => {
        // si on change le stock
        stockModifier(i, e);

        if (stocks[i-1] !== document.getElementsByTagName("tr")[i].children[4].children[0].value){
            champActif(document.getElementsByTagName("tr")[i].children[5].children[0], true);
            champActif(document.getElementsByTagName("tr")[i].children[6].children[0], true);
        }
        else{
            champActif(document.getElementsByTagName("tr")[i].children[5].children[0], false)
            champActif(document.getElementsByTagName("tr")[i].children[6].children[0], false)
        }
    })

    champsAjouter.addEventListener("keyup", (e) => {
        // si on ajout une certaine quantite en stock
        calculeNouvStock(i, e);

        if (document.getElementsByTagName("tr")[i].children[5].children[0].value !== "0"){
            champActif(document.getElementsByTagName("tr")[i].children[4].children[0], true);
        }
        else{
            champActif(document.getElementsByTagName("tr")[i].children[4].children[0], false);
        }
        
    })

    champsRetirer.addEventListener("keyup", (e) => {
        // si on retire une certaine quantite en stock
        calculeNouvStock(i, e);

        if (document.getElementsByTagName("tr")[i].children[6].children[0].value !== "0"){
            champActif(document.getElementsByTagName("tr")[i].children[4].children[0], true);
        }
        else{
            champActif(document.getElementsByTagName("tr")[i].children[4].children[0], false);
        }
    })

}






function stockModifier(ligne, event){
    // modifi la couleur du fond de la ligne qui à été modifier

    // si la modification change la valeur du champ
    if (stocks[ligne-1] !== document.getElementsByTagName("tr")[ligne].children[4].children[0].value){
        lignesTab[ligne].style.backgroundColor = "#f8ac3e";
        
        lignesTab[ligne].children[4].children[0].style.backgroundColor = "white";
        lignesTab[ligne].children[5].children[0].style.backgroundColor = "white";
        lignesTab[ligne].children[6].children[0].style.backgroundColor = "white";
    }
    else{
        lignesTab[ligne].style.backgroundColor = "";
    }
}

function calculeNouvStock(ligne, event){
    // calcule et modifie la quantite en stock d'une ligne en fonction de ce qui y est ajouter ou retirer
    let ajout = parseInt(document.getElementsByTagName("tr")[ligne].children[5].children[0].value);
    let retrait = parseInt(document.getElementsByTagName("tr")[ligne].children[6].children[0].value);

    if(isNaN(ajout)){
        ajout = 0;
    }
    if(isNaN(retrait)){
        retrait = 0
    }

    let aAjouter = ajout - retrait;

    document.getElementsByTagName("tr")[ligne].children[4].children[0].value = parseInt(stocks[ligne-1]) + aAjouter;


    // indication visuel que la quantite en stock à changer
    stockModifier(ligne, event);
}

function champActif(cible, deactiver){
    // active ou désactive un champ et change sa couleur
    cible.disabled = deactiver;

    if (deactiver){
        cible.style.backgroundColor = "#999";

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
            cible.parentNode.removeChild(cible.nextSibling);
        }
    }
}