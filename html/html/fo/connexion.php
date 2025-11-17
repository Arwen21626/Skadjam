<?php 
session_start();
include __DIR__ . '/../../01_premiere_connexion.php';
if(isset($_POST['mdp']) && isset($_POST['mail'])){
    $erreur = false;
    $mail = $_POST["mail"];
    $mdp = $_POST["mdp"];
    //echo 'mail : '.$mail;
    //echo 'mdp : '.$mdp;
    $stmt = $dbh->prepare("SELECT * FROM sae3_skadjam._compte WHERE adresse_mail = ?");
    $stmt->execute([$mail]);
    $tab = $stmt->fetch(PDO::FETCH_ASSOC);
    //echo '<pre>';
    $passCorrect = password_verify($mdp, $tab['mot_de_passe']);
    //print_r($_SESSION);
    //print_r($tab);
    
    //echo '<pre>';

    if ($passCorrect){
        $_SESSION['id_compte'] = $tab['id_compte'];
        header('Location: /../index.php');
    }
    else{
        $erreur = true;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . "/../../php/structure/head_front.php"?>
<head>
    
    <title>connexion</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_front.php"; ?>
    <main>
        <h2>Connexion</h2>
        <form method="post">
            <div class="flex flex-col items-center ml-10 mb-7 mr-10">

                <div class="flex w-fit flex-col items-start">
                    <label for="mail">Adresse mail :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 mb-5 w-150 h-15" type="text" name="mail" id="mail" required>
                </div>

                <div class="flex w-fit flex-col mt-6 items-start">
                    <label for="mdp">Mot de passe :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 w-150 h-15" type="password" name="mdp" id="mdp" required>
                    <a href=""></a>
                    <br>
                    <a href="nouveau_mdp.php" class="underline! self-end cursor-pointer hover:text-rouge">mot de passe oublié ?</a>
                </div class="flex w-fit flex-col mt-6 items-start">

                <div class=" justify-self-center mt-8 mb-8">
                    <input type="submit" value="Se connecter" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-vertClair pl-3">
                </div>
                
                <div class=" flex w-fit flex-col mt-6 items-start ">
                    <?php if($erreur){ ?>
                        <p class="text-rouge"><?php echo 'adresse mail ou mot de passe invalide !';?></p>
                    <?php } ?>
                </div>
                
            </div>
        </form>
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore client ? </p>
            <a href=".crea_compte_client.php" class="underline! hover:text-rouge">Créer un compte client</a>
        </div>
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore vendeur ? </p>
            <a href="../bo/crea_compte_vendeur.php" class="underline! hover:text-rouge">Créer un compte vendeur</a>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
