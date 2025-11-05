<?php
include("connexion.php");
include("verification_formulaire.php");

/* initialiser toutes les variables avant l'affichage de la page */
    $errConfirmation = '';
    $errExist = '';
    $errMail = '';
    $errTel = '';
    $errNom = '';
    $errMdp = '';  

    $nom = '';
    $prenom = '';
    $mail = '';
    $tel = '';
    $denomination = '';
    $adresse = '';
    $iban = '';
    $siren = '';
    $mdp = '';
    $verif = '';
    $erreurs = [];
if (isset($_POST["nom"])){
  
    //récuperer les attributs du post
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];
    $mail = $_POST["mail"];
    $tel = "+33". substr($_POST["tel"], 1) ;
    $denomination = $_POST["denomination"];
    $adresse = $_POST["adresse"];
    $siren = $_POST["siren"];
    $mdp = $_POST["mdp"];
    $verif = $_POST["verif"];

    /* enregistrer toutes les erreurs */
    if (!verifNomPrenom($nom)) $erreurs["nom"] = "lettre majuscule ou minuscule seulement";
    if (!verifNomPrenom($prenom)) $erreurs["prenom"] = "lettre majuscule ou minuscule seulement";
    if (!verifMail($mail)) $erreurs["mail"] = "format incorrecte";
    if (!mailUnique($mail)) $erreurs["unique"] = "un utilisateur avec cette e-mail existe deja : $mail";
    if (!verifTelephone($tel)) $erreurs["tel"] = "numéro à 10 chiffres";
    if (!verifMotDePasse($mdp)) $erreurs["mdp"] = "doit inclure tous les éléments si dessous";
    if (!confirmationMotDePasse($verif, $mdp)) $erreurs["conf"] = "le mot de passe est différent";

    /* s'il n'y a pas d'erreur faire la requete */
    if (empty($erreurs)){
        //preparer la requete sql pour inserer dans le compte
        $stmt = $pdo->prepare("INSERT INTO _compte (nom_compte, prenom_compte, adresse_mail, motDePasse, numero_telephone, bloque) VALUES (?,?,?,?,?, false)");
        //excuter la requete sql avec les attributs
        $stmt->execute([$nom, $prenom,$mail, password_hash($mdp, PASSWORD_DEFAULT), $tel]);
    }
    
    //preparer la requete sql pour inserer dans vendeur
    //$stmt = $pdo->prepare("INSERT INTO _vendeur (raison_sociale, siren, denomination");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style_form_creation_vendeur.css">
    <link rel="stylesheet" href="../css/general_back.css">
    <title>Créer un compte vendeur</title>
</head>
<body>
    <main>
        <h2>Inscription Vendeur</h2>
        <form method="POST">

            <h3 class="underline">Informations vendeur :</h3>
            <div class="zone-form">
                <!-- à la validation du formulaire, s'il y a des erreurs, les informations valides resteront saisies -->
                <div class="case-form">
                    <label for="nom">Nom * :</label>
                    <input type="text" id="nom" name="nom" value="<?= (!$errNom && isset($_POST["nom"])) ? $_POST["nom"] : '' ?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (!empty($erreurs) && isset($erreurs["nom"])) ? "<p class=\"erreur\">" . $erreurs["nom"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="prenom">Prénom * :</label>
                    <input type="text" id="prenom" name="prenom" value="<?= (!$errNom && isset($_POST["prenom"])) ? $_POST["prenom"] : '' ?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (!empty($erreurs) && isset($erreurs["prenom"])) ? "<p class=\"erreur\">" . $erreurs["prenom"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="mail">Mail * :</label>
                    <input type="email" id="mail" name="mail" value="<?= (!($errMail || $errExist) && isset($_POST["mail"])) ? $_POST["mail"] : ''  ?>" size="40" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (!empty($erreurs) && isset($erreurs["mail"])) ? "<p class=\"erreur\">" . $erreurs["mail"] . " </p>" : '' ?>
                    <?php echo (!empty($erreurs) && isset($erreurs["unique"])) ? "<p class=\"erreur\">" . $erreurs["unique"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="tel">Numéro de téléphone * :</label>
                    <input type="tel" id="tel" name="tel" value="<?= $_POST["tel"] ?? ''?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (!empty($erreurs) && isset($erreurs["tel"])) ? "<p class=\"erreur\">" . $erreurs["tel"] . " </p>" : '' ?>
                </div>
            </div>

            <h3 class="underline">Informations entreprise :</h3>
            <div class="zone-form">
                <div class="case-form">
                    <label for="denomination">Nom de l'entreprise * :</label>
                    <input type="text" id="denomination" name="denomination" value="<?= $_POST["denomination"] ?? ''?>" size="40" required>
                </div>
                <div class="case-form">
                    <label for="adresse">Adresse du siège social * :</label>
                    <input type="text" id="adresse" name="adresse" value="<?= $_POST["adresse"] ?? ''?>" size="40" required>
                </div>
                <div class="case-form">
                    <label for="siren">Numéro de SIREN * :</label>
                    <input type="text" id="siren" name="siren" value="<?= $_POST["siren"] ?? ''?>" size="25" required>
                </div>
                <div class="case-form">
                    <label for="iban">Numéro de IBAN * :</label>
                    <input type="text" id="iban" name="iban" value="<?= $_POST["iban"] ?? ''?>" size="25" required>
                </div>
            </div>

            <h3 class="underline">Mot de passe :</h3>
            <div class="zone-form">
                <div class="case-form">
                    <label for="mdp">Mot de passe * :</label>
                    <input type="password" id="mdp" name="mdp" value="<?= (!$errMdp && isset($_POST["mdp"])) ? $_POST["mdp"] : ''?>" size="30" required>
                    <?php echo (!empty($erreurs) && isset($erreurs["mdp"])) ? "<p class=\"erreur\">" . $erreurs["mdp"] . " </p>" : '' ?>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <p>1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
                </div>
                <div class="case-form">
                    <label for="verif">Vérification du mot de passe * :</label>
                    <input type="password" id="verif" name="verif" value="<?= (!($errConfirmation || $errMdp) && isset($_POST["verif"])) ? $_POST["verif"] : ''?>" size="30" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (!empty($erreurs) && isset($erreurs["conf"])) ? "<p class=\"erreur\">" . $erreurs["conf"] . " </p>" : '' ?>
                </div>
            </div>

            <div class="inline milieu left">
                <label for="cgu" class="underline">J'ai lu et j'acccepte les conditions générales d'utilisation :</label>
                <input type="checkbox" required>
            </div>
            <div class="buttons-form">
                <input type="reset" value="Annuler">
                <input type="submit" value="Valider">
            </div>
        </form>
        <div class="inline center milieu">
            <p>Vous avez déjà un compte ?</p>
            <a href="connexion_vendeur" class="underline" >Connectez vous</a>
        </div>
    </main>
</body>
</html>
