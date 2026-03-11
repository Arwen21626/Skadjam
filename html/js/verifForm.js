function validerNomPrenom(nomForm) {
    var nom = nomForm
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(nom)) {
        return true
    } else {
        return false
    }
}

function validerCodePostal(codePostalForm) {
    var nom = codePostalForm
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[0-9]{5}$/i;
    if (modele.test(nom) && (1000<nom && nom<99999)) {
        return true
    } else {
        return false
    }
}

function validerVille(villeForm) {
    var nom = villeForm
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var modele = /^[A-Za-zÀ-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i;
    if (modele.test(nom)) {
        return true
    } else {
        return false
    }
}

function validerAdresse(adresseForm) {
    var nom = adresseForm
    //verifie le format de l'adresse
    var modele = /^(\d+\s*[A-Za-z]*)[, ]*(.+)$/ui;
    if (modele.test(nom)) {
        return true
    } else {
        return false
    }
}

function verifExpiration(date){
    //Vérifie que la date d'expiration n'est pas dépassé
    dateCut = date.split('/')
    mois = dateCut[0]
    annee = dateCut[1]
    anneeEnCours = getFullYear()
    moisEnCours = getMonth()
    annee += 2000;
    valide = false;

    if($annee > $anneeEnCours && $mois > 0 && $mois <= 12){
        $valide = true;
    }
    else if($annee == $anneeEnCours){
        if($mois >= $moisEnCours && $mois > 0 && $mois <= 12){
            $valide = true;
        }else{
            $valide = false;
        }
    }
    else{
        $valide = false;
    }
    return $valide;
}