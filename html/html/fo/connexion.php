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

    /* !! renvoie détails produit !! TODO */

    if ($passCorrect){
        // Initialisation de la session après confirmation du mot de passe
        $_SESSION['idCompte'] = $tab['id_compte'];

        // Récupération des données de la bdd pour voir si c'est un vendeur ou un client
        $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._vendeur WHERE id_compte = ?");
        $stmt->execute([$_SESSION['idCompte']]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        $_SESSION['role'] = 'vendeur';
        
        if($role == null){
            $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._client WHERE id_compte = ?");
            $stmt->execute([$_SESSION['idCompte']]);
            $role = $stmt->fetch(PDO::FETCH_ASSOC);
            $_SESSION['role'] = 'client';
        }

        print_r($_SESSION);
        
        
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
    <main class="min-h-[150px]">
        <h2 class="flex flex-col items-center">Connexion</h2>
        <form method="post">

        <?php $_GET['idProduit'] = 0?>
        <input name="idProduit" id="idProduit" value="<?php echo $_GET['idProduit'];?>" class="hidden">

            <div class="flex flex-col items-center md:ml-10 md:mb-7 md:mr-10">

                <div class="flex w-fit flex-col items-start">
                    <label for="mail">Adresse mail :</label>
                    
                    <div class="flex modif-attribut float-rigth">
                        <input class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 mb-5 md:w-150 h-15 w-70" type="text" name="mail" id="mail" value="<?= isset($_POST['mail'])? $_POST['mail'] : "" ?>" required>
                        <div>
                            <div class="w-15! h-15! cursor:default"></div>
                        </div>
                    </div>
                </div>

                <div class="flex w-fit flex-col mt-6 items-start">

                    <label for="mdp">Mot de passe :</label>

                    <div class="flex w-fit flex-wrap relative items-center"> <!-- div pour rassembler l'input et le bouton -->
                        <input id="mdp" class="ml-5 border-5 border-solid rounded-2xl border-vertClair pl-3 md:w-150 h-15 w-70" name="mdp" id="mdp"  value="<?= isset($_POST['mdp'])? $_POST['mdp'] : "" ?>" required>
                    
                        <!-- oeil pour afficher/cacher le mdp -->
                         <div class="flex modif-attribut float-rigth">  <!-- div pour changer les boutons  -->
                            <button type="button" class="bouton-modifier group/eye cursor-pointer ">
                                <img src="/../../../images/logo/bootstrap_icon/eye.svg" alt="modifier" title="modifier" class="w-9! h-9! ml-4 block group-hover/eye:hidden relative md:w-12! md:h-12!">
                                <img src="/../../../images/logo/bootstrap_icon/eye-fill.svg" alt="modifier" title="modifier" class=" w-9! h-9! ml-4 hidden group-hover/eye:block relative md:w-12! md:h-12!">
                            </button>
                            <button type="button" class="group/valider cursor-pointer bouton-valider hidden">
                                <img src="/../../../images/logo/bootstrap_icon/eye-slash.svg" alt="valider" title="valider" class=" w-9! h-9! ml-4 block group-hover/valider:hidden relative md:w-12! md:h-12!">
                                <img src="/../../../images/logo/bootstrap_icon/eye-slash-fill.svg" alt="valider" title="valider" class=" w-9! h-9! ml-4 hidden group-hover/valider:block relative md:w-12! md:h-12!">
                            </button>
                        </div>
                    </div>

                    <br>
                    <!-- Renvoie sur la page de réinitialisation de mot de passe -->
                    <a href="reinitialiser_mdp.php" class="underline! self-end cursor-pointer hover:text-rouge">mot de passe oublié ?</a>
                </div>

                <div class=" justify-self-center mt-8 mb-8">
                    <!-- Envoie des données en méthode POST pour se connecter -->
                    <input type="submit" value="Se connecter" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-vertClair pl-3">
                </div>
                
                <div class=" flex w-fit flex-col mt-6 items-center ">
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
            var passwordInput = document.getElementById("mdp");
            passwordInput.type = 'password';

            document.querySelectorAll(".modif-attribut .bouton-modifier, .modif-attribut .bouton-valider").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent
                    const boutonEye = container.querySelector(".bouton-modifier"); // oeil
                    const boutonSlash = container.querySelector(".bouton-valider"); // oeil slash

                    boutonSlash.classList.toggle("hidden");
                    boutonSlash.classList.toggle("block");  
                    
                    boutonEye.classList.toggle("hidden");
                    boutonEye.classList.toggle("block");
                });
            });
            document.querySelectorAll(".modif-attribut .bouton-valider").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent
                    passwordInput.type = 'password';

                });
            });
            document.querySelectorAll(".modif-attribut .bouton-modifier").forEach(button => {
                button.addEventListener("click", () => {
                    const container = button.closest(".modif-attribut"); // parent

                    passwordInput.type = 'text';
                    
                });
            });
        </script>

    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
