<?php 
session_start();
$erreur = false;
include __DIR__ . '/../../01_premiere_connexion.php';
if(isset($_POST['mdp']) && isset($_POST['mail'])){
    // Initialisation des données
    $erreur = false;
    $mail = htmlentities($_POST["mail"]);
    $mdp = htmlentities($_POST["mdp"]);

    // Récupération des données de la bdd pour tester la connexion
    $stmt = $dbh->prepare("SELECT id_compte, mot_de_passe FROM sae3_skadjam._compte WHERE adresse_mail = ?");
    $stmt->execute([$mail]);
    $tab = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérification mot de passe
    $passCorrect = password_verify($mdp, $tab['mot_de_passe']);
    

    if ($passCorrect){
        // Initialisation de la session après confirmation du mot de passe
        $_SESSION['id_compte'] = $tab['id_compte'];

        // Récupération des données de la bdd pour voir si c'est un vendeur ou un client
        $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._vendeur WHERE id_compte = ?");
        $stmt->execute([$tab['id_compte']]);
        $estVendeur = $stmt->fetch(PDO::FETCH_ASSOC);

        if($estVendeur['id_compte']){ 
            // Vendeur
            header('Location: ../bo/index_vendeur.php');
        }else{ 
            // Client
            header('Location: ../fo/index.php');
        }
        
    }
    else{
        // Erreur détecté dans l'adresse mail ou le mot de passe
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
        <h2 class="flex flex-col items-center">Connexion</h2>
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
                    <!-- Renvoie sur la page de réinitialisation de mot de passe -->
                    <a href="reinitialiser_mdp.php" class="underline! self-end cursor-pointer hover:text-rouge">mot de passe oublié ?</a>
                </div class="flex w-fit flex-col mt-6 items-start">

                <div class=" justify-self-center mt-8 mb-8">
                    <!-- Envoie des données en méthode POST pour se connecter -->
                    <input type="submit" value="Se connecter" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-vertClair pl-3">
                </div>
                
                <div class=" flex w-fit flex-col mt-6 items-start ">
                    <!-- Si erreur détecté -->
                    <?php if($erreur){ ?>
                        <p class="text-rouge items-center"><?php echo 'adresse mail ou mot de passe invalide';?></p>
                    <?php }?>
                </div>
                
            </div>
        </form>
        <!-- Renvoie sur la page de création d'un compte client -->
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore client ? </p>
            <a href="./creation_compte_client.php" class="underline! hover:text-rouge">Créer un compte client</a>
        </div>
        <!-- Renvoie sur la page de création d'un compte vendeur -->
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore vendeur ? </p>
            <a href="../bo/crea_compte_vendeur.php" class="underline! hover:text-rouge">Créer un compte vendeur</a>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
