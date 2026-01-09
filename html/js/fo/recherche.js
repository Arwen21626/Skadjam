function afficherProduit(lignes){
    
    lignes.forEach(prod => {
        //console.log(prod)
        
        idProduit = prod['id_produit']
        let parent = document.getElementById("prod")

        // Section   
        let produit = document.createElement("section")
        parent.appendChild(produit)
        produit.classList.add("bg-bleu", "grid", "grid-cols-[40%_60%]", "w-40", "md:w-80", "h-auto", "p-2", "md:p-3", "m-2")
        parent = produit

        //Lien
        let lien = document.createElement("a")
        lien.href = "details_produit.php?idProduit="+idProduit
        lien.classList.add("col-span-2", "justify-self-center", "mb-3");
        parent.appendChild(lien)

        parent = lien

        // Image
        let image = document.createElement("img")
        image.src = prod['url_photo']
        image.alt = prod['alt']
        image.title = prod['title']
        parent.appendChild(image)

        // Nom produit
        let nom = document.createElement("p")
        nom.textContent = prod['libelle_produit']
        parent.appendChild(nom)
        nom.classList.add("col-span-2")

        // Prix et note
        let contient = document.createElement("div")
        parent.appendChild(contient)
        contient.classList.add("flex", "justify-start", "items-center", "col-span-2")

        parent = contient

        // Prix
        let prix = document.createElement("p")
        prix.textContent = prod['prix_ttc'].replace(".", ",")+" €"
        parent.appendChild(prix)

        // Note
        let contientNote = document.createElement("div")
        parent.appendChild(contientNote)
        contientNote.classList.add("w-2/4", "ml-2", "md:ml-10", "flex")

        parent = contientNote

        let note = prod['note_moyenne']
        affichageNote(note, parent)
        
        //setTimeout(function(){console.log('Code waits for 1  second')}, 1000);
    
    });
}



async function affichageNote(note, parent ){
    // fonction qui affiche une note avec des étoiles
    //affichage d'une note nulle
    let section = document.createElement("section")
    
    parent.appendChild(section)
    parent = section

    section.classList.add("flex", "flex-nowrap", "items-center")

    if (note == null){
        let p = document.createElement("p")
        p.textContent = "non noté"
        parent.appendChild(p)
    } 

    else {
        let entierPrec = parseInt(note)
        let entierSuiv = entierPrec +1
        let moitie = entierPrec + 0.5
        let noteFinale = 0
        let nbEtoileVides = 0

        //note arrondie à l'entier précédent
        if(note < entierPrec+0.3){
            noteFinale = entierPrec;
        }

        //note arrondie à 0.5
        else if((note < moitie) || (note < entierPrec+0.8)){
            noteFinale = moitie;
            nbEtoileVides = 5-entierPrec-1;
            //affichage d'une note et demie
            //boucle pour étoiles pleines
            for(i=0; i<entierPrec; i++){
                let etoileP = document.createElement("img")
                etoileP.src = "../../images/logo/bootstrap_icon/star-fill.svg"
                etoileP.alt = "étoile pleine"
                etoileP.title = "étoile pleine"
                parent.appendChild(etoileP)
            }
            // Demie étoile
            let etoileD = document.createElement("img")
            etoileD.src = "../../images/logo/bootstrap_icon/star-half.svg"
            etoileD.alt = "demie étoile"
            etoileD.title = "demie étoile"
            parent.appendChild(etoileD)
            
            // Boucle pour étoiles vides-->
            for(i=0; i<nbEtoileVides; i++){
                let etoileV = document.createElement("img")
                etoileV.src = "../../images/logo/bootstrap_icon/star.svg"
                etoileV.alt = "étoile vide"
                etoileV.title = "étoile vide"
                parent.appendChild(etoileV)
            }
        }
        
        //note arrondie à l'entier suivant
        else{
            noteFinale = entierSuiv;
        }

        //affichage d'une note entière :
        if(noteFinale != moitie){
            nbEtoileVides = 5-noteFinale;
            //boucle pour étoiles pleines
            for(i=0; i<noteFinale; i++){
                let etoilePF = document.createElement("img")
                etoilePF.src = "../../images/logo/bootstrap_icon/star-fill.svg"
                etoilePF.alt = "étoile pleine"
                etoilePF.title = "étoile pleine"
                parent.appendChild(etoilePF)
            }
            //boucle pour étoiles vides
            for(i=0; i<nbEtoileVides; i++){
                let etoileVF = document.createElement("img")
                etoileVF.src = "../../images/logo/bootstrap_icon/star.svg"
                etoileVF.alt = "étoile vide"
                etoileVF.title = "étoile vide"
                parent.appendChild(etoileVF)
            }
        }
    }
}

// Filtres


// Tris
function triPrixCroissant(tab){
    let temp = tab.sort((a, b) => parseInt(a['prix_ttc']) - parseInt(b['prix_ttc']))
    console.log(temp)
    return tab.sort((a, b) => parseInt(a['prix_ttc']) - parseInt(b['prix_ttc']))
}

function triPrixDecroissant(tab){
    let temp = tab.sort((a, b) => parseFloat(b['prix_ttc']) - parseFloat(a['prix_ttc']))
    console.log(temp)
    return tab.sort((a, b) => parseFloat(b['prix_ttc']) - parseFloat(a['prix_ttc']))
}

function triAz(tab){
    let temp = tab.sort((a,b) => a['libelle_produit'].localeCompare(b['libelle_produit']))
    console.log(temp)
    return tab.sort((a,b) => a['libelle_produit'].localeCompare(b['libelle_produit']))
}

function triZa(tab){
    let temp = tab.sort((a,b) => b['libelle_produit'].localeCompare(a['libelle_produit']))
    console.log(temp)
    return tab.sort((a,b) => b['libelle_produit'].localeCompare(a['libelle_produit']))
}

function triEtoileCroissant(tab){
    let temp = tab.sort((a,b) => parseFloat(b['note_moyenne']) - parseFloat(a['note_moyenne']))
    console.log(temp)
    return tab.sort((a,b) => parseFloat(b['note_moyenne']) - parseFloat(a['note_moyenne']))
}

function triEtoileDecroissant(tab){
    let temp = tab.sort((a,b) => parseFloat(a['note_moyenne']) - parseFloat(b['note_moyenne']))
    console.log(temp)
    return tab.sort((a,b) => parseFloat(a['note_moyenne']) - parseFloat(b['note_moyenne']))
}