<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";

$erreurs = $_SESSION['erreurs'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_front.php";?>
<head>
    <title>Création d'un compte client</title>
</head>

<body>
    <?php
    // Import du header
    include __DIR__."/../../php/structure/header_front.php";
    ?>
    
    <main style="margin: 0" class="flex flex-col justify-center">
        <?php
        // Import de la bar de navigation
        include __DIR__."/../../php/structure/navbar_front.php";
        ?>

        <h2 class="flex justify-center text-center">Création du compte client</h2>

        <!-- Formulaire -->
        <form class="flex flex-wrap p-15 pt-0 justify-around placeholder-gray-500" action="../../php/traitement_donnees_compte_client.php" method="post">
            <!-- Ajout d'un attribut au POST nécessaire à la redirection sur le panier en cas de volonté d'achat -->
            <?php if (isset($_GET['veutAcheter'])) { ?>
                <input type="hidden" name="veutAcheter" value="V">
            <?php } ?>
            <!-- Nom -->
            <div id="nomForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="nom">Nom* :</label>
                <input placeholder="Dupond" class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="text" name="nom" id="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
                <?php if (isset($erreurs['nom'])) { ?>
                    <p id="erreurNomPHP" class='text-rouge' style='font-size: 0.90em'>Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents.</p>
                <?php } ?>
            </div>

            <!-- Prénom -->
            <div id="prenomForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="prenom">Prénom* :</label>
                <input placeholder="Jean" class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
                <?php if (isset($erreurs['prenom'])) { ?>
                    <p id="erreurPrenomPHP" class='text-rouge' style='font-size: 0.90em'>Le prénom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces des accents.</p>
                <?php } ?>
            </div>

            <!-- Pseudo -->
            <div id="pseudoForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="pseudo">Pseudo* :</label>
                <input placeholder="Breizh22" class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="text" name="pseudo" id="pseudo"value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>" required>
                <?php if (isset($erreurs['pseudo'])) { ?>
                    <p id="erreurPseudoPHP" class='text-rouge' style='font-size: 0.90em'>Le pseudo ne peut que contenir des majuscules, des minuscules, des accents, des chiffres, des tirets, tirets du bas ou des espaces.</p>
                <?php } ?>
            </div>

            <!-- Date de naissance -->
            <div id="naissanceForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="naissance">Date de naissance* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="date" maxlength="8" name="naissance" id="naissance" value="<?= htmlspecialchars($old['naissance'] ?? '') ?>" required> 
                <?php if (isset($erreurs['naissance'])) { ?>
                    <p id="erreurNaissancePHP" class='text-rouge' style='font-size: 0.90em'>La date de naissance doit être conforme et vous devez être majeur.</p>
                <?php } ?>
            </div>

            <!-- Téléphone -->
            <div id="telForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="telephone">Telephone* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" maxlength="10" type="tel" name="telephone" id="telephone" placeholder="06 12 34 56 78" pattern="0[0-9]{9}" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" required>
                <?php if (isset($erreurs['telephone'])) { ?>
                    <p id="erreurTelPHP" class='text-rouge' style='font-size: 0.90em'>Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres.</p>
                    <?php } ?>
            </div>

            <!-- Adresse email -->
            <div id="mailForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mail">Adresse email* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="mail" name="mail" id="mail" value="<?= htmlspecialchars($old['mail'] ?? '') ?>" required>
                <?php if (isset($erreurs['mail'])) { ?>
                    <p id="erreurMailPHP" class='text-rouge' style='font-size: 0.90em'>L'adresse doit être au format : adresse@e.mail</p>
                <?php } ?>
            </div>

            <!-- Mot de passe -->
            <div id="mdpForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mdp">Mot de passe* :</label>
                <div class="zone-mdp flex flex-row ">
                    <input class="champ-mdp border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="password" name="mdp" id="mdp" value="<?= htmlspecialchars($old['mdp'] ?? '') ?>" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
                <?php if (isset($erreurs['mdp'])) { ?>
                    <p id="erreurMdpPHP" class='text-rouge' style='font-size: 0.90em'>1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
                <?php } ?>
            </div>

            <!-- Vérification du mot de passe -->
            <div id="mdpVerifForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="verifMdp">Vérification du mot de passe* :</label>
                <div class="zone-mdp flex flex-row ">
                    <input class="champ-mdp border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="password" name="verifMdp" id="verifMdp" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
                <?php if (isset($verifMdp)) { ?>
                    <p id="erreurVerifMdpPHP" class='text-rouge' style='font-size: 0.90em'>La vérification doit être identique à votre mot de passe.</p>
                <?php } ?>
            </div>

            <!-- Acceptation des CGU -->
            <div class="flex flex-row flex-wrap items-center mt-2 mb-2">
                <label for="cgu" class="underline! cursor-pointer hover:text-rouge">J'ai lu et j'acccepte les conditions générales d'utilisation</label>
                <input type="checkbox" id="cgu" name="cgu" required class="cursor-pointer ml-5 w-5 h-5">
            </div>

            <!-- Validation ou pas du formulaire -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="cursor-pointer border-2 border-vertClair md:rounded-2xl w-40 md:h-14 rounded-xl h-10 p-0 m-0 mr-10" type="button"><a href="/index.php">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertClair md:rounded-2xl w-40 md:h-14 rounded-xl h-10 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="S'inscrire">
            </div>
        </form>
        <div class="flex flex-row flex-wrap justify-center">
            <!-- Lien vers la page de connexion -->
            <div class="hover:text-rouge flex flex-row flex-wrap justify-center ml-10 mr-10 mb-10">
                <a href="connexion.php">Vous avez déjà un compte ?<span class="underline">Connectez vous</span></a>
            </div>

            <!-- Lien vers la création d'un compte vendeur -->
            <div class="hover:text-rouge flex flex-row flex-wrap justify-center ml-10 mr-10 mb-10">
                <a href="../bo/crea_compte_vendeur.php">Vous êtes un vendeur ?<span class="underline">Créer un compte vendeur</span></a>
            </div>
        </div>

        <script src="../../js/verifForm.js"></script>
        <script>
            // initialisation
            let nom = document.getElementById("nom")
            let prenom = document.getElementById("prenom")
            let pseudo = document.getElementById("pseudo")
            let naissance = document.getElementById("naissance")
            let tel = document.getElementById("telephone")
            let mail = document.getElementById("mail")
            let mdp = document.getElementById("mdp")
            let verifMdp = document.getElementById("verifMdp")

            // Verif nom
            let nomForm = document.getElementById("nomForm")
            let erreurNom = document.createElement("p")
            erreurNom.textContent = "Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
            erreurNom.classList.add("md:text-rouge", "text-rouge")

            nom.addEventListener("change", function(){
                if(!verifNomPrenom(nom.value)){                    
                    erreurNom.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurNom.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                nomForm.appendChild(erreurNom)
            })

            // Verif prenom
            let prenomForm = document.getElementById("prenomForm")
            let erreurPrenom = document.createElement("p")
            erreurPrenom.textContent = "Le prenom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
            erreurPrenom.classList.add("md:text-rouge", "text-rouge")

            prenom.addEventListener("change", function(){
                if(!verifNomPrenom(prenom.value)){                    
                    erreurPrenom.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurPrenom.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                prenomForm.appendChild(erreurPrenom)
            })

            // Verif pseudo
            let pseudoForm = document.getElementById("pseudoForm")
            let erreurPseudo = document.createElement("p")
            erreurPseudo.textContent = "Le pseudo ne peut que contenir des majuscules, des minuscules, des accents, des chiffres, des tirets, tirets du bas ou des espaces."
            erreurPseudo.classList.add("md:text-rouge", "text-rouge")

            pseudo.addEventListener("change", function(){
                if(!verifPseudo(pseudo.value)){                    
                    erreurPseudo.classList.remove("md:hidden", "hidden")
                }else{
                    erreurPseudo.classList.add("md:hidden", "hidden")
                }
                
                let errPHP = document.getElementById("erreurPseudoPHP")
                if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                pseudoForm.appendChild(erreurPseudo)
            })

            // Verif naissance
            let naissanceForm = document.getElementById("naissanceForm")
            let erreurNaissance = document.createElement("p")
            erreurNaissance.textContent = "La date de naissance doit être conforme et vous devez être majeur."
            erreurNaissance.classList.add("md:text-rouge", "text-rouge")

            naissance.addEventListener("change", function(){
                if(!verifNaissance(naissance.value)){                    
                    erreurNaissance.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNaissancePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurNaissance.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNaissancePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                naissanceForm.appendChild(erreurNaissance)
            })

            // Verif tel
            let telForm = document.getElementById("telForm")
            let erreurTel = document.createElement("p")
            erreurTel.textContent = "Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres."
            erreurTel.classList.add("md:text-rouge", "text-rouge")

            tel.addEventListener("change", function(){
                if(!verifTelephone(tel.value)){                    
                    erreurTel.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurTelPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurTel.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurTelPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                telForm.appendChild(erreurTel)
            })

            // Verif mail
            let mailForm = document.getElementById("mailForm")
            let erreurMail = document.createElement("p")
            erreurMail.textContent = "L'adresse doit être au format : adresse@e.mail"
            erreurMail.classList.add("md:text-rouge", "text-rouge")

            mail.addEventListener("change", function(){
                if(!verifMail(mail.value)){                    
                    erreurMail.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMailPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurMail.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMailPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mailForm.appendChild(erreurMail)
            })

            // Verif mdp
            let mdpForm = document.getElementById("mdpForm")
            let erreurMdp = document.createElement("p")
            erreurMdp.textContent = "1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum"
            erreurMdp.classList.add("md:text-rouge", "text-rouge")

            mdp.addEventListener("change", function(){
                if(!verifMotDePasse(mdp.value)){                    
                    erreurMdp.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMdpPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurMdp.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMdpPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mdpForm.appendChild(erreurMdp)
            })

            // Verif mdp
            let mdpVerifForm = document.getElementById("mdpVerifForm")
            let erreurVerifMdp = document.createElement("p")
            erreurVerifMdp.textContent = "La vérification doit être identique à votre mot de passe."
            erreurVerifMdp.classList.add("md:text-rouge", "text-rouge")

            verifMdp.addEventListener("change", function(){
                if(!confirmationMotDePasse(mdp.value, verifMdp.value)){                    
                    erreurVerifMdp.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurVerifMdpPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurVerifMdp.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurVerifMdpPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mdpVerifForm.appendChild(erreurVerifMdp)
            })

        </script>

    </main>
    <?php 
    // Import du footer
    include __DIR__."/../../php/structure/footer_front.php";
    ?>

</body>
<script src="../../js/bo/visibilite_mdp.js"></script>
</html>
