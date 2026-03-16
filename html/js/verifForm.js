function verifNomPrenom(nom) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(nom)) {
        return true
    } else {
        return false
    }
}

function verifCodePostal(codePostal) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[0-9]{5}$/i;
    if (modele.test(codePostal) && (1<codePostal && codePostal<99999)) {
        return true
    } else {
        return false
    }
}

function verifVille(ville) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(ville)) {
        return true
    } else {
        return false
    }
}

function verifAdresse(adresse) {
    //verifie le format de l'adresse
    var modele = /^(\d+\s*[A-Za-z]*)[, ]*(.+)$/ui;
    if (modele.test(adresse)) {
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

function verifMail(mail) {
    console.log(mail.length)
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-z0-9.]+@[A-Za-z-]+.[A-Za-z]+$/;
    if (modele.test(mail) || mail.length > 150) {
        return true
    } else {
        return false
    }
}

function verifMotDePasse(mdp) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-@_#$.£!?%*+:;,&~|^])[^\s<>]{10,}$/;
    if (modele.test(mdp)) {
        return true
    } else {
        return false
    }
}