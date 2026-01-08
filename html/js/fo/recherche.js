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
