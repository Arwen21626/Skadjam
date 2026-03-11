const API_URL = "/php/structure/authentikATOR/authAPI.php"

let baseParam

function initParam(idClient, secret = ""){
    console.log(secret)
    baseParam = {"societe": "Alizon", "idClient": idClient, "secret": secret}
}

async function callApi(action, params ={}){
    let data = new URLSearchParams({action, ...baseParam, ...params})

    let reponse = await fetch(API_URL,{
        method: "post",
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: data.toString()
    })
    console.log(reponse)
    return await reponse.json();
}

async function getSecret(){
    secretData = await callApi('getSecret')
    secretData = secretData
    console.log("secretData = "+secretData)
    return secretData
}

async function verifOtp(code){
    isOk = await callApi('verifOtp', {'userCode': code})
    isOk = isOk['verify']
    console.log("verif ok = "+isOk)
    return isOk
    
}

async function saveSecret(){
    isSave = await callApi('saveSecret')
    isSave['save']
    console.log("Save = "+isSave)
    return isSave
}

async function getTentative(){
    nbTentative = await callApi('getTentative')
    console.log("nbTentative = "+nbTentative)
    return nbTentative
}

async function getTempsRestant(){
    tempsRest = await callApi('getTempsRestant')
    console.log('temps = '+tempsRest)
    return tempsRest
}

async function addTempsRestant(){
    ret = await callApi('addTempsRestant')
    console.log("addtempsRestant "+ret)
    return ret
}

async function addTentative(){
    ret = await callApi('addTentative')
    console.log('addTentative ' + ret)
    return ret
}

