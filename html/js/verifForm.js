function verifNomPrenom(nomForm) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(nomForm)) {
        return true
    } else {
        return false
    }
}

function verifCodePostal(codePostalForm) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[0-9]{5}$/i;
    if (modele.test(codePostalForm) && (1000<codePostalForm && codePostalForm<99999)) {
        return true
    } else {
        return false
    }
}

function verifVille(villeForm) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(villeForm)) {
        return true
    } else {
        return false
    }
}

function verifAdresse(adresseForm) {
    //verifie le format de l'adresse
    var modele = /^(\d+\s*[A-Za-z]*)[, ]*(.+)$/ui;
    if (modele.test(adresseForm)) {
        return true
    } else {
        return false
    }
}

function verifExpiration(date){
    //vérifie l'expiration de la date de la carte bancaire
    dateCut = date.split('/')
    mois = dateCut[0]
    annee = dateCut[1]

    x = new Date()
    anneeEnCours = x.getFullYear()-2000
    moisEnCours = x.getMonth()+1

    if(annee > anneeEnCours && mois > 0 && mois <= 12){
        valide = true;
    }
    else if(annee == anneeEnCours){
        if(mois >= moisEnCours && mois > 0 && mois <= 12){
            valide = true;
        }else{
            valide = false;
        }
    }
    else{
        valide = false;
    }
    return valide;
}

function verifCryptogramme(cryptogramme){
    // Vérifie que le cryptogramme à bien 3 chiffres
    var modele = /[0-9]{3}/i;
    if (modele.test(cryptogramme)) {
        return true
    } else {
        return false
    }
}

function verifNumCarte(num){
    num = num.replaceAll(" ", "");
    //Vérifie que le numéro de la carte à bien 16 chiffres
    var modele = /^[0-9]{16}$/
    if (modele.test(num)) {
        return true
    }else{
        return false
    }
}