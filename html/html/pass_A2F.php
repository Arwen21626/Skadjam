<?php
session_start();
$role = null;
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'visiteur'){
    $role = $_SESSION['role'];
    $idClient = $_SESSION['idCompte'];
    
} else {
    header("Location : /index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass A2F</title>
    <?php
    ($role === 'vendeur') ? include __DIR__.'/../php/structure/head_back.php' : include __DIR__.'/../php/structure/head_front.php';
    ?>
</head>
<body>
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/header_back.php' : include __DIR__.'/../php/structure/header_front.php' ?>
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/navbar_back.php' : include __DIR__.'/../php/structure/navbar_front.php' ?>
    
    <main class=" w-3/4">
        <h2>Authentification à deux facteurs</h2>

        <p>
            Pour sécuriser au mieux votre compte vous pouvez ici activer l'Authentification a deux facteurs.
            Vous aurez besoin de stocker une clé secret dans une application mobile d'Authentification comme Google Authenticator, Proton Authenticator ou d'autre qui génèrerons grace a cette clé un code à 6 chiffres.
        </p>

        Etape pour activer l'authentifiacation a deux facteurs
        <ol class=" list-decimal!">
            <li>Télécharger une des application d'authentifiacation</li>
            <li>Générer la clé secrete en appuyant sur générer</li>
            <li>Copier la clé en appuyant sur copier ou scanner le QR code pour enregistrer la clé</li>
            <li>Tester si l'authentifiacation fonctionne correctement en copiant le code à 6 chiffres généré</li>
            <li>Valider l'activation de l'Authentification a deux facteurs</li>
        </ol>

        <button id="gen-key" onclick="generer(<?= $idClient ?>)">Générer</button>
        <pre id="txt-key"></pre>
        <img src="" alt="QR code" id="img-qr-code" class=" hidden" width="180px" height="180px">
        <?php include __DIR__.'/../php/structure/authentikATOR/input_code.php' ?>
        <p id="result" class="hidden"></p>
        <button id="terminer" onclick="terminer(<?= $idClient ?>)" disabled class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Terminer</button>
    </main>
    
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/footer_back.php' : include __DIR__.'/../php/structure/footer_front.php' ?>
</body>
<script src="./../php/structure/authentikATOR/appelAJAX.js"></script>
<script>
    let ret = 1
    let secret
    const btn_gen = document.getElementById("gen-key");
    const input = document.getElementById("input-code");
    const txt_key = document.getElementById("txt-key");
    const img_qr = document.getElementById("img-qr-code");
    const res = document.getElementById("result");
    const btnTerminer = document.getElementById("terminer")
    

    function terminer(idCompte){
        initParam(idCompte,secret)
        console.log("termine")
        let ret = saveSecret()
        
    }

    async function generer(idCompte){
        initParam(idCompte,secret)
        data = await getSecret()
        secret = data['secret']
        qrcode = data['qrcode']
        console.log("secret : "+secret)
        console.log("qrcode : "+qrcode)
        txt_key.textContent = secret
        img_qr.src = qrcode
        img_qr.style.display = "block"
        input.style.display = "block"
    }

    async function submit(idCompte){
        initParam(idCompte,secret)
        const code = recup_code()
        console.log("[INFO] submit() — code saisi :", code)
        ret = await verifOtp(code)
        console.log("[INFO] submit() — vérifié :", ret)
        if (ret == 0) {
            res.textContent = "Code bon."
            res.classList.remove("hidden")
            btnTerminer.removeAttribute("disabled")
        }else{
            res.textContent = "Code incorrect, réessayez."
            res.classList.remove("hidden")
        }
    }

</script>
</html>