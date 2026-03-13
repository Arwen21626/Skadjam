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
$initBeforPhp = ($auth->getInitBefor())?0: 1;

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
    
    <main class=" p-8 flex flex-col items-center">
        <h2>Authentification à deux facteurs</h2>
        <p class="w-3/4 m-4">
            Pour sécuriser au mieux votre compte, vous pouvez ici activer l'Authentification à deux facteurs. Vous aurez besoin de stocker une clé secrète dans une application mobile d'authentification comme Google Authenticator, Proton Authenticator ou d'autres qui généreront grâce à cette clé un code à 6 chiffres.
        </p>

        <section class=" w-3/4">
            <h3>Étape pour activer l'authentification à deux facteurs :</h3>
            <ol class=" list-decimal! list-inside mt-4 ml-5">
                <li>Télécharger une des applications d'authentification</li>
                <li>Générer la clé secrète en appuyant sur générer</li>
                <li>Copier la clé en appuyant sur copier ou scanner le QR code pour enregistrer la clé</li>
                <li>Tester si l'authentification fonctionne correctement en copiant le code à 6 chiffres généré</li>
                <li>Valider l'activation de l'Authentification à deux facteurs</li>
            </ol>
        </section>
        <?php if ($initBeforPhp==0){ ?>
            <button id="supprimer" onclick="supprimer()" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5S">Supprimer A2F</button>

        <?php } else {?>
            <button id="gen-key" onclick="generer(<?= $idClient ?>)" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5S">Générer</button>
        <?php } ?>        
        <section id="veiw-pass" class=" hidden flex-col items-center m-5">
            <pre id="txt-key"></pre>
            <img src="" alt="QR code" id="img-qr-code"  class="w-1/3 border-vertClair border-2 rounded-xl m-4">
            <?php include __DIR__.'/../php/structure/authentikATOR/input_code.php' ?>
            <p id="result" class="hidden"></p>
            <button id="terminer" onclick="terminer(<?= $idClient ?>)" disabled class=" hidden border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Terminer</button>
        </section>
    </main>
    
    <?php ($role === 'vendeur') ? include __DIR__.'/../php/structure/footer_back.php' : include __DIR__.'/../php/structure/footer_front.php' ?>
    
    <section id="popup"  class="hidden fixed inset-0 backdrop-blur-sm bg-black/30 z-40">
        <section class="flex z-10 bg-white flex-col absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 border-vertClair border-2 rounded-xl p-5 w-1/3">
            <p class=" self-center">Attention</p><br>
            <p>Etes-vous sur de voulour supprimer l'authentification à deux facteurs.</p>
            <section class="flex flex-row justify-between">
                <button id="annuler" onclick="closePopUp()" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Annuler</button>
                <button id="supprimer" onclick="suppPopUp(<?= $idClient ?>)" class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5">Supprimer</button>
            </section>
        </section>
    </section>
</body>
<script src="./../php/structure/authentikATOR/appelAJAX.js"></script>
<script>

    console.log("[pass_A2F] deb initBefor = "+ "<?= $initBeforPhp ?>")
    let secret
    let qrcode
    
    const popup = document.getElementById("popup")
    const view = document.getElementById("veiw-pass")
    const btn_gen = document.getElementById("gen-key")
    const input = document.getElementById("input-code")
    const txt_key = document.getElementById("txt-key")
    const img_qr = document.getElementById("img-qr-code")
    const res = document.getElementById("result")
    const btnTerminer = document.getElementById("terminer")
    

    async function terminer(idCompte){
        initParam(idCompte,secret)
        console.log("[pass_A2F] termine")
        let ret = await saveSecret()
        history.back()
    }

    async function generer(idCompte){
        initParam(idCompte,secret, 0)
        data = await getSecret()
        secret = data['secret']
        qrcode = data['qrcode']
        initBefor = data['init']
        console.log("[pass_A2F] data :")
        console.log(data)
        console.log("[pass_A2F] initBefor = "+initBefor)
        showView()
    }

    async function supprimer(){
        showPopUp()
    }

    function showView(){
        console.log("[pass_A2F] secret : "+secret)
        console.log("[pass_A2F] qrcode : "+qrcode)
        txt = "Secret : "+secret
        txt_key.textContent = txt
        img_qr.src = qrcode
        view.style.display = "flex"
        input.style.display = "flex"
        btn_gen.style.display = "none"
        goFirst()
        input.scrollIntoView({
            behavior: 'smooth', // animation fluide
            block: 'center'     // centrer verticalement
        })
    }

    async function showPopUp() {
        popup.style.display = "block"
    }

    async function closePopUp() {
        popup.style.display = "none"
        
        //showView()
    }

    async function suppPopUp(idCompte) {
        initParam(idCompte,secret)
        ret = await delSecret()
        window.location.replace("./pass_A2F.php")
    }

    async function submit(idCompte){
        initParam(idCompte,secret)
        const code = recup_code()
        console.log("[pass_A2F] submit() — code saisi :", code)
        ret = await verifOtp(code)
        console.log("[pass_A2F] submit() — vérifié :", ret)
        if (ret == 0) {
            res.textContent = "Code bon."
            res.classList.remove("hidden")
            btnTerminer.removeAttribute("disabled")
            btnTerminer.style.display = "block"
            input.style.display = "none"
            return true
        }else{
            res.textContent = "Code incorrect, réessayez."
            res.classList.remove("hidden")
            return false
        }
    }

</script>
</html>