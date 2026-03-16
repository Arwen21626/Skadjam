<?php
    session_start(); // Démarrage de la session

    // Initialisation du rôle visiteur si aucun rôle n'est défini en session
    if (!isset($_SESSION['role'])){
        $_SESSION['role'] = 'visiteur';
    }

    $erreur = false;
    include __DIR__ . '/../../01_premiere_connexion.php'; // Connexion à la base de données

    // Traitement du formulaire de connexion
    if(isset($_POST['mdp']) && isset($_POST['mail'])){
        $erreur = false;

        // Récupération et sécurisation des données du formulaire
        $mail = htmlentities($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);

        // Récupération des données du compte correspondant à l'adresse mail saisie
        $stmt = $dbh->prepare("SELECT id_compte, mot_de_passe, code_secret FROM sae3_skadjam._compte WHERE adresse_mail = ?");
        $stmt->execute([$mail]);
        $tab = $stmt->fetch(PDO::FETCH_ASSOC);
        $dataConnexion = [];

        if($tab){
            // Vérification du mot de passe avec le hash stocké en base
            $passCorrect = password_verify($mdp, $tab['mot_de_passe']);
    
            if ($passCorrect){
                // Initialisation de la session après confirmation du mot de passe
                $dataConnexion['idCompte'] = $tab['id_compte'];
    
                // Vérifie si le compte est un vendeur ou un client
                $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._vendeur WHERE id_compte = ?");
                $stmt->execute([$dataConnexion['idCompte']]);
                $role = $stmt->fetch(PDO::FETCH_ASSOC);
                $dataConnexion['role'] = ($stmt->rowCount() > 0) ? 'vendeur' : 'client';

                // Si le compte n'a pas de code secret A2F, on le considère directement connecté
                if (!$tab['code_secret']){
                    $dataConnexion['connecte'] = true;
                }
                
                // Sauvegarde du contexte d'achat pour rediriger vers le panier après connexion
                if (isset($_POST['veutAcheter'])){
                    $dataConnexion['veutAcheter'] = $_POST['veutAcheter'];
                } else {
                    $dataConnexion['veutAcheter'] = null;
                }

                // Sauvegarde de l'id produit pour rediriger vers la fiche produit après connexion
                if (isset($_POST['idProduit'])){
                    $dataConnexion['idProduit'] = $_POST['idProduit'];
                } else {
                    $dataConnexion['idProduit'] = null;
                }

                // Stockage des données de connexion en session avant redirection vers l'A2F
                $_SESSION['dataConnexion'] = $dataConnexion;

                header('Location: ./authentification.php');
                exit();
                
            } else {
                // Erreur détecté dans l'adresse mail ou le mot de passe
                $erreur = true;
            }
        } else {
            // Erreur détecté dans l'adresse mail ou le mot de passe
            $erreur = true;
        }
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . "/../../php/structure/head_front.php"?>
    <title>Connexion</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_front.php"; ?>
    <main class="flex flex-col self-center md:min-h-[615px]">
        <h2>Connexion</h2>
        <form method="post">
        <?php if(isset($_GET['idProduit'])){ ?>
            <!-- Transmet l'id du produit en champ caché pour rediriger après connexion -->
            <input name="idProduit" id="idProduit" value="<?php echo $_GET['idProduit'];?>" class="hidden w-1">
        
        <?php }?>
        <?php if (isset($_POST['veutAcheter'])) {?> 
            <!-- Transmet l'intention d'achat en champ caché pour rediriger vers le panier après connexion -->
            <input type="hidden" name="veutAcheter" value="V">
        <?php } ?>

            <div class="flex flex-col items-center ">
                <div class="flex flex-col items-start space-y-4">
                    <!--Champ mail  -->
                    <div id="mailForm" class="flex flex-col md:w-[550px]">
                        <label for="mail">Adresse mail :</label>
                        <input class="cursor:default border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " type="text" name="mail" id="mail" value="<?= isset($_POST['mail'])? $_POST['mail'] : "" ?>" required>
                    </div>

                    <!-- Champ MDP -->
                    <div id="mdpForm" class="flex flex-col md:w-[550px]">
                        <label for="mdp">Mot de passe :</label>
                        <div class="zone-mdp flex flex-row">
                            <!--le flex row sert a alinger l'oeil me demander pas pourquoi (signé Arwen et svp touchez plus) -->
                            <input id="mdp" type="password" class="champ-mdp border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " name="mdp" id="mdp"  value="<?= isset($_POST['mdp'])? $_POST['mdp'] : "" ?>" required>
                            <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                        </div>

                        <!-- Renvoie sur la page de réinitialisation de mot de passe -->
                        <a href="reinitialiser_mdp.php" class="underline self-end cursor-pointer hover:text-rouge">Mot de passe oublié ?</a>
                    </div>
                </div>
                
                <div class=" flex w-fit flex-col mt-6 items-center ">
                    <!-- Si erreur détecté -->
                    <?php if($erreur){ ?>
                        <p id="erreurConnection" class="text-rouge items-center"><?php echo 'adresse mail ou mot de passe invalide';?></p>
                    <?php }?>
                </div>

                <!-- Boutons -->
                <div class="flex flex-row space-x-10 mb-7">
                    <div class=" justify-self-center">
                        <!-- Boutton de retour à l'index.php -->
                        <a href="/index.php"><button class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer" type="button">Annuler</button></a>
                    </div>
                    <div class=" justify-self-center">
                        <!-- Envoie des données en méthode POST pour se connecter -->
                        <input type="submit" value="Se connecter" class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer">
                    </div>
                </div>
            </div>
        </form>

        <!-- Renvoie sur la page de création d'un compte client -->
        <?php if (isset($_POST['veutAcheter'])) { //Modification pour rediriger vers le panier si le visiteur se crée un compte pour valider son panier?>
            <div class="flex flex-row flex-wrap justify-center m-2">
                <p class="mr-2">Pas encore client ? </p>
                <a href="./creation_compte_client.php?veutAcheter=V" class="underline! hover:text-rouge">Créer un compte client</a>
            </div>
        <?php } else { ?>
            <div class="flex flex-row flex-wrap justify-center m-2">
                <p class="mr-2">Pas encore client ? </p>
                <a href="./creation_compte_client.php" class="underline! hover:text-rouge">Créer un compte client</a>
            </div>
        <?php } ?>

        <!-- Renvoie sur la page de création d'un compte vendeur -->
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore vendeur ? </p>
            <a href="../bo/crea_compte_vendeur.php" class="underline! hover:text-rouge">Créer un compte vendeur</a>
        </div>

        <script src="../../js/verifForm.js"></script>
        <script>
            console.log("js")
            // initialisation
            let mail = document.getElementById("mail")
            let mdp = document.getElementById("mdp")

            // Verif nom
            let mailForm = document.getElementById("mailForm")
            let erreurMail = document.createElement("p")
            erreurMail.textContent = "L'adresse doit être au format : adresse@e.mail"
            erreurMail.classList.add("md:text-rouge", "text-rouge")

            mail.addEventListener("change", function(){
                if(!verifMail(mail.value)){                    
                    erreurMail.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurConnection")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurMail.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurConnection")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mailForm.appendChild(erreurMail)
            })

            // Verif nom
            let mdpForm = document.getElementById("mdpForm")
            let erreurMDP = document.createElement("p")
            erreurMDP.textContent = "1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum"
            erreurMDP.classList.add("md:text-rouge", "text-rouge")

            mdp.addEventListener("change", function(){
                if(!verifMotDePasse(mdp.value)){                    
                    erreurMDP.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurConnection")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurMDP.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurConnection")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mdpForm.appendChild(erreurMDP)
            })
        </script>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
<script src="../../js/bo/visibilite_mdp.js"></script>
</html>