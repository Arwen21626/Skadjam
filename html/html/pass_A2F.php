<?php
include __DIR__.'/../php/structure/authentikATOR/AuthATOR.php';
include __DIR__ . '/../01_premiere_connexion.php';
session_start();
$role = null;
if (isset($_SESSION['role']) && $_SESSION['role'] !== 'visiteur'){
    $role = $_SESSION['role'];
    $idClient = $_SESSION['idCompte'];
    
} else {
    header("Location : /index.php");
}

$auth = new AuthATOR($dbh, "Alizon", $idClient, "", true);
$initBeforPhp = $auth->getInitBefor() ? 1 : 0; // CORRIGÉ : 1 = déjà configuré, 0 = pas encore

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass A2F</title>
    <?php
    ($role === 'vendeur') ? include __DIR__.'/../php/structure/head_back.php' : include __DIR__.'/../php/structure/head_front.php';
    ?>
</head>
<body class="flex flex-col min-h-screen">
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/header_back.php' : include __DIR__.'/../php/structure/header_front.php' ?>
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/navbar_back.php' : include __DIR__.'/../php/structure/navbar_front.php' ?>
    
    <main class="flex-1 p-4 relative md:p-8 flex flex-col items-center">
        <button onclick="window.history.back()" class="w-25 h-10 top-3 left-3 rounded-[8px] border-vertClair bg-white border-2 md:rounded-xl md:w-40 md:h-14 md:top-5 md:left-5 cursor-pointer m-5S absolute  z-5">Retour</button>

        <h2 class=" mt-8!">Authentification à deux facteurs</h2>
        <p class="m-2 md:w-3/4 md:m-4">
            Pour sécuriser au mieux votre compte, vous pouvez ici activer l'Authentification à deux facteurs. Vous aurez besoin de stocker une clé secrète dans une application mobile d'authentification comme <br><strong>Google Authenticator</strong>, <strong>Proton Authenticator</strong> ou d'autres qui généreront grâce à cette clé un code à 6 chiffres.
        </p>

        <section class="m-2 md:w-3/4">
            <h3>Étape pour activer l'authentification à deux facteurs :</h3>
            <ol class=" list-decimal! list-inside mt-4 ml-5">
                <li>Télécharger une des applications d'authentification</li>
                <li>Générer la clé secrète en appuyant sur générer</li>
                <li>Copier la clé en appuyant sur copier ou scanner le QR code pour enregistrer la clé</li>
                <li>Tester si l'authentification fonctionne correctement en copiant le code à 6 chiffres généré</li>
                <li>Valider l'activation de l'Authentification à deux facteurs</li>
            </ol>
        </section>
        <?php if ($initBeforPhp == 1){ // CORRIGÉ : 1 = déjà configuré → afficher "Supprimer" ?>
            <button id="supprimer" onclick="supprimer()" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5S">Supprimer A2F</button>

        <?php } else { ?>
            <button id="gen-key" onclick="generer(<?= $idClient ?>)" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5S">Générer</button>
        <?php } ?>        
        <section id="veiw-pass" class=" hidden flex-col items-center m-5">
            <div class="hidden md:flex flex-row items-center">
                <p class="m-2">Secret :</p>
                <pre  id="txt-key"></pre>
            </div>
            <div class="md:hidden flex flex-row items-center justify-between">
                <p>Code Secret : </p>
                <button id="btn-cpy" class="block border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-2" onclick="copier()">Copier</button>
            </div>
            <img src="" alt="QR code" id="img-qr-code"  class="md:w-1/3 border-vertClair border-2 rounded-xl m-4">
            <?php include __DIR__.'/../php/structure/authentikATOR/input_code.php' ?>
            <p id="result" class="hidden"></p>
            <button id="terminer" onclick="terminer(<?= $idClient ?>)" disabled class=" hidden border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Terminer</button>
        </section>
    </main>
    
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/footer_back.php' : include __DIR__.'/../php/structure/footer_front.php' ?>
    
    <section id="popup"  class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-40">
        <section class="flex z-10 bg-white flex-col absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 border-vertClair border-2 rounded-xl p-5 md:w-1/3">
            <p class=" self-center">Attention</p><br>
            <p>Etes-vous sur de voulour supprimer l'authentification à deux facteurs.</p>
            <form action="/php/supprimerOtp.php" class="flex flex-row justify-between">
                <button id="annuler" onclick="closePopUp()" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Annuler</button>
                <button id="supprimer" type="submit" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Supprimer</button>
            </form>
        </section>
    </section>
</body>
<script src="./../php/structure/authentikATOR/appelAJAX.js"></script>
<script>

    console.log("[pass_A2F] deb initBefor = " + "<?= $initBeforPhp ?>")
    let secret
    let qrcode
    
    const popup = document.getElementById("popup")
    const view = document.getElementById("veiw-pass")
    const btnGen = document.getElementById("gen-key")
    const input = document.getElementById("input-code")
    const txtKey = document.getElementById("txt-key")
    const imQr = document.getElementById("img-qr-code")
    const res = document.getElementById("result")
    const btnTerminer = document.getElementById("terminer")
    const btnCpy = document.getElementById("btn-cpy")
    

    async function terminer(idCompte){
        initParam(idCompte, secret, 1) // CORRIGÉ : 1 = mode édition
        console.log("[pass_A2F] termine")
        let ret = await saveSecret()
        history.back()
    }

    async function generer(idCompte){
        initParam(idCompte, secret, 1) // CORRIGÉ : 1 = mode édition (était 0, donc inversé)
        let data = await getSecret()
        secret = data['secret']
        qrcode = data['qrcode']
        let initBefor = data['init'] // 1 = déjà configuré, 0 = pas encore
        console.log("[pass_A2F] data :")
        console.log(data)
        console.log("[pass_A2F] initBefor = " + initBefor)
        showView()
    }

    async function supprimer(){
        showPopUp()
    }

    function showView(){
        console.log("[pass_A2F] secret : " + secret)
        console.log("[pass_A2F] qrcode : " + qrcode)
        txtKey.textContent = secret
        imQr.src = qrcode
        view.style.display = "flex"
        input.style.display = "flex"
        btnGen.style.display = "none"
        goFirst()
        input.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        })
    }

    async function showPopUp() {
        popup.style.display = "block"
    }

    async function closePopUp() {
        popup.style.display = "none"
    }

    async function suppPopUp(idCompte) {
        initParam(idCompte, secret, 0) // lecture seule, pas d'édition
        ret = await delSecret()
        window.location.replace("./pass_A2F.php")
    }

    async function submit(idCompte){
        initParam(idCompte, secret, 1) // CORRIGÉ : 1 = mode édition
        const code = recup_code()
        console.log("[pass_A2F] submit() — code saisi :", code)
        let ret = await verifOtp(code)
        console.log("[pass_A2F] submit() — vérifié :", ret)
        if (ret === true) { // CORRIGÉ : true = code valide (plus de == 0)
            res.textContent = "Code bon."
            res.classList.remove("hidden")
            btnTerminer.removeAttribute("disabled")
            btnTerminer.style.display = "block"
            input.style.display = "none"
            return true
        } else {
            res.textContent = "Code incorrect, réessayez."
            res.classList.remove("hidden")
            return false
        }
    }

    function sleep(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    async function copier() {
        await navigator.clipboard.writeText(txtKey.textContent)
        btnCpy.textContent = "Copié!"
        await sleep(500)
        btnCpy.textContent = "copier"
    }
</script>
</html>