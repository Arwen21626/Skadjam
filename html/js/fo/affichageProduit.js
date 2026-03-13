function afficherProduit(tableau,tableauFA, indice){
    
    let i = indice

    let trouve = false
    
    let idProduit = tableau[i]['id_produit']
    let parent = document.getElementById("prod")

    // Carte produit   
    let produit = document.createElement("div")
    produit.classList.add("bg-bleu","flex", "flex-col", "w-40", "h-auto","p-2", "m-2", "md:w-80", "md:p-3", "justify-between")
    produit.id = idProduit
    parent.appendChild(produit)
    parent = produit

    //Lien vers la page detail
    let lien = document.createElement("a")
    lien.href = "details_produit.php?idProduit="+idProduit
    lien.classList.add("mb-3");
    parent.appendChild(lien)

    parent = lien

    // Image
    let image = document.createElement("img")
    image.src = tableau[i]['url_photo']
    image.alt = tableau[i]['alt']
    image.title = tableau[i]['titre']
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

    prixTTC.append(tableau[i]['prix_ttc'].replace(".", ",") + " € (")

    // Ajout <abbr> pour "TTC"
    let abbr = document.createElement("abbr")
    abbr.textContent = "TTC"
    abbr.title = "Toutes Taxes Comprises"

    prixTTC.append(abbr)
    prixTTC.append(")")

    prixTTC.classList.add("line-through")
    parent.appendChild(prixTTC)

    // Prix Remise
    let prixRemise = document.createElement("p")

    prixRemise.append(tableau[i]['prix_remise'].replace(".", ",") + " € (")

    // Ajout <abbr> pour "TTC"
    let abbr2 = document.createElement("abbr")
    abbr2.textContent = "TTC"
    abbr2.title = "Toutes Taxes Comprises"

    prixRemise.append(abbr2)
    prixRemise.append(")")

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

    // Conteneur des boutons futurs achats & panier
    parent = produit
    let contientBtnFAP = document.createElement("div")
    contientBtnFAP.classList.add("flex", "justify-end")
    parent.appendChild(contientBtnFAP)

    parent = contientBtnFAP

    // Lien vers action quand on appuie sur FA
    let lienFA = document.createElement("a")
    lienFA.href = "/php/traitementFAPanier.php?idProduit="+idProduit+"&ajout=fa&vientDe=recherche"
    lienFA.id = "btnFA"
    parent.appendChild(lienFA)
    parent = lienFA

    // Bouton FA
    let boutonFA = document.createElement("button")
    boutonFA.classList.add("cursor-pointer", "size-10", "bg-no-repeat", "bg-size-[auto_40px]")
    let bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus.svg)]"
    if (tableauFA != null) {
        for (let key in tableauFA) {
            if (key == idProduit) {
                trouve = true
            }
        }
    }

    // Si on trouve le produit dans la liste on change l'icone
    if (trouve != false){
        bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus-fill.svg)]"
    }


    boutonFA.classList.add(bg)
    parent.appendChild(boutonFA)

    parent = contientBtnFAP

    // Lien vers action quand on appuie sur Panier
    let lienP = document.createElement("a")
    lienP.href = "/php/traitementFAPanier.php?idProduit="+idProduit+"&ajout=panier&vientDe=recherche"
    lienP.id = "btnPanier"
    parent.appendChild(lienP)
    parent = lienP

    // Bouton Panier
    let boutonP = document.createElement("button")
    boutonP.classList.add("cursor-pointer", "size-10", "bg-no-repeat", "bg-size-[auto_40px]")
    bg = "bg-[url(/images/logo/bootstrap_icon/carte.svg)]"
    trouve = tableauFA.find(idProduit)

    // Si on trouve le produit dans la liste on change l'icone
    if (trouve != undefined){
        bg = "bg-[url(/images/logo/bootstrap_icon/cart-fill.svg)]"
    }

    boutonFA.classList.add(bg)
    parent.appendChild(boutonFA)

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