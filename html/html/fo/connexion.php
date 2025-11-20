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
        $_SESSION['idCompte'] = $tab['id_compte'];

        // Récupération des données de la bdd pour voir si c'est un vendeur ou un client
        $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._vendeur WHERE id_compte = ?");
        $stmt->execute([$_SESSION['idCompte']]);
        $estVendeur = $stmt->fetch(PDO::FETCH_ASSOC);

        // echo '<pre>';
        // print_r($estVendeur);
        // echo '<pre>';

        if($estVendeur['id_compte']){ 
            // Index vendeur + role vendeur
            $_SESSION['role'] = 'vendeur';
            header('Location: ../bo/index_vendeur.php');
        }
        else{
            $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._client WHERE id_compte = ?");
            $stmt->execute([$_SESSION['idCompte']]);
            $estClient = $stmt->fetch(PDO::FETCH_ASSOC);

            // echo '<pre>';
            // print_r($estClient);
            // echo '<pre>';
            
            if($estClient['id_compte']){ 
                // Index client + role client
                $_SESSION['role'] = 'client';
                header('Location: ../fo//index.php');
            }
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
        <h2 class="md:flex md:flex-col md:items-center">Connexion</h2>
        <form method="post">
            <div class="md:flex md:flex-col md:items-center md:ml-10 md:mb-7 md:mr-10">

                <div class="flex w-fit flex-col items-start">
                    <label for="mail">Adresse mail :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 mb-5 md:w-150 h-15 w-70" type="text" name="mail" id="mail" value="<?= isset($_POST['mail'])? $_POST['mail'] : "" ?>" required>
                </div>

                <div class="flex w-fit flex-col mt-6 items-start">
                    <label for="mdp">Mot de passe :</label>
                    <input id="mdp" class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 w-150 h-15" name="mdp" id="mdp"  value="<?= isset($_POST['mdp'])? $_POST['mdp'] : "" ?>" required>
                    
                    <!-- oeil pour afficher/cacher le mdp -->
                    <button type="button" class="bouton-modifier group/eye cursor-pointer">
                        <img src="/../../../images/logo/bootstrap_icon/eye.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                        <img src="/../../../images/logo/bootstrap_icon/eye-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                    </button>
                    <button type="button" class="group/valider cursor-pointer bouton-valider">
                        <img src="/../../../images/logo/bootstrap_icon/eye-slash.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/valider:hidden">
                        <img src="/../../../images/logo/bootstrap_icon/eye-slash-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/valider:block">
                    </button>

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

        <script>
            document.querySelectorAll(".modif-attribut .bouton-modifier, .modif-attribut .groupe-bouton").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent
                    const boutonEye = container.querySelector(".bouton-modifier"); // oeil
                    const groupeEyeSlash = container.querySelector(".groupe-bouton"); // oeil slash

                    groupeBouton.classList.toggle("hidden");
                    groupeBouton.classList.toggle("flex");  
                    
                    boutonModifier.classList.toggle("hidden");
                    boutonModifier.classList.toggle("block");
                });
            });
            document.querySelectorAll(".modif-attribut .bouton-valider").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent
                    var passwordInput = document.getElementById("mdp");
                    passwordInput.type = 'password';

                });
            });
            document.querySelectorAll(".modif-attribut .bouton-annuler").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent
                    var passwordInput = document.getElementById("mdp");

                    passwordInput.type = 'text';
                    
                });
            });
        </script>

    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
