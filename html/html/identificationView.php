<?php
ob_start();
require_once __DIR__.'/../01_premiere_connexion.php';
require_once __DIR__.'/../php/identification.php';
session_start();

//init variables
$role = (isset($_SESSION['role']))? $_SESSION['role']: null;
$idClient = (isset($_SESSION['idCompte']))?$_SESSION['idCompte']:null;
$redirect = (isset($_SESSION['redirect']))?$_SESSION['redirect']:null;
print_r($_SESSION['action']);
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
            if (!$code){
                $identifie = true;
            }
            else{
                $action = 'authRequest';
            }
        }
    }

}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'authVerif'){
    $identifie = (isset($_POST['auth'])) ? ($_POST['auth'] === 'valide') : false;
}

if ($identifie){
    $_SESSION['session_confirme'] = true;
    //header("Location: ".$redirect);
    ob_clean();
    echo json_encode(['url' => $redirect]);
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
<body class="show">

<main>
<?php ($role === 'client') ? require_once __DIR__.'/../php/structure/header_front.php' : require_once __DIR__.'/../php/structure/header_back.php' ?>

<?php
echo "action = ".$action;
$action = (is_null($action))?'identRequest':$action;
if ($action === 'identRequest'){ ?>
    <h2>Identification</h2>
    <div class="flex flex-col items-center">

        <form action="identificationView.php" method="POST">
            <input type="hidden" name="action" value="identVerif">
<!--
            <div class="flex flex-col md:w-[550px]">
                <label for="mail">Adresse mail :</label>
                <input class="cursor:default border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " type="text" name="mail" id="mail" value="<?= isset($_POST["mail"])? $_POST["mail"] : "" ?>" required>
            </div>
-->
            <div class="flex flex-col md:w-[550px]">
                <label for="password">Mot de passe :</label>
                <div class="zone-mdp flex flex-row">
                    <!--le flex row sert a alinger l'oeil me demander pas pourquoi (signé Arwen et svp touchez plus) -->
                    <input id="password" type="password" class="champ-mdp border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " name="password" id="mdp"  value="<?= isset($_POST['password'])? $_POST['password'] : "" ?>" required>
                    <?php include __DIR__ . "/../php/structure/bouton_mdp.php" ?>
                </div>

                <!-- Renvoie sur la page de réinitialisation de mot de passe -->
                <a href="reinitialiser_mdp.php" class="underline self-end cursor-pointer hover:text-rouge">Mot de passe oublié ?</a>
            </div>

            <div class="flex flex-row space-x-10 mb-7">
                <div class=" justify-self-center">
                    <!-- Boutton de retour à l'index.php -->
                    <button class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer" onclick="back()">Annuler</button>
                </div>
                <div class=" justify-self-center">
                    <!-- Envoie des données en méthode POST pour se connecter -->
                    <input type="submit" value="Valider" class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer">
                </div>
            </div>

        </form>
<?php
}
print_r("<br>".$action);
if ($action === 'authRequest'){
?>
    <h2>Authentification</h2>
    <?php include __DIR__.'/../php/structure/authentikATOR/input_code.php' ?>
    <p id="result" class="hidden "></p>
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

    async function submit(idClient){
        
        res.style.color = "black"
        res.classList.add("hidden")
        initParam(idClient)
        let code = recup_code()
        ret = await verifOtp(code) // Vérifie le code OTP saisi par l'utilisateur
        console.log("[authentification] connection : "+ret)
        if (ret == 0){ // Code correct
            valider.textContent = "Connexion..."
            res.textContent = "Code bon."
            res.classList.remove("hidden")

            // Envoi de la confirmation au PHP via AJAX pour finaliser la connexion
            data = new FormData()
            data.append('auth', 'valide')
            data.append('action', 'authVerif')

            await fetch('identificationView.php', {method: 'post', body: data})
            .then(r => {
                console.log("data url = "+r)
                return r.json() // Récupère l'URL de redirection renvoyée par le PHP
            })
            .then(r => {
                console.log("url = "+r)
                window.location.href = r.url // Redirige le navigateur vers l'URL reçue
            })
        } else { // Code incorrect
            res.classList.remove("hidden")
            addT = await addTentative()
            console.log("[authentification] addT "+addT)
            result = await getTentative()
            console.log("[authentification] result "+result)
            nbTentative = result['tentative']
            console.log("[authentification] nbTentative "+nbTentative)
            res.style.color = "#A70101"
            res.textContent = "Code incorrect, réessayez. "+(3-nbTentative)+" essais restants."
            if (nbTentative==3){
                ret = await addTempsRestant()
                ret1 = await resetTentative()
                window.location.href = "./authentification.php"
            }
        }
    }
</script>
</body>
</html>