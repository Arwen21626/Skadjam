const API_URL = "/php/structure/authentikATOR/authAPI.php"

let baseParam

function initParam(idClient, secret = "", edition = 1){
    console.log("[appelAJAX] secret = "+secret)
    console.log("[appelAJAX] edition = "+edition)
    baseParam = {"societe": "Alizon", "idClient": idClient, "secret": secret, "edition":edition}
}

async function callApi(action, params ={}){
    let data = new URLSearchParams({action, ...baseParam, ...params})

    let reponse = await fetch(API_URL,{
        method: "post",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: data.toString()
    })
    console.log("[appelAJAX] reponse = "+reponse)
    return await reponse.json();
}

async function getSecret(){
    secretData = await callApi('getSecret')
    secretData = secretData
    console.log("[appelAJAX] secretData = "+secretData)
    return secretData
}

async function verifOtp(code){
    isOk = await callApi('verifOtp', {'userCode': code})
    isOk = isOk['verify']
    console.log("[appelAJAX] verif ok = "+isOk)
    return isOk
    
}

async function saveSecret(){
    isSave = await callApi('saveSecret')
    isSave['save']
    console.log("[appelAJAX] Save = "+isSave)
    return isSave
}

async function getTentative(){
    nbTentative = await callApi('getTentative')
    nbTentative = nbTentative['tentative']
    console.log("[appelAJAX] nbTentative = "+nbTentative)
    return nbTentative
}

async function getTempsRestant(){
    tempsRest = await callApi('getTempsRestant')
    tempsRest = tempsRest['restant']
    console.log('[appelAJAX] temps = '+tempsRest)
    return tempsRest
}

async function addTempsRestant(){
    ret = await callApi('addTempsRestant')
    ret = ret['addTemps']
    console.log("[appelAJAX] addtempsRestant = "+ret)
    return ret
}

async function addTentative(){
    ret = await callApi('addTentative')
    ret = ret['addTentative']
    console.log('[appelAJAX] addTentative = ' + ret)
    return ret
}

async function resetTentative(){
    ret = await callApi('resetTentative')
    ret = ret['resetTentative']
    console.log('[appelAJAX] resetTentative = ' + ret)
    return ret
}

