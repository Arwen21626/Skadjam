<?php
session_start();
include __DIR__ . '/../../01_premiere_connexion.php';
include(__DIR__ . '/../../php/modification_variable.php');
include(__DIR__ . '/../../php/verification_formulaire.php');

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
    $numero = '';
    $compNum = '';
    $siren = '';
    $mdp = '';
    $verif = '';
    

    $erreurs = [];
if (isset($_POST["nom"])){
  
    //récuperer les attributs du post
    $nom = $_POST["nom"]; 
    $prenom = formatPrenom($_POST["prenom"]);   
    $mail = $_POST["mail"];                     
    $tel = $_POST["tel"];                       
    $denomination = $_POST["denomination"];     
    $raisonSociale = $_POST["raisonSociale"];   
    $iban = $_POST["iban"];                     
    $adresse = $_POST["adresse"];
    $ville = $_POST["ville"];
    $cp = $_POST["cp"];
    $siren = $_POST["siren"];                   
    $mdp = $_POST["mdp"];                       
    $verif = $_POST["verif"];                   

    /* enregistrer toutes les erreurs */

    /* NOM */
    if (!verifNomPrenom($nom)) $erreurs["nom"] = "lettre majuscule ou minuscule seulement";

    /* PRENOM */
    if (!verifNomPrenom($prenom)) $erreurs["prenom"] = "lettre majuscule ou minuscule seulement";

    /* MAIL */
    if (!verifMail($mail)) $erreurs["mail"] = "format incorrecte";
    if (!mailUnique($mail)) $erreurs["unique"] = "un utilisateur avec cette e-mail existe deja : $mail";

    /* TEL */
    if (!verifTelephone($tel)) $erreurs["tel"] = "numéro à 10 chiffres";

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
    if (!verifCp($cp)) $erreurs["cp"] = "code postale invalide";

    if (!verifVille($ville)) $erreurs["ville"] = "format ville incorrect";

    if (!verifAdresse($adresse)) $erreurs["adresse"] = "format de l'adresse invalide";
    
    $temp = tabAdresse($adresse);
    $numero = $temp[0];
    $compNum = $temp[1];
    $adresse = $temp[2];

    /* s'il n'y a pas d'erreur faire la requete */
    if (empty($erreurs)){
        try{
            $idCompte = null;
            //preparer la requete sql pour inserer dans le compte
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._compte (nom_compte, prenom_compte, adresse_mail, mot_de_passe, numero_telephone, bloque) VALUES (?,?,?,?,?, false) RETURNING id_compte");
            //excuter la requete sql avec les attributs
            $stmt->execute([$nom, $prenom,$mail, password_hash($mdp, PASSWORD_DEFAULT),formatTel($tel) ]);
            
            //recuperer l'id du compte associé au vendeur
            $idCompte = $stmt->fetchColumn();

            //preparer la requete sql pour inserer dans vendeur
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._vendeur (id_compte, raison_sociale, siren, iban, denomination) VALUES (?,?,?,?,?)");
            $stmt->execute([$idCompte,$raisonSociale, (int)$siren, $iban, $denomination]);

            //preparer la requete pour inserer dans adresse et recuperer l'id
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._adresse (adresse_postale, complement_adresse, numero_rue, code_postal, ville) VALUES (?,?,?,?,?) RETURNING id_adresse");
            $stmt->execute([$adresse, $compNum, $numero, $cp, $ville]);
            $idAdresse = $stmt->fetchColumn();

            //inserer dans habite pour lier le compte a l'adresse
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._habite (id_adresse,id_compte) VALUES (?,?)");
            $stmt->execute([$idAdresse, $idCompte]);
            $_SESSION["idCompte"] = $idCompte;
                                            
            header("Location: index_vendeur.php");
            
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    
    
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . "/../../php/structure/head_back.php"?>
<head>
    <title>Créer un compte vendeur</title>
</head>
<body class=" @container">
    <?php 
    require_once __DIR__ . "/../../php/structure/header_back.php";
    require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>
    <main class=" flex flex-col items-center">
        <h2 class="m-8">Inscription Vendeur</h2>
        <form method="POST" class=" w-2/3 @max-[768px]:w-7/8">

            <h3 class="underline">Informations vendeur :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10 @max-[768px]:ml-5 @max-[768px]:mr-5">
                <!-- à la validation du formulaire, s'il y a des erreurs, les informations valides resteront saisies -->
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="nom">Nom * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2" type="text" id="nom" name="nom" value="<?= (!isset($erreurs["nom"])) ? $nom : '' ?>" size="25" required >
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["nom"])) ? "<p class=\"text-rouge\">" . $erreurs["nom"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="prenom">Prénom * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="prenom" name="prenom" value="<?= (!isset($erreurs["prenom"])) ? $prenom : '' ?>" size="25" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["prenom"])) ? "<p class=\"text-rouge\">" . $erreurs["prenom"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="mail">Mail * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="email" id="mail" name="mail" <?= (!(isset($erreurs["mail"]) || isset($erreurs["unique"]))) ? "value=\"$mail\"" : ''  ?> size="40" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["mail"])) ? "<p class=\"text-rouge\">" . $erreurs["mail"] . " </p>" : '' ?>
                    <?php echo (isset($erreurs["unique"])) ? "<p class=\"text-rouge\">" . $erreurs["unique"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="tel">Numéro de téléphone * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="tel" id="tel" name="tel" value="<?= (!isset($erreurs["tel"]))?$tel:''?>" size="16" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["tel"])) ? "<p class=\"text-rouge\">" . $erreurs["tel"] . " </p>" : '' ?>
                </div>
            </div>

            <h3 class="underline">Informations entreprise :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="raisonSociale" name="raisonSociale" value="<?= (!isset($erreurs["raisonSociale"]))? $raisonSociale: ''?>" size="40" required>
                    <?php echo (isset($erreurs["raisonSociale"])) ? "<p class=\"text-rouge\">" . $erreurs["raisonSociale"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="denomination">Nom de l'entreprise * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="denomination" name="denomination" value="<?= (!isset($erreurs["denomination"]))? $denomination: ''?>" size="40" required>
                    <?php echo (isset($erreurs["denomination"])) ? "<p class=\"text-rouge\">" . $erreurs["denomination"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="siren">Numéro de SIREN * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="siren" name="siren" value="<?= (!isset($erreurs["siren"]))?$siren: ''?>" size="11" required>
                    <?php echo (isset($erreurs["siren"])) ? "<p class=\"text-rouge\">" . $erreurs["siren"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="iban">Numéro de IBAN * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="iban" name="iban" value="<?= (!isset($erreurs["iban"]))?$iban: 'FR'?>" placeholder="FR" size="30" required>
                    <?php echo (isset($erreurs["iban"])) ? "<p class=\"text-rouge\">" . $erreurs["iban"] . " </p>" : '' ?>
                </div>
            </div>

            <!-- ########## ADRESSE ########## -->
            <h3 class="underline">Siège social :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="adresse">Adresse * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="adresse" name="adresse" value="<?= $_POST["adresse"] ?? ''?>" size="40" placeholder="ex : 3 rue des camélias" required>
                    <?php echo (isset($erreurs["adresse"])) ? "<p class=\"text-rouge\">" . $erreurs["adresse"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="ville">Ville * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="ville" name="ville" value="<?= $_POST["ville"] ?? ''?>" size="30" required>
                    <?php echo (isset($erreurs["ville"])) ? "<p class=\"text-rouge\">" . $erreurs["ville"] . " </p>" : '' ?>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="cp">Code Postal * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="cp" name="cp" value="<?= $_POST["cp"] ?? ''?>" size="10" required>
                    <?php echo (isset($erreurs["cp"])) ? "<p class=\"text-rouge\">" . $erreurs["cp"] . " </p>" : '' ?>
                </div>
                
            </div>

            <h3 class="underline">Mot de passe :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="mdp">Mot de passe * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="password" id="mdp" name="mdp" value="<?= (!isset($erreurs["mdp"]) && isset($_POST["mdp"])) ? $_POST["mdp"] : ''?>" size="30" required>
                    <?php echo (isset($erreurs["mdp"])) ? "<p class=\"text-rouge\">" . $erreurs["mdp"] . " </p>" : '' ?>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <p>1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="verif">Vérification du mot de passe * :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-beige pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="password" id="verif" name="verif" value="<?= (!(isset($erreurs["conf"]) || isset($erreurs["mdp"])) && isset($_POST["verif"])) ? $_POST["verif"] : ''?>" size="30" required>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo (isset($erreurs["conf"])) ? "<p class=\"text-rouge\">" . $erreurs["conf"] . " </p>" : '' ?>
                </div>
            </div>

            <div class="flex flex-row flex-wrap items-center mt-2 mb-2">
                <label for="cgu" class="underline! cursor-pointer">J'ai lu et j'acccepte les conditions générales d'utilisation :</label>
                <input type="checkbox" id="cgu" name="cgu" required class="ml-10 w-5 h-5 @max-[768px]:ml-2 mt-3">
            </div>
            <div class="flex flex-row justify-around mt-8 mb-8 @max-[768px]:flex-col @max-[768px]:items-center">
                <input type="reset" value="Annuler" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-beige pl-3">
                <input type="submit" value="Valider" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-beige pl-3 @max-[768px]:mt-2">
            </div>
        </form>
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Vous avez déjà un compte ? </p>
            <a href="./connexion_compte_vendeur.php" class="underline!" >Connectez vous</a>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
</html>
