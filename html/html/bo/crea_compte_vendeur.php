<?php
include("../SAE3/html/01_premiere_connexion.php");
include(__DIR__ . "../../php/verification_formulaire.php");
include(__DIR__ . "../../php/modification_variable.php");

/* initialiser toutes les variables avant l'affichage de la page */
    
    $nom = '';
    $prenom = '';
    $mail = '';
    $tel = '';
    $denomination = '';
    $raisonSociale = '';
    $iban = 'FR';
    $adresse = '';
    $ville = '';
    $cp = '';
    $siren = '';
    $mdp = '';
    $verif = '';
    

    $erreurs = [];
if (isset($_POST["nom"])){
  
    //récuperer les attributs du post
    $nom = $_POST["nom"]; //verif
    $prenom = format_prenom($_POST["prenom"]);//verif
    $mail = $_POST["mail"]; //verif
    $tel = $_POST["tel"]; //verif
    $denomination = $_POST["denomination"];//verif
    $raisonSociale = $_POST["raisonSociale"];//verif
    $iban = $_POST["iban"];//verif
    $adresse = $_POST["adresse"];
    $ville = $_POST["ville"];
    $cp = $_POST["cp"];
    $siren = $_POST["siren"];//verif
    $mdp = $_POST["mdp"];//verif
    $verif = $_POST["verif"];//verif

    /* enregistrer toutes les erreurs */

    /* NOM */
    if (!verifNomPrenom($nom)) $erreurs["nom"] = "lettre majuscule ou minuscule seulement";

    /* PRENOM */
    if (!verifNomPrenom($prenom)) $erreurs["prenom"] = "lettre majuscule ou minuscule seulement";

    /* MAIL */
    if (!verifMail($mail)) $erreurs["mail"] = "format incorrecte";
    if (!mailUnique($mail)) $erreurs["unique"] = "un utilisateur avec cette e-mail existe deja : $mail";

    /* TEL */
    if (!verifTelephone(format_tel($tel))) $erreurs["tel"] = "numéro à 10 chiffres";

    /* DENOMINATION */
    if (!verifDenomination($denomination)) $erreurs["denomination"] = "autorisé majuscules, minuscules et chiffres";

    /* RS */
    if (!verifDenomination($raisonSociale)) $erreurs["raisonSociale"] = "autorisé majuscules, minuscules et chiffres";

    /* IBAN */
    if (!verifIban($iban)) $erreurs["iban"] = "numéro IBAN invalide";

    /* MDP */
    if (!verifMotDePasse($mdp)) $erreurs["mdp"] = "doit inclure tous les éléments si dessous";
    if (!confirmationMotDePasse($verif, $mdp)) $erreurs["conf"] = "le mot de passe est différent";

    /* SIREN */
    if (!verifSiren($siren)) $erreurs["siren"] = "numéro SIREN invalide";

    /* ##### ADRESSE ##### */




    /* s'il n'y a pas d'erreur faire la requete */
    if (empty($erreurs)){
        try{
            $id = null;
            //preparer la requete sql pour inserer dans le compte
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._compte (nom_compte, prenom_compte, adresse_mail, motDePasse, numero_telephone, bloque) VALUES (?,?,?,?,?, false) RETURNING id_compte");
            //excuter la requete sql avec les attributs
            $stmt->execute([$nom, $prenom,$mail, password_hash($mdp, PASSWORD_DEFAULT),format_tel($tel) ]);
            
            //recuperer l'id du compte associé au vendeur
            $id = $stmt->fetchColumn();

            //preparer la requete sql pour inserer dans vendeur
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._vendeur (id_compte, raison_sociale, siren, iban, denomination) VALUES (?,?,?,?,?)");
            $stmt->execute([$id,$raisonSociale, (int)$siren, $iban, $denomination]);

        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/bo//general_back.css">
    <link rel="stylesheet" href="../../css/style_form_creation_vendeur.css">
    <title>Créer un compte vendeur</title>
</head>
<body>
    <?php //require_once("../../php/header_back.php") ?>
    <main>
        <h2>Inscription Vendeur</h2>
        <form method="POST">

            <h3 class="underline">Informations vendeur :</h3>
            <div class="zone-form ">
                <!-- à la validation du formulaire, s'il y a des erreurs, les informations valides resteront saisies -->
                <div class="case-form">
                    <label for="nom">Nom * :</label>
                    <input type="text" id="nom" name="nom" value="<?= (!isset($erreurs["nom"])) ? $nom : '' ?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["nom"])) ? "<p class=\"erreur\">" . $erreurs["nom"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="prenom">Prénom * :</label>
                    <input type="text" id="prenom" name="prenom" value="<?= (!isset($erreurs["prenom"])) ? $prenom : '' ?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["prenom"])) ? "<p class=\"erreur\">" . $erreurs["prenom"] . " </p>" : '' ?>
                </div>
                <div class="case-form ">
                    <label for="mail">Mail * :</label>
                    <input type="email" id="mail" name="mail" value="<?= (!(isset($erreurs["mail"]) || isset($erreurs["unique"]))) ? $mail : ''  ?>" size="40" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["mail"])) ? "<p class=\"erreur\">" . $erreurs["mail"] . " </p>" : '' ?>
                    <?php echo (isset($erreurs["unique"])) ? "<p class=\"erreur\">" . $erreurs["unique"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="tel">Numéro de téléphone * :</label>
                    <input type="tel" id="tel" name="tel" value="<?= (!isset($erreurs["tel"]))?$tel:''?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["tel"])) ? "<p class=\"erreur\">" . $erreurs["tel"] . " </p>" : '' ?>
                </div>
            </div>

            <h3 class="underline">Informations entreprise :</h3>
            <div class="zone-form">
                <div class="case-form">
                    <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                    <input type="text" id="raisonSociale" name="raisonSociale" value="<?= (!isset($erreurs["raisonSociale"]))? $raisonSociale: ''?>" size="40" required>
                    <?php echo (isset($erreurs["raisonSociale"])) ? "<p class=\"erreur\">" . $erreurs["raisonSociale"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="denomination">Nom de l'entreprise * :</label>
                    <input type="text" id="denomination" name="denomination" value="<?= (!isset($erreurs["denomination"]))? $denomination: ''?>" size="40" required>
                    <?php echo (isset($erreurs["denomination"])) ? "<p class=\"erreur\">" . $erreurs["denomination"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="siren">Numéro de SIREN * :</label>
                    <input type="text" id="siren" name="siren" value="<?= (!isset($erreurs["siren"]))?$siren: ''?>" size="25" required>
                    <?php echo (isset($erreurs["siren"])) ? "<p class=\"erreur\">" . $erreurs["siren"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="iban">Numéro de IBAN * :</label>
                    <input type="text" id="iban" name="iban" value="<?= (!isset($erreurs["iban"]))?$iban: 'FR'?>" placeholder="FR" size="30" required>
                    <?php echo (isset($erreurs["iban"])) ? "<p class=\"erreur\">" . $erreurs["iban"] . " </p>" : '' ?>
                </div>
            </div>

            <!-- ########## ADRESSE ########## -->
            <h3 class="underline">Siège social :</h3>
            <div class="zone-form">
                <div class="case-form">
                    <label for="adresse">Adresse * :</label>
                    <input type="text" id="adresse" name="adresse" value="<?= $_POST["adresse"] ?? ''?>" size="40" placeholder="ex : 3 rue des camélia" required>
                    <?php echo (isset($erreurs["adresse"])) ? "<p class=\"erreur\">" . $erreurs["adresse"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="ville">Ville * :</label>
                    <input type="text" id="ville" name="ville" value="<?= $_POST["ville"] ?? ''?>" size="40" required>
                    <?php echo (isset($erreurs["ville"])) ? "<p class=\"erreur\">" . $erreurs["ville"] . " </p>" : '' ?>
                </div>
                <div class="case-form">
                    <label for="cp">Code Postal * :</label>
                    <input type="text" id="cp" name="cp" value="<?= $_POST["cp"] ?? ''?>" size="40" required>
                    <?php echo (isset($erreurs["cp"])) ? "<p class=\"erreur\">" . $erreurs["cp"] . " </p>" : '' ?>
                </div>
                
            </div>

            <h3 class="underline">Mot de passe :</h3>
            <div class="zone-form">
                <div class="case-form">
                    <label for="mdp">Mot de passe * :</label>
                    <input type="password" id="mdp" name="mdp" value="<?= (!isset($erreurs["mdp"]) && isset($_POST["mdp"])) ? $_POST["mdp"] : ''?>" size="30" required>
                    <?php echo (isset($erreurs["mdp"])) ? "<p class=\"erreur\">" . $erreurs["mdp"] . " </p>" : '' ?>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <p>1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
                </div>
                <div class="case-form">
                    <label for="verif">Vérification du mot de passe * :</label>
                    <input type="password" id="verif" name="verif" value="<?= (!(isset($erreurs["conf"]) || isset($erreurs["mdp"])) && isset($_POST["verif"])) ? $_POST["verif"] : ''?>" size="30" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["conf"])) ? "<p class=\"erreur\">" . $erreurs["conf"] . " </p>" : '' ?>
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
    <?php require_once("../../php/footer_back.php") ?>
</body>
</html>
