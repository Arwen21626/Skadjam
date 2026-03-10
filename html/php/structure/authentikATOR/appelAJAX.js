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

