<?php
session_start();
include __DIR__ . '/../../01_premiere_connexion.php';
include(__DIR__ . '/../../php/modification_variable.php');
include(__DIR__ . '/../../php/verification_formulaire.php');

if (!isset($_SESSION["role"]) || $_SESSION["role"]!=="vendeur"){
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
    $latitude = '';
    $longitude = '';
    

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
    $latitude = $_POST["latitude"];
    $longitude = $_POST["longitude"];

    /* enregistrer toutes les erreurs */

    /* NOM */
    if (!verifNomPrenom($nom)) $erreurs["nom"] = "Le nom peut contenir seulement des lettres majuscules ou minuscules, des tirets, des espaces et les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô";

    /* PRENOM */
    if (!verifNomPrenom($prenom)) $erreurs["prenom"] = "Le prénom peut contenir seulement des lettres majuscules ou minuscules, des tirets, des espaces et les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô";

    /* MAIL */
    if (!verifMail($mail)) $erreurs["mail"] = "L'adresse email doit être du format : adresse@e.mail";
    if (!mailUnique($mail)) $erreurs["unique"] = "L'adresse email doit être du format : adresse@e.mail";

    /* TEL */
    if (!verifTelephone($tel)) $erreurs["tel"] = "Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres";

    /* DENOMINATION */
    if (!verifDenomination($denomination)) $erreurs["denomination"] = "La dénomination de l'entreprise peut contenir uniquement des lettres majuscules ou minuscules, des chiffres, des tirets et des tirets";

    /* RS */
    if (!verifDenomination($raisonSociale)) $erreurs["raisonSociale"] = "La raison sociale de l'entreprise peut contenir uniquement des lettres majuscules ou minuscules, des chiffres, des tirets et des tirets";

    /* IBAN */
    if (!verifIban($iban)) $erreurs["iban"] = "L'iban doit commencer par FR suivi de 25 chiffres";

    /* MDP */
    if (!verifMotDePasse($mdp)) $erreurs["mdp"] = "Doit inclure tous les éléments si dessous";
    if (!confirmationMotDePasse($verif, $mdp)) $erreurs["conf"] = "Le mot de passe est différent";

    /* SIREN */
    if (!verifSiren($siren)) $erreurs["siren"] = "Le numéro de SIREN doit contenir 9 chiffres";

    /* ##### ADRESSE ##### */
    if (!verifCp($cp)) $erreurs["cp"] = "Code postale doit contenir 5 chiffres";

    if (!verifVille($ville)) $erreurs["ville"] = "La ville peut contenir seulement des lettres majuscules ou minuscules, des tirets, des espaces et les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô";

    if (!verifAdresse($adresse)) $erreurs["adresse"] = "Votre adresse postal ne peut contenir que des chiffres, lettres majuscules ou minuscules, virgules et espaces.";
    
    $temp = tabAdresse($adresse);
    $numero = $temp[0];
    $compNum = $temp[1];
    $adresse = $temp[2];

    /* s'il n'y a pas d'erreur faire la requete */
    if (empty($erreurs)){
        try{
            $dbh->beginTransaction();

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
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._adresse (adresse_postale, complement_adresse, numero_rue, code_postal, ville, latitude, longitude) VALUES (?,?,?,?,?,?,?) RETURNING id_adresse");
            $stmt->execute([$adresse, $compNum, $numero, $cp, $ville, $latitude, $longitude]);
            $idAdresse = $stmt->fetchColumn();

            //inserer dans habite pour lier le compte a l'adresse
            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._habite (id_adresse,id_compte) VALUES (?,?)");
            $stmt->execute([$idAdresse, $idCompte]);
            $_SESSION["idCompte"] = $idCompte;
            $_SESSION["role"] = "vendeur";

            $dbh->commit();
                                            
            header("Location: index_vendeur.php");
            
        } catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . "/../../php/structure/head_front.php"?>
<head>
    <title>Créer un compte vendeur</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body>
    <?php 
    require_once __DIR__ . "/../../php/structure/header_front.php";
    require_once __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="flex flex-col items-center">
        <h2>Inscription Vendeur</h2>
        <form method="post" class="w-6/7" action="crea_compte_vendeur.php">
            <!-- ########## INFORMATIONS ########## -->
            <h3>Informations vendeur :</h3>
            <section class="flex flex-col md:flex-row md:flex-wrap md:justify-start md:w-3/4">
                <!-- à la validation du formulaire, s'il y a des erreurs, les informations valides resteront saisies -->
                <div class="flex flex-nowrap">
                    <!-- Nom -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2 ">
                        <label for="nom">Nom * :</label>
                        <input placeholder="Dupond" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60 md:w-96" type="text" id="nom" name="nom" value="<?= $nom ?>" size="25" required >
                        <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                        <?php echo isset($erreurs["nom"]) ? "<span class=\"text-rouge font-xs\">" . nl2br(wordwrap($erreurs["nom"], 56, "\n")) . " </span>" : '' ?>
                    </div>

                    <!-- Prénom -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2 md:ml-20">
                        <label for="prenom">Prénom * :</label>
                        <input placeholder="Jean" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60 md:w-96" type="text" id="prenom" name="prenom" value="<?= $prenom ?>" size="25" required>
                        <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                        <?php echo isset($erreurs["prenom"]) ? "<span class=\"text-rouge\">" . nl2br(wordwrap($erreurs["prenom"], 56, "\n")) . " </span>" : '' ?>
                    </div>
                </div>
                <div class="flex flex-nowrap">
                    <!-- Mail -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2">
                        <label for="mail">Mail * :</label>
                        <input placeholder="jean.dupond@mail.com" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-72 md:w-96" type="email" id="mail" name="mail" value="<?= $mail ?>" size="40" required>
                        <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                        <?php echo (isset($erreurs["mail"])) ? "<span class=\"text-rouge\">" . $erreurs["mail"] . " </span>" : '' ?>
                        <?php echo isset($erreurs["unique"]) ? "<span class=\"text-rouge\">" . $erreurs["unique"] . " </span>" : '' ?>
                    </div>

                    <!-- Téléphone -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2 md:ml-20">
                        <label for="tel">Numéro de téléphone * :</label>
                        <input placeholder="06 12 34 56 78" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-40 md:w-60" type="tel" id="tel" name="tel" value="<?= $tel ?>" size="16" required>
                        <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                        <?php echo isset($erreurs["tel"]) ? "<span class=\"text-rouge\">" . $erreurs["tel"] . " </span>" : '' ?>
                    </div>
                </div>
            </section>

            <h3>Informations entreprise :</h3>
            <section class="flex flex-col md:flex-row md:flex-wrap md:w-1/2">
                <!-- Raison sociale -->
                <div class="flex flex-col mt-2 mb-2 md:m-2">
                    <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                    <input placeholder="Nom de l'entreprise" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 md:w-5/6" type="text" id="raisonSociale" name="raisonSociale" value="<?= $raisonSociale ?>" size="40" required>
                    <?php echo isset($erreurs["raisonSociale"]) ? "<span class=\"text-rouge\">" . $erreurs["raisonSociale"] . " </span>" : '' ?>
                </div>

                <!-- Nom entreprise -->
                <div class="flex flex-col mt-2 mb-2 md:m-2 ">
                    <label for="denomination">Nom de l'entreprise * :</label>
                    <input placeholder="Nom de l'entreprise" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 md:w-5/6" type="text" id="denomination" name="denomination" value="<?= $denomination ?>" size="40" required>
                    <?php echo isset($erreurs["denomination"]) ? "<span class=\"text-rouge\">" . $erreurs["denomination"] . " </span>" : '' ?>
                </div>

                <!-- IBAN -->
                <div class="flex flex-col mt-2 mb-2 md:m-2">
                    <label for="iban">Numéro de IBAN * :</label>
                    <input placeholder="FR 76 12345 67890 12345678901 45" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 md:w-5/6" type="text" id="iban" name="iban" value="<?= $iban ?>" size="40" required>
                    <?php echo isset($erreurs["iban"]) ? "<span class=\"text-rouge\">" . $erreurs["iban"] . " </span>" : '' ?>
                </div>

                <!-- SIREN -->
                <div class="flex flex-col mt-2 mb-2 md:m-2">
                    <label for="siren">Numéro de SIREN * :</label>
                    <input placeholder="123 456 789" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60" type="text" id="siren" name="siren" value="<?= $siren ?>" size="11" required>
                    <?php echo isset($erreurs["siren"]) ? "<span class=\"text-rouge\">" . $erreurs["siren"] . " </span>" : '' ?>
                </div>
            </section>

            <!-- ########## ADRESSE ########## -->
            <h3>Siège social :</h3>
            <section class="flex flex-col md:flex-row">
                <div class="flex flex-col md:w-1/3">
                    <!-- Adresse -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2">
                        <label for="adresse">Adresse * :</label>
                        <input id="adresse" class="border-4 border-solid rounded-2xl border-beige md:w-90 p-1 pl-3 placeholder-gray-500 " type="text" id="adresse" name="adresse" value="<?= $adresse?>" size="60" placeholder="ex : 3 rue des camélias" required>
                        <?php echo isset($erreurs["adresse"]) ? "<span class=\"text-rouge\">" . $erreurs["adresse"] . " </span>" : '' ?>
                    </div>

                    <!-- Ville -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2">
                        <label for="ville">Ville * :</label>
                        <input placeholder="Lannion" id="ville" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60" type="text" id="ville" name="ville" value="<?= $ville?>" size="30" required>
                        <?php echo isset($erreurs["ville"]) ? "<p class=\"text-rouge\">" . $erreurs["ville"] . " </p>" : '' ?>
                    </div>

                    <!-- Code postal -->
                    <div class="flex flex-col mt-2 mb-2 md:m-2">
                        <label for="cp">Code Postal * :</label>
                        <input placeholder="22300" id="cP" class="border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-40" type="text" id="cp" name="cp" value="<?= $cp?>" size="10" required>
                        <?php echo isset($erreurs["cp"]) ? "<span class=\"text-rouge\">" . $erreurs["cp"] . " </span>" : '' ?>
                    </div>
                </div>
                <!-- Carte -->
                <article class="flex flex-col md:w-2/3">
                    <div id="map" class="h-60 md:h-80 z-0"></div>
                    <!-- Coordonnées -->
                    <div class="grid grid-cols-2 grid-rows-2 h-20 md:flex md:flex-row m-2">
                        <div class="m-2">
                            <label for="latitude">Latitude : </label>
                            <input placeholder="49.12" required type="text" name="latitude" id="latitude" class="border-2 border-solid rounded-2xl border-beige p-1 pl-3  w-30 md:w-60" value="<?= $latitude ?>">
                        </div>
                        <div class="m-2">
                            <label for="longitude">Longitude : </label>
                            <input placeholder="-0.12" required type="text" name="longitude" id="longitude" class="border-2 border-solid rounded-2xl border-beige p-1 pl-3  w-30 md:w-60" value="<?= $longitude ?>">
                        </div>
                    </div>
                    <p id="errorMap" class="text-rouge"></p>
                </article>
            </section>

            <h3>Mot de passe :</h3>
            <section class="flex flex-col md:flex-row md:flex-wrap md:w-3/4">
                <!-- MDP -->
                <div class="flex flex-col mt-2 mb-2 md:m-2">
                    <label for="mdp">Mot de passe * :</label>
                    <div class="zone-mdp flex flex-row">
                        <input class="champ-mdp border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60 md:w-96" type="password" id="mdp" name="mdp" value="<?= $mdp ?>" size="50" required>
                        <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                    </div>
                    <?php echo isset($erreurs["mdp"]) ? "<p class=\"text-rouge\">" . $erreurs["mdp"] . " </p>" : '' ?>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <p class="mt-2">1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
                </div>
                <div class="flex flex-col mt-2 mb-2 md:m-2">
                    <label for="verif">Vérification du mot de passe * :</label>
                    <div class="zone-mdp flex flex-row">
                        <input class="champ-mdp border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60 md:w-96" type="password" id="verif" name="verif" value="<?= $verif ?>" size="50" required>
                        <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                    </div>
                    <!-- s'il y a une erreur elle sera affiché sous la cellule -->
                    <?php echo isset($erreurs["conf"]) ? "<p class=\"text-rouge\">" . $erreurs["conf"] . " </p>" : '' ?>
                </div>
            </section>

            <!-- CGU -->
            <div class="md:flex md:flex-row md:flex-wrap md:w-3/4 m-4">
                <label for="cgu" class="underline! cursor-pointer hover:text-rouge">J'ai lu et j'acccepte les conditions générales d'utilisation</label>
                <input type="checkbox" id="cgu" name="cgu" required class="cursor-pointer ml-5 w-5 h-5">
            </div>

            <!-- Boutons formulaire -->
            <div class="flex flex-row m-4 justify-around">
                <a href="/index.php" class="flex justify-center cursor-pointer w-40 md:w-60 md:h-14 h-10 md:m-4 border-2 border-solid rounded-2xl border-vertClair p-1 pl-3">
                    <button type="button">Annuler</button>
                </a>
                <input type="submit" value="Valider" class="cursor-pointer w-40 md:w-60 md:h-14 h-10 md:m-4 border-2 border-solid rounded-2xl border-vertClair">
            </div>
        </form>

        <div class="flex flex-row flex-wrap">
            <!-- Lien vers la page de connexion -->
            <a class="flex flex-row flex-wrap justify-center mb-4 ml-4 mr-4 hover:text-rouge" href="../fo/connexion.php">Vous avez déjà un compte ?<span class="underline">Connectez vous</span></a> 
            
            <!-- Lien vers la création d'un compte vendeur -->
            <a class="flex flex-row flex-wrap justify-center mb-4 ml-4 mr-4 hover:text-rouge" href="../fo/creation_compte_client.php">Vous êtes un client ?<span class="underline">Créer un compte client</span></a> 
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php" ?>
</body>
<script src="../../js/bo/visibilite_mdp.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([48, -3], 7);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var pointer = L.icon({
        iconUrl: '../../images/logo/pointeurVertFonce.png',
        iconSize: [45, 70],
    });
</script>
<script src="../../js/fo/geolocalVendeur.js"></script>
</html>
<?php
} else {
    header("Location: index_vendeur.php");
}

?>