<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass A2F</title>
    <?php include __DIR__.'/../../php/structure/head_front.php' ?>
</head>
<body>
    <?php include __DIR__.'/../../php/structure/header_front.php' ?>
    
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

        <button id="gen-key">Générer</button>
        <pre id="txt-key"></pre>
        <img src="" alt="QR code" id="img-qr-code" class=" hidden" width="180px" height="180px">
        <label id="lbl-code" for="code" class="hidden">Entrez le code : </label>
        <input id="input-code" name="code" type="text" class=" border-2 border-gray-500 hidden" maxlength="6" size="6">
        <p id="result" class="hidden"></p>
    </main>
    
    <?php include __DIR__.'/../../php/structure/footer_front.php' ?>
</body>
<script>
    const btn_gen = document.getElementById("gen-key");
    const input = document.getElementById("input-code");
    const txt_key = document.getElementById("txt-key");
    const img_qr = document.getElementById("img-qr-code");
    const res = document.getElementById("result");
    const label = document.getElementById("lbl-code");

    btn_gen.addEventListener('click', () => 
    {
        fetch('../../php/structure/authentikATOR/create_secret.php')
        .then(r => r.json())
        .then(data => {
            txt_key.textContent = data.secret
            img_qr.src = data.qrcode
            img_qr.style.display = "block"
            input.style.display = "block"
            
        });
    })
    
    input.addEventListener('keyup', () => {
        text = input.value;
        reg = /^[0-9]{6}$/
        let donnees = new FormData();

        if (reg.test(text)){
            console.log("nice");
            donnees.append('secret', txt_key.textContent);
            donnees.append('code', text);
            fetch('../../php/structure/authentikATOR/verify_otp.php', {
                method: 'post',
                body: donnees
            })
            .then(r => r.json())
            .then(data => {
                res.textContent = data.verify;
                res.style.display = "block"
                label.style.display = "block"
            })
        }else{
            console.log("nul")
        }
    })

</script>
</html>