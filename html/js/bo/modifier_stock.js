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
    champsStock.addEventListener("keyup", () => {
            // si on change le stock
            if (stocks[i-1] !== document.getElementsByTagName("tr")[i].children[4].children[0].value){
                champActif(document.getElementsByTagName("tr")[i].children[5].children[0].disabled = true)
                document.getElementsByTagName("tr")[i].children[6].children[0].disabled = true
            }
            else{
                document.getElementsByTagName("tr")[i].children[5].children[0].disabled = false
                document.getElementsByTagName("tr")[i].children[6].children[0].disabled = false
            }
            stockModifier(i);
        })

    champsAjouter.addEventListener("keyup", () => {
            // si on ajout une certaine quantite en stock
            if (stocks[i-1] !== document.getElementsByTagName("tr")[i].children[4].children[0].value){
                document.getElementsByTagName("tr")[i].children[4].children[0].disabled = true
            }
            else{

            }
            calculeNouvStock(i)
        })

    champsRetirer.addEventListener("keyup", () => {
            // si on retire une certaine quantite en stock
            document.getElementsByTagName("tr")[i].children[4].children[0].disabled = true
            calculeNouvStock(i)
        })

}

function stockModifier(ligne){
    // modifi la couleur du fond de la ligne qui à été modifier

    // si la modification change la valeur du champ
    if (stocks[ligne-1] !== document.getElementsByTagName("tr")[ligne].children[4].children[0].value){
        lignesTab[ligne].style.backgroundColor = "#f8ac3e";

    }
    else{
        lignesTab[ligne].style.backgroundColor = "";
    }
}

function calculeNouvStock(ligne){
    // calcule et modifie la quantite en stock d'une ligne en fonction de ce qui y est ajouter ou retirer
    
    let aAjouter = parseInt(document.getElementsByTagName("tr")[ligne].children[5].children[0].value) - parseInt(document.getElementsByTagName("tr")[ligne].children[6].children[0].value)

    if(isNaN(aAjouter)){
        aAjouter = 0;
    }

    document.getElementsByTagName("tr")[ligne].children[4].children[0].value = parseInt(stocks[ligne-1]) + aAjouter;


    // indication visuel que la quantite en stock à changer
    stockModifier(ligne);
}

function champActif(cible, actif){
    document.getElementsByTagName("tr")[i].children[6].children[0].disabled = true
}