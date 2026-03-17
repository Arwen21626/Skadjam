const API_URL = "/php/structure/authentikATOR/authAPI.php"

let baseParam

function initParam(idClient, secret = "", edition = 0) {
    console.log("[appelAJAX] secret = " + secret)
    console.log("[appelAJAX] edition = " + edition)
    baseParam = {"societe": "Alizon", "idClient": idClient, "secret": secret, "edition": edition}
}

async function callApi(action, params = {}) {
    let data = new URLSearchParams({action, ...baseParam, ...params})

    let reponse = await fetch(API_URL, {
        method: "post",
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: data.toString()
    })
    console.log("[appelAJAX] reponse = " + reponse)
    return await reponse.json()
}

async function getSecret() {
    let secretData = await callApi('getSecret')
    console.log("[appelAJAX] secretData = " + secretData)
    return secretData
}

async function verifOtp(code) {
    let result = await callApi('verifOtp', {'userCode': code})
    let isOk = result['verify'] // CORRIGÉ : true = code valide, false = invalide
    console.log("[appelAJAX] verif ok = " + isOk)
    return isOk
}

async function saveSecret() {
    let result = await callApi('saveSecret')
    let isSave = result['save']
    console.log("[appelAJAX] Save = " + isSave)
    return isSave
}

async function getTentative() {
    let result = await callApi('getTentative')
    let nbTentative = result['tentative']
    console.log("[appelAJAX] nbTentative = " + nbTentative)
    return nbTentative
}

async function getTempsRestant() {
    let result = await callApi('getTempsRestant')
    let tempsRest = result['restant']
    console.log('[appelAJAX] temps = ' + tempsRest)
    return tempsRest
}

async function addTempsRestant() {
    let result = await callApi('addTempsRestant')
    let ret = result['addTemps']
    console.log("[appelAJAX] addTempsRestant = " + ret)
    return ret
}

async function addTentative() {
    let result = await callApi('addTentative')
    let ret = result['addTentative']
    console.log('[appelAJAX] addTentative = ' + ret)
    return ret
}

async function resetTentative() {
    let result = await callApi('resetTentative')
    let ret = result['resetTentative']
    console.log('[appelAJAX] resetTentative = ' + ret)
    return ret
}

async function delSecret() {
    let result = await callApi('delSecret')
    let ret = result['delSecret']
    console.log('[appelAJAX] delSecret = ' + ret)
    return ret
}