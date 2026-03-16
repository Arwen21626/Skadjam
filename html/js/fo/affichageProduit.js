//Variable pour la boucle
let stock = -1

function afficherProduit(tableau, tableauFA, tableauP, indice, role){
    
    let i = indice

    let trouve = false
    
    let idProduit = tableau[i]['id_produit']
    let parent = document.getElementById("prod")
    let carteProduit = document.getElementById("prod")

    // Carte produit   
    let produit = document.createElement("div")
    produit.classList.add("carteProduit","bg-bleu","flex", "flex-col", "w-40", "h-auto","p-2", "m-2", "md:w-80", "md:p-3", "justify-between")
    produit.id = idProduit
    parent.appendChild(produit)
    parent = produit

    //Lien vers la page detail
    let lien = document.createElement("a")
    lien.href = "details_produit.php?idProduit="+idProduit
    lien.classList.add("mb-3");
    parent.appendChild(lien)

    parent = lien

    //Conteneur rupture stock + Image
    let contImg = document.createElement("div")
    contImg.classList.add("img", "relative")
    parent.appendChild(contImg)

    // Image
    let image = document.createElement("img")
    image.src = tableau[i]['url_photo']
    image.alt = tableau[i]['alt']
    image.title = tableau[i]['titre']
    image.classList.add("w-auto", "h-40", "md:h-80", "mx-auto", "block")
    contImg.appendChild(image)

    //Rupture stock
    if (tableau[i]['quantite_stock'] == 0) {
        let divBandeau = document.createElement("div")
        let texteBandeau = document.createElement("p")
        texteBandeau.textContent = "Hors-stock"

        divBandeau.classList.add("absolute", "inset-0", "flex", "items-center", "justify-center", "z-1")
        texteBandeau.classList.add("bg-rouge", "shadow-lg","text-white", "px-6", "py-2", "w-full", "text-center")

        divBandeau.appendChild(texteBandeau)
        contImg.appendChild(divBandeau)
    }

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

    // Bouton Panier
    let boutonP = document.createElement("button")
    boutonP.classList.add("btnPanier", "cursor-pointer", "size-10", "bg-no-repeat", "bg-size-[auto_40px]")
    bg = "bg-[url(/images/logo/bootstrap_icon/cart-vert-fonce.svg)]"

    trouve = false

    // Si client
    if(role == 'client'){
        if (tableauP != null) {
            for (let key in tableauP) {
                if (key == idProduit) {
                    trouve = true
                }
            }
        }
    }
    // Sinon visiteur
    else{
        if (tableauP != null) {
            for (let key in tableauP.contient) {
                if (key == idProduit) {
                    trouve = true
                }
            }
        }
    }
    // Si on a trouvé le produit dans la liste on change l'icone
    if (trouve != false){
        bg = "bg-[url(/images/logo/bootstrap_icon/cart-fill-vert-fonce.svg)]"
    }
    boutonP.classList.add(bg)

    stock = carteProduit.querySelector("p").textContent
    boutonP.addEventListener('click', function () {

        if (this.className.includes("cart-fill-vert-fonce.svg")) {
            p.classList.remove("hidden")
            label.classList.add("hidden")
            inputNb.classList.add("hidden")
        } else {
            p.classList.add("hidden")
            label.classList.remove("hidden")
            inputNb.classList.remove("hidden")
        }

        input.value = idProduit
        input.name = "idProduit"
        formNbAddPanier.appendChild(input)

        contNbAddPanier.classList.remove("hidden")
        fondNbAddPanier.classList.remove("hidden")
    })

    parent.appendChild(boutonP)

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