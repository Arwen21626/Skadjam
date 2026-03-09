function afficherProduit(tableau, indice){
    
    let i = indice
    
    let idProduit = tableau[i]['id_produit']
    let parent = document.getElementById("prod")

    // Section   
    let produit = document.createElement("section")
    parent.appendChild(produit)
    produit.classList.add("bg-bleu","flex", "flex-col", "w-40", "h-auto","p-2", "m-2", "md:w-80", "md:p-3")
    parent = produit

    //Lien
    let lien = document.createElement("a")
    lien.href = "details_produit.php?idProduit="+idProduit
    lien.classList.add("mb-3");
    parent.appendChild(lien)

    parent = lien

    // Image
    let image = document.createElement("img")
    image.src = tableau[i]['url_photo']
    image.alt = tableau[i]['alt']
    image.title = tableau[i]['alt']
    image.classList.add("w-auto", "h-40", "md:h-80", "mx-auto", "block")
    parent.appendChild(image)

    // Nom produit
    let nom = document.createElement("p")
    nom.textContent = tableau[i]['libelle_produit']
    parent.appendChild(nom)

    // Prix et note
    let contientPrix = document.createElement("article")
    parent.appendChild(contientPrix)
    contientPrix.classList.add("flex","flex-row", "justify-between", "items-center")

    parent = contientPrix

    // Prix TTC
    let prixTTC = document.createElement("p")
    prixTTC.textContent = tableau[i]['prix_ttc'].replace(".", ",")+" € (TTC)"
    prixTTC.classList.add("line-through")
    parent.appendChild(prixTTC)

    let prixRemise = document.createElement("p")
    prixRemise.textContent = tableau[i]['prix_remise'].replace(".", ",")+" € (TTC)"
    parent.appendChild(prixRemise)

    if (tableau[i]['prix_remise'] == tableau[i]['prix_ttc']) {
        prixRemise.classList.add("hidden")
        prixTTC.classList.remove("line-through")
    }

    // Note
    let contientNote = document.createElement("div")
    parent = lien   
    parent.appendChild(contientNote)
    contientNote.classList.add("flex")

    parent = contientNote

    let note = tableau[i]['note_moyenne']
    affichageNote(note, parent)

    // Promotion
    if (tableau[i]['id_promotion'] != null){
        parent = produit
        let promo = document.createElement("div")
        promo.classList.add("bg-rouge", "absolute", "w-36", "md:w-74", "underline", "text-beige", "pt-2", "pb-1.5")
        let nomPromo = document.createElement("h4")
        nomPromo.textContent = tableau[i]['label']
        nomPromo.classList.add("text-center", "text-beige", "overline", "m-0")

        parent.appendChild(promo)
        parent = promo
        parent.append(nomPromo)
    }
    

    
    //setTimeout(function(){console.log('Code waits for 1  second')}, 1000);
}