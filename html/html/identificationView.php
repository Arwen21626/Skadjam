<?php
ob_start();
require_once __DIR__.'/../01_premiere_connexion.php';
require_once __DIR__.'/../php/identification.php';
session_start();

//init variables
$role = (isset($_SESSION['role']))? $_SESSION['role']: null;
$idClient = (isset($_SESSION['idCompte']))?$_SESSION['idCompte']:null;
$redirect = (isset($_SESSION['redirect']))?$_SESSION['redirect']:null;
$action = (isset($_SESSION['action']))?$_SESSION['action']:null;
if (is_null($action)){
    $action = (isset($_POST['action']))?$_POST['action']:null;
}
$erreur = [];
$identifie = false;

if (is_null($role) || is_null($idClient) || $role === 'visiteuir' || is_null($redirect)){
    $_SESSION['role'] = 'visiteur';
    header('Location: /html/fo/connexion.php');
    exit;
}
$_SESSION['totp'] = false; // CORRIGÉ : bool au lieu de 1


if ($action === 'identVerif'){
    if (!isset($_POST['password'])){$erreur['password'] = 0;}
    
    if (empty($erreur)){
        $password = $_POST['password'];
        $data = verif_id($password, $idClient);
        $passValide = $data['pass'];
        $code = $data["code"];
        if (!$passValide){
            $action = 'identRequest';
        }else{
            $compteValide = true;
            error_log(print_r($code,true));
            if (!$code){
                $identifie = true;
            }else{
                 // CORRIGÉ : false = a passé par TOTP (pas de redirection directe)
                $action = 'authRequest';
            }
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'authVerif'){
    error_log("\n delamerde\n\n\n\n");
    $identifie = (isset($_POST['auth'])) ? ($_POST['auth'] === 'valide') : false;
    error_log("identifie ".$identifie."\n\n\n\n");
    error_log((true == 0)?"true": "false");
    $_SESSION['totp'] = true;
}

if ($identifie){
    error_log("have totp = ".($_SESSION['totp'] ? 'true' : 'false'));
    error_log(print_r($_SESSION,true));
    if (!$_SESSION['totp']){ // CORRIGÉ : true = pas de TOTP → redirection directe
        header("Location: ".$redirect);
    }else{
        ob_clean();
        echo json_encode(['url' => $redirect]); // false = vient du TOTP → réponse AJAX
    }
    $_SESSION['session_confirme'] = true;
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php ($role === 'client') ? require_once __DIR__.'/../php/structure/head_front.php' : require_once __DIR__.'/../php/structure/head_back.php' ?>
    <title>confirmation d'identité</title>
</head>
<body class="show flex flex-col min-h-screen">
<?php ($role === 'client') ? require_once __DIR__.'/../php/structure/header_front.php' : require_once __DIR__.'/../php/structure/header_back.php' ?>

<main class="flex-1 flex flex-col justify-center">

<?php
$action = (is_null($action))?'identRequest':$action;
if ($action === 'identRequest'){ ?>
    <h2>Identification</h2>
    <div class="flex flex-col items-center">

        <form action="identificationView.php" method="POST">
            <input type="hidden" name="action" value="identVerif">

            <div class="flex flex-col md:w-[550px]">
                <label for="password">Mot de passe :</label>
                <div class="zone-mdp flex flex-row">
                    <input id="password" type="password" class="champ-mdp border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " name="password" id="mdp"  value="<?= isset($_POST['password'])? $_POST['password'] : "" ?>" required>
                    <?php include __DIR__ . "/../php/structure/bouton_mdp.php" ?>
                </div>

                <a href="reinitialiser_mdp.php" class="underline self-end cursor-pointer hover:text-rouge">Mot de passe oublié ?</a>
            </div>

            <div class="flex flex-row space-x-10 mb-7">
                <div class=" justify-self-center">
                    <button class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer" onclick="window.history.back()">Annuler</button>
                </div>
                <div class=" justify-self-center">
                    <input type="submit" value="Valider" class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer">
                </div>
            </div>

        </form>
<?php
}

if ($action === 'authRequest'){
?>
    <h2>Authentification</h2>
    
    <?php include __DIR__.'/../php/structure/authentikATOR/input_code.php' ?>
    <p id="result" class="hidden self-center"></p>
<?php
}
?>
    </div>
</main>
<?php ($role === 'client') ? require_once __DIR__.'/../php/structure/footer_front.php' : require_once __DIR__.'/../php/structure/footer_back.php' ?>
<script src="../../js/bo/visibilite_mdp.js"></script>
<script src="./../../php/structure/authentikATOR/appelAJAX.js"></script>
<script>
    const res = document.getElementById("result");
    const btnAction = document.getElementById("btn-action")
    let btnAnnuler = document.createElement("button")
    btnAnnuler.classList.add("border-vertClair", "border-2", "rounded-xl", "w-40", "h-14", "cursor-pointer", "m-5")
    btnAnnuler.textContent = "Annuler"
    btnAction.classList.add("flex", "flex-row", "justify-between")
    btnAction.prepend(btnAnnuler)

    btnAnnuler.addEventListener("click", () => {window.history.back()})

    goFirst()
    async function submit(idClient){
        
        res.style.color = "black"
        res.classList.add("hidden")
        initParam(idClient) // edition=0 par défaut : lecture seule, on vérifie juste le code
        let code = recup_code()
        let ret = await verifOtp(code) // CORRIGÉ : retourne true/false
        console.log("[authentification] connection : " + ret)
        if (ret === true) { // CORRIGÉ : true = code valide (plus de == 0)
            valider.textContent = "Connexion..."
            res.textContent = "Code bon."
            res.classList.remove("hidden")

            let data = new FormData()
            data.append('auth', 'valide')
            data.append('action', 'authVerif')

            await fetch('identificationView.php', {method: 'post', body: data})
            .then(r => {
                console.log("data url = " + r)
                return r.json()
            })
            .then(r => {
                console.log("url = " + r)
                window.location.href = r.url
            })
        } else { // Code incorrect
            res.classList.remove("hidden")
            let addT = await addTentative()
            console.log("[authentification] addT " + addT)
            let result = await getTentative()
            console.log("[authentification] result " + result)
            let nbTentative = result['tentative']
            console.log("[authentification] nbTentative " + nbTentative)
            res.style.color = "#A70101"
            res.textContent = "Code incorrect, réessayez. " + (3 - nbTentative) + " essais restants."
            if (nbTentative == 3){
                await addTempsRestant()
                await resetTentative()
                window.location.href = "./authentification.php"
            }
        }
    }
</script>
</body>
</html>