function verifNomPrenom(nom) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var regex = /^[A-Za-zÀ-öø-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i
    if (regex.test(nom)) {
        return true
    } else {
        return false
    }
}

function verifCodePostal(codePostal) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var regex = /^[0-9]{5}$/i
    if (regex.test(codePostal) && (1<codePostal && codePostal<99999)) {
        return true
    } else {
        return false
    }
}

function verifVille(ville) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var regex = /^[A-Za-zÀ-öø-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/i
    if (regex.test(ville)) {
        return true
    } else {
        return false
    }
}

function verifAdresse(adresse) {
    //verifie le format de l'adresse
    var regex = /^(\d+\s*[A-Za-z]*)[, ]*(.+)$/ui
    if (regex.test(adresse)) {
        return true
    } else {
        return false
    }
}

function verifVille(ville){
    // Vérification de la ville
    var regex = /^([A-Za-zÀ-öø-ÿ]+)*$/i
    if (regex.test(ville)) {
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
        valide = true
    }
    else if(annee == anneeEnCours){
        if(mois >= moisEnCours && mois > 0 && mois <= 12){
            valide = true
        }else{
            valide = false
        }
    }
    else{
        valide = false
    }
    return valide
}

function verifCryptogramme(cryptogramme){
    // Vérifie que le cryptogramme à bien 3 chiffres
    var regex = /[0-9]{3}/i
    if (regex.test(cryptogramme)) {
        return true
    } else {
        return false
    }
}

function verifNumCarte(num){
    num = num.replaceAll(" ", "")
    //Vérifie que le numéro de la carte à bien 16 chiffres
    var regex = /^[0-9]{16}$/
    if (regex.test(num)) {
        return true
    }else{
        return false
    }
}

function verifMail(mail) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var regex = /^[A-Za-z0-9.]+@[A-Za-z-]+.[A-Za-z]+$/
    if (regex.test(mail) && mail.length < 150) {
        return true
    } else {
        return false
    }
}

function verifMotDePasse(mdp) {
    // Autorise Les majuscule, minuscule, accent maj/min, un espace, un ou deux tiret(s)
    var regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-@_#$.£!?%*+:,&~|^])[^\s<>]{10,}$/
    if (regex.test(mdp)) {
        return true
    } else {
        return false
    }
}

function confirmationMotDePasse(mdp, confMdp){
    // Vérification que le mot de passe correspond au mot de passe de vérification
    return mdp === confMdp
}

function verifPseudo(pseudo){
    // Vérification du pseudo
    var regex = /^([0-9A-Za-zÀ-öø-ÿ]+)*$/
    if (regex.test(pseudo) && pseudo.length < 30) {
        return true
    } else {
        return false
    }
}

function verifDate(date) {
    // Vérifie que la date est au format jj-mm-aaaa
    const regex = /^\d{4}-\d{2}-\d{2}$/
    if (regex.test(date)){
        const [annee, mois, jour] = date.split('-').map(Number)
        const d = new Date(annee, mois - 1, jour)
    
        // Vérifie que la date créée correspond exactement
        if(d.getFullYear() === annee && d.getMonth() + 1 === mois && d.getDate() === jour){
            return true
        } else {
            return false
        }
    }else{
        return false
    }
}

function verifNaissance(naissance) {
    console.log(naissance)
    if (verifDate(naissance)){
        // Séparer jour, mois, année
        const [annee, mois, jour] = naissance.split('-').map(Number)
        const dateNaissance = new Date(annee, mois - 1, jour)
        const aujourdHui = new Date()
    
        // Calcul exact de l'âge
        let age = aujourdHui.getFullYear() - dateNaissance.getFullYear()
        const moisDiff = aujourdHui.getMonth() - dateNaissance.getMonth()
    
        // Ajuste si l'anniversaire n'est pas encore passé cette année
        if (moisDiff < 0 || (moisDiff === 0 && aujourdHui.getDate() < dateNaissance.getDate())) {
            age--
        }
        if(age >= 18){
            return true
        } else {
            return false
        }
    }else{
        return false
    }
}

function verifTelephone(telephone){
    // Vérification du telephone
    var regex = /^0[0-9]{9}$/
    if (regex.test(telephone)) {
        return true
    } else {
        return false
    }
}

function verifIBAN(iban){
    // Vérification de l'IBAN
    var regex = /^FR[0-9]{25}$/
    if (regex.test(iban)) {
        return true
    } else {
        return false
    }
}

function verifSiren(siren){
    // Vérification de l'IBAN
    var regex = /^[0-9]{9}$/
    if (regex.test(siren) ) {
        var sirenSplit = siren.split("")
        sirenSplit[1] *= 2;
        sirenSplit[3] *= 2;
        sirenSplit[5] *= 2;
        sirenSplit[7] *= 2;
        sirenSplit.forEach(nb => {
            if (nb > 9) {
                nb = nb -9
            }
        var somme = sirenSplit.some()
        if (somme%10 === 0) {
            return true
        }
        else{
            return false
        }
        });
        
    } else {
        return false
    }
}