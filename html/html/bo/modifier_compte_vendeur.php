<?php
session_start();
include __DIR__ . "/../../php/verif_role_bo.php";
include __DIR__ . "/../../01_premiere_connexion.php";
require_once __DIR__."/../../php/verification_formulaire.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__."/../../php/modification_variable.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__."/../../../connections_params.php"; // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// définition variables d'erreurs
$erreur = false;
$erreurNom = false;
$erreurPrenom = false;
$erreurMail = false;
$erreurTel = false;
$erreurDenomination = false;
$erreurRaisonSociale = false;
$erreurIban = false;
$erreurSiren = false;
$erreurAdresse = false;
$erreurCp = false;
$erreurVille = false;
$erreurDescription = false;


$idCompte = $_SESSION["idCompte"];

$isset = false;
// Traitement du formulaire seulement si toutes les données sont saisie
if (isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["mail"]) && isset($_POST["tel"]) && isset($_POST["denomination"]) && isset($_POST["raisonSociale"]) && isset($_POST["iban"]) && isset($_POST["adresse"]) && isset($_POST["ville"]) && isset($_POST["cp"]) && isset($_POST["siren"])){
    $isset = true;
    // Récupération des données du formulaire
    try{
        // S'il y a eu une erreur lors de l'execution
        $erreur = false;

        // Récupération de l'ancien email
        foreach($dbh->query("SELECT c.adresse_mail 
                                FROM sae3_skadjam._compte c 
                                WHERE id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
            $ancienMail = $ligne['adresse_mail'];
        }

        //récuperer les attributs du post
        $nom = htmlentities(formatPrenom($_POST["nom"]));
        $prenom = htmlentities(formatPrenom($_POST["prenom"]));
        $mail = htmlentities($_POST["mail"]);
        $tel = htmlentities($_POST["tel"]);
        $denomination = htmlentities($_POST["denomination"]);
        $raisonSociale = htmlentities($_POST["raisonSociale"]);
        $iban = htmlentities($_POST["iban"]);
        $ville = htmlentities($_POST["ville"]);
        $cp = htmlentities($_POST["cp"]);
        $siren = htmlentities($_POST["siren"]);
        $description = isset($_POST["description"]) ? $_POST["description"] : "";
        $adresse = htmlentities($_POST["adresse"]);
        $temp = tabAdresse($adresse);
        $num = $temp[0];
        $numBis = $temp[1];
        $adresse = $temp[2];

        // Vérification que toutes les données commune à la création et à la modification d'un compte client sont correcte
        if (strlen($_POST['description']) <= 500 && verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['tel']) && verifDenomination($_POST['denomination']) && verifDenomination($_POST['raisonSociale']) && verifIban($_POST['iban']) && verifSiren($_POST['siren']) && verifCp($_POST['cp']) && verifVille($_POST['ville']) && verifAdresse($_POST['adresse'])){
            // Vérification de l'email et de l'adresse
            if(mailUnique($mail) || $ancienMail === $mail){
                // Modification du compte
                $tel = formatTel($tel);
                $modifCompte = $dbh->prepare("UPDATE sae3_skadjam._compte
                                            SET nom_compte = '$nom', prenom_compte = '$prenom', adresse_mail = '$mail', numero_telephone = '$tel'
                                            WHERE id_compte = $idCompte");

                $modifVendeur = $dbh->prepare("UPDATE sae3_skadjam._vendeur
                                            SET denomination = '$denomination', raison_sociale = '$raisonSociale', iban = '$iban', siren = '$siren', description_vendeur = '$description'
                                            WHERE id_compte = $idCompte");
                $modifCompte->execute();
                $modifVendeur->execute();

                // Modification des adresses
                foreach($dbh->query("SELECT h.id_adresse
                                        FROM sae3_skadjam._habite h
                                        WHERE id_compte = $idCompte
                                        ORDER BY id_adresse ASC", PDO::FETCH_ASSOC) as $ligne){
                    if(isset($_POST['adresse']) && isset($_POST['ville']) && isset($_POST['cp'])){

                        $adresse = htmlentities($_POST['adresse']);
                        $numRue = htmlentities(tabAdresse($_POST['adresse'])[0]);
                        $nomRue = htmlentities(tabAdresse($_POST['adresse'])[2]);
                        $complement = htmlentities(tabAdresse($_POST['adresse'])[1]);
                        $codePostal = htmlentities($_POST['cp']);
                        $ville = htmlentities($_POST['ville']);

                        if (verifAdresse($adresse) && verifVille($ville) && verifCp($codePostal)){
                            $idAdresse = $ligne['id_adresse'];
                            $modifAdresse = $dbh->prepare("UPDATE sae3_skadjam._adresse
                                                            SET numero_rue = $numRue, code_postal = $codePostal, complement_adresse = '$complement', ville = '$ville', adresse_postale = '$nomRue'
                                                            WHERE id_adresse = $idAdresse");
                            $modifAdresse->execute();
                        // Erreurs concernant le format de l'adresse
                        }else if(!verifAdresse($adresse)){
                            $erreur = true;
                            $erreurAdresse = true;
                        }else if(!verifVille($ville)){
                            $erreur = true;
                            $erreurVille = true;
                        }else if(!verifCp($codePostal)){
                            $erreur = true;
                            $erreurCp = true;
                        }
                    // erreur si l'un des champs obligatoire des adresses n'est pas rempli
                    }else{
                        $erreur = true;
                        $erreurAdresse = true;
                        $erreurVille = true;
                        $erreurCp = true;
                    }
                }
            }

            $urlPhoto = '/images/logo/bootstrap_icon/image.svg';

            if(isset($_FILES['photo'])){
                // Récupération de la photo existante (si elle existe)
                $reqPhoto = $dbh->prepare("SELECT ph.id_photo, ph.url_photo
                                            FROM sae3_skadjam._presente pr
                                            INNER JOIN sae3_skadjam._photo ph
                                                ON pr.id_photo = ph.id_photo
                                            WHERE pr.id_vendeur = $idCompte");
                $reqPhoto->execute();

                $photoExistante = $reqPhoto->fetch();
            }

            // Traitement upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
                if (!in_array($_FILES['photo']['type'], $typesAutorises)) {
                    die("Format d'image non autorisé");
                }

                // Génération nom fichier
                $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
                $nomPhoto = explode(' ', $nom)[0] . '_' . time() . '.' . $ext;
                $urlPhoto = '/images/images_vendeur/' . $nomPhoto;

                // Déplacement fichier
                move_uploaded_file($_FILES['photo']['tmp_name'], '../../' . $urlPhoto);

                // Aucune photo existante
                if(!$photoExistante){
                    // Insertion photo
                    $insertPhoto = $dbh->prepare("INSERT INTO sae3_skadjam._photo(url_photo,alt,titre)
                                                VALUES(:urlPhoto,:denomination,:nomPhoto)");
                    $insertPhoto->execute([
                        'urlPhoto'   => $urlPhoto,
                        'denomination' => $denomination,
                        'nomPhoto'   => $nomPhoto
                    ]);

                    $idPhoto = $dbh->lastInsertId();

                    // Liaison vendeur <-> photo
                    $insertPresente = $dbh->prepare("INSERT INTO sae3_skadjam._presente(id_vendeur,id_photo)
                                                    VALUES($idCompte,$idPhoto)");
                    $insertPresente->execute();

                // Photo existante
                }else{
                    // Suppression ancienne photo (si pas image par défaut)
                    if ($photoExistante['url_photo'] !== 'image.svg') {
                        $anciennePath = __DIR__ . '/../../images/images_vendeur/' . $photoExistante['url_photo'];
                        if (file_exists($anciennePath)) {
                            unlink($anciennePath);
                        }
                    }

                    // Mise à jour photo
                    $updatePhoto = $dbh->prepare("UPDATE sae3_skadjam._photo
                                                SET url_photo = :url, alt = :alt, titre = :titre
                                                WHERE id_photo = :idPhoto");
                    $updatePhoto->execute([
                        'url'     => $urlPhoto,
                        'alt'     => $denomination,
                        'titre'   => $nomPhoto,
                        'idPhoto' => $photoExistante['id_photo']
                    ]);
                }
            }

            if(!$erreur){
                // Fermer la connexion à la base de données
                $dbh = null;
                // Redirection vers la page d'accueil
                header("location: ./profil_vendeur.php");
                exit();
            }
        // Messages d'erreurs si l'un des champs est mal rempli
        }else{ 
            if(!verifNomPrenom($_POST['nom'])){
                $erreur = true;
                $erreurNom = true;
            }
            if(!verifNomPrenom($_POST['prenom'])){
                $erreur = true;
                $erreurPrenom = true;
            }
            if(!verifMail($_POST['mail'])){
                $erreur = true;
                $erreurMail = true;
            }
            if(!mailUnique($_POST['mail']) && $_POST['mail'] !== $ancienMail){
                $erreur = true;
                $erreurMail = true;
            }
            if(!verifTelephone($_POST['tel'])){
                $erreur = true;
                $erreurTel = true;
            }
            if(!verifAdresse($_POST['adresse'])){
                $erreur = true;
                $erreurAdresse = true;
            }
            if(!verifDenomination($_POST['denomination'])){
                $erreur = true;
                $erreurDenomination = true;
            }
            if(!verifDenomination($_POST['raisonSociale'])){
                $erreur = true;
                $erreurRaisonSociale = true;
            }
            if(!verifIban($_POST['iban'])){
                $erreur = true;
                $erreurIban = true;
            }
            if(!verifSiren($_POST['siren'])){
                $erreur = true;
                $erreurSiren = true;
            }
            if(!verifCp($_POST['cp'])){
                $erreur = true;
                $erreurCp = true;
            }
            if(!verifVille($_POST['ville'])){
                $erreur = true;
                $erreurVille = true;
            }
            if(strlen($_POST['description']) > 500){
                $erreur = true;
                $erreurDescription = true;
            }
        }
    }catch(PDOException $e){
        echo "Erreur dans l'envoie des données dans la base de données.";
        die();
    }
}
if(!$isset || $erreur){
    // Préparation des données qui vont remplir les champs du formulaire
    // Récupération des infos du compte vendeur
    if(!$isset){
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON c.id_compte = v.id_compte
                                    INNER JOIN sae3_skadjam._habite h
                                        ON h.id_compte = c.id_compte
                                    INNER JOIN sae3_skadjam._adresse a
                                        ON a.id_adresse = h.id_adresse
                                    WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
            $nom = $ligne['nom_compte'];
            $prenom = $ligne['prenom_compte'];
            $mail = $ligne['adresse_mail'];
            $tel = formatTel($ligne['numero_telephone']);
            $denomination = $ligne['denomination'];
            $raisonSociale = $ligne['raison_sociale'];
            $siren = $ligne['siren'];
            $iban = $ligne['iban'];
            $cp = $ligne['code_postal'];
            $ville = $ligne['ville'];
            $description = $ligne['description_vendeur'];
            $num = $ligne['numero_rue'];
            $latitude = $ligne['latitude'];
            $longitude = $ligne['longitude'];
            $numBis = $ligne['complement_adresse'];
            $adresse = $ligne['adresse_postale'];
        }
    }
    ?>
<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . "/../../php/structure/head_back.php";?>
    <head>
        <title>Modification du compte vendeur</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    </head>
    <body>
        <?php include __DIR__."/../../php/structure/header_back.php"; ?>
        <main style="margin: 0" class="flex flex-col justify-center">
            <?php include __DIR__."/../../php/structure/navbar_back.php"; ?>

            <h2 class="flex justify-center text-center">Modification du compte vendeur</h2>
            <!-- Formulaire -->
            <form class="flex flex-col self-center w-9/10 p-15 pt-0" action="modifier_compte_vendeur.php" method="post" enctype="multipart/form-data">
                <!-- Photo de profil -->
                <div class="flex flex-row justify-around">
                    <div class="grid grid-rows-1 w-70 m-2 p-4 justify-items-center">
                        <input type="file" id="photo" name="photo" class="hidden">
                        <!-- label qui agit comme bouton -->
                        <label id="labelImage" for="photo" class="bg-beige w-60 h-60 rounded-2xl image-produit cursor-pointer" style="background-image: url('../..<?= isset($url) ? $url : '/images/logo/bootstrap_icon/image.svg'; ?>'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                        <label class="cursor-pointer" for="photo"><h4><strong>Photo de profil</strong></h4></label>
                    </div>

                    <!-- Vendeur -->
                    <div class="flex flex-col flex-wrap">
                        <h3>Informations vendeur :</h3>
                        <div class="flex flex-row space-x-25 space-y-6">
                            <div id="nomForm" class="flex flex-col md:w-110 space-y-2">
                                <label for="nom">Nom * :</label>
                                <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="Dupond" type="text" id="nom" name="nom" value="<?= $nom; ?>" size="30" required >
                                <?= $erreurNom ? "<p id=\"erreurNomPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents.</p>" : ""; ?>
                            </div>
                            <div id="mailForm" class="flex flex-col md:w-120 space-y-2">
                                <label for="mail">Mail * :</label>
                                <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="jean.dupond@mail.com" type="email" id="mail" name="mail" value="<?= $mail; ?>" size="30" required>
                                <?= $erreurMail ? "<p id=\"erreurMailPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">L'adresse email doit être au format : adresse@e.mail</p>" : ""; ?>
                            </div>
                        </div>
                        <div class="flex flex-row space-x-25 space-y-6">
                            <div id="prenomForm" class="flex flex-col md:w-110 space-y-2">
                                <label for="prenom">Prénom * :</label>
                                <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="Jean" type="text" id="prenom" name="prenom" value="<?= $prenom; ?>" size="30" required>
                                <?= $erreurPrenom ? "<p id=\"erreurPrenomPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents.</p>" : ""; ?>
                            </div>
                            <div id="telephoneForm" class="flex flex-col md:w-70 space-y-2">
                                <label for="tel">Numéro de téléphone * :</label>
                                <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="06 12 34 56 78" type="tel" id="tel" name="tel" value="<?= $tel; ?>" size="10" required>
                                <?= $erreurTel ? "<p id=\"erreurTelPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres.</p>" : ""; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Entreprise -->
                <h3>Informations entreprise :</h3>
                <div class="flex flex-col">
                    <div class="flex flex-row space-x-40 space-y-6">
                        <div id="raisonSocialeForm" class="flex flex-col md:w-150 space-y-2">
                            <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="Nom de l'entreprise" type="text" id="raisonSociale" name="raisonSociale" value="<?= $raisonSociale; ?>" size="30" required>
                            <?= $erreurRaisonSociale ? "<p id=\"erreurRaisonSocialePHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">La raison sociale ne peut contenir que des majuscules, des minuscules, des chiffres, des tirets, des espaces ou des accents.</p>" : ""; ?>
                        </div>
                        <div id="denominationForm" class="flex flex-col md:w-150 space-y-2">
                            <label for="denomination">Nom de l'entreprise * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="Nom de l'entreprise" type="text" id="denomination" name="denomination" value="<?= $denomination; ?>" size="30" required>
                            <?= $erreurDenomination ? "<p id=\"erreurRaisonSocialePHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">La dénomination ne peut contenir que des majuscules, des minuscules, des chiffres, des tirets, des espaces ou des accents.</p>" : ""; ?>
                        </div>
                    </div>
                    <div  class="flex flex-row space-x-40 space-y-6">
                        <div id="ibanForm" class="flex flex-col md:w-150 space-y-2">
                            <label for="iban">Numéro de IBAN * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" type="text" id="iban" name="iban" value="<?= $iban; ?>" placeholder="FR 76 12345 67890 12345678901 45" size="30" required>
                            <?= $erreurIban ? "<p id=\"erreuIbanPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le numéro de IBAN doit commencer par FR suivi de 25 chiffres sans espace.</p>" : ""; ?>
                        </div>
                        <div id="sirenForm" class="flex flex-col md:w-150 space-y-2">
                            <label for="siren">Numéro de SIREN * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 placeholder-gray-500" placeholder="123 456 789" type="text" id="siren" name="siren" value="<?= $siren; ?>" size="10" required>
                            <?= $erreurSiren ? "<p id=\"erreurSirenPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le numéro de SIREN doit contenir exactement 9 chiffres.</p>" : ""; ?>
                        </div>
                    </div>                    
                </div>

                <!-- Adresse -->
                <h3>Siège social :</h3>
                <div class="flex flex-row space-x-20">
                    <!-- Champs de l'adresse -->
                    <div class="flex flex-col w-1/3">
                        <!-- Adresse postale -->
                        <div id="adresseForm" class="flex flex-col space-y-2 ">
                            <label for="adresse">Adresse * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-90 placeholder-gray-500" type="text" id="adresse" name="adresse" value="<?= $num . (!empty($numBis) ? " $numBis" : " ") . $adresse; ?>" size="50" placeholder="1 rue des Fleurs" required>
                            <?= $erreurAdresse ? "<p id=\"erreurAdressePHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">L'adresse postal doit être du même format que : 1 rue des fleurs</p>" : ""; ?>
                        </div>

                        <!-- Ville -->
                        <div id="villeForm" class="flex flex-col space-y-2">
                            <label for="ville">Ville * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-60 placeholder-gray-500" placeholder="Lannion" type="text" id="ville" name="ville" value="<?= $ville; ?>" size="50" required>
                            <?= $erreurVille ? "<p id=\"erreurVillePHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">La ville ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô.</p>" : ""; ?>
                        </div>

                        <!-- Code postal -->
                        <div id="codePostalForm" class="flex flex-col space-y-2">
                            <label for="cp">Code Postal * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-40 placeholder-gray-500" placeholder="22300" type="text" id="cP" name="cp" value="<?= $cp; ?>" size="10" required>
                            <?= $erreurCp ? "<p id=\"erreurCodePostalPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">Le code postal doit être composé de 5 chiffres.</p>" : ""; ?>
                        </div>
                    </div>
                    <!-- Carte -->
                     <div class="flex flex-col w-2/3">
                        <div id="map" class="h-80 z-0"></div>
                        <!-- Coordonnées -->
                        <div class="h-20 flex flex-row m-2">
                            <div class="m-2">
                                <label for="latitude">Latitude : </label>
                                <input required type="text" name="latitude" id="latitude" class="border-2 border-solid rounded-2xl border-beige p-1 pl-3 w-60 placeholder-gray-500" placeholder="49.12" value="<?= $latitude; ?>">
                            </div>
                            <div class="m-2">
                                <label for="longitude">Longitude : </label>
                                <input required type="text" name="longitude" id="longitude" class="border-2 border-solid rounded-2xl border-beige p-1 pl-3 w-60 placeholder-gray-500" placeholder="-0.12" value="<?= $longitude; ?>">
                            </div>
                        </div>
                        <p id="errorMap" class="text-rouge"></p>
                    </div>
                </div>

                <!-- Description -->
                <h3>Description :</h3>
                <div id="descriptionForm" class="flex flex-col justify-around">
                    <textarea class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4" id="description" name="description" rows="5"><?= isset($description) ? $description : ''; ?></textarea>
                    <?= $erreurDescription ? "<p id=\"erreurDescriptionPHP\" style=\"font-size: 0.90em\" class=\"text-rouge\">La description ne peut pas dépasser 500 caractères.</p>" : ""; ?>
                </div>

                <!-- Valider le formulaire -->
                <div class="flex mt-10 justify-center md:justify-end">
                    <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10" type="button"><a href="./profil_vendeur.php">Annuler</a></button>
                    <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40  h-14 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="Valider">
                </div>
            </form>

                <script src="../../js/verifForm.js"></script>
            <script>
                // initialisation
                let nom = document.getElementById("nom")
                let prenom = document.getElementById("prenom")
                let mail = document.getElementById("mail")
                let telephone = document.getElementById("tel")
                let raisonSociale = document.getElementById('raisonSociale')
                let denomination = document.getElementById('denomination')
                let iban = document.getElementById('iban')
                let siren = document.getElementById('siren')
                let adresseF = document.getElementById("adresse")
                let villeF = document.getElementById("ville")
                let codePostal = document.getElementById("cP")
                let mdp = document.getElementById("mdp")
                let verifMdp = document.getElementById("verif")
                let description = document.getElementById("description")

                // Verif nom
                let nomForm = document.getElementById("nomForm")
                let erreurNom = document.createElement("p")
                erreurNom.textContent = "Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
                erreurNom.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                nom.addEventListener("change", function(){
                    if(!verifNomPrenom(nom.value)){                    
                        erreurNom.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurNom.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    nomForm.appendChild(erreurNom)
                })

                // Verif prenom
                let prenomForm = document.getElementById("prenomForm")
                let erreurPrenom = document.createElement("p")
                erreurPrenom.textContent = "Le prenom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
                erreurPrenom.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                prenom.addEventListener("change", function(){
                    if(!verifNomPrenom(prenom.value)){                    
                        erreurPrenom.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurPrenom.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    prenomForm.appendChild(erreurPrenom)
                })

                // Verif mail
                let mailForm = document.getElementById("mailForm")
                let erreurMail = document.createElement("p")
                erreurMail.textContent = "L'adresse doit être au format : adresse@e.mail"
                erreurMail.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                mail.addEventListener("change", function(){
                    if(!verifMail(mail.value)){                    
                        erreurMail.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurMail.classList.add("md:hidden", "hidden")
                    }
                    let errPHP1 = document.getElementById("erreurMailPHP1")
                    if(errPHP1) errPHP1.classList.add("md:hidden", "hidden")
                    let errPHP2 = document.getElementById("erreurMailPHP2")
                    if(errPHP2) errPHP2.classList.add("md:hidden", "hidden")
                    mailForm.appendChild(erreurMail)
                })

                // Verif tel
                let telForm = document.getElementById("telephoneForm")
                let erreurTel = document.createElement("p")
                erreurTel.textContent = "Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres."
                erreurTel.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                telephone.addEventListener("change", function(){
                    if(!verifTelephone(telephone.value)){
                        erreurTel.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurTel.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurTelPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    telForm.appendChild(erreurTel)
                })

                // Verif raison sociale
                let raisonSocialeForm = document.getElementById("raisonSocialeForm")
                let erreurRaisonSociale = document.createElement("p")
                erreurRaisonSociale.textContent = "La raison sociale de l'entreprise peut contenir uniquement des lettres majuscules ou minuscules, des chiffres, des tirets et des accents."
                erreurRaisonSociale.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                raisonSociale.addEventListener("change", function(){
                    if(!verifDenomination(raisonSociale.value)){
                        erreurRaisonSociale.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurRaisonSociale.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurRaisonSocialePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    raisonSocialeForm.appendChild(erreurRaisonSociale)
                })

                // Verif denomination de l'entreprise
                let denominationForm = document.getElementById("denominationForm")
                let erreurDenomination = document.createElement("p")
                erreurDenomination.textContent = "La dénomination de l'entreprise peut contenir uniquement des lettres majuscules ou minuscules, des chiffres, des tirets et des accents."
                erreurDenomination.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                denomination.addEventListener("change", function(){
                    if(!verifDenomination(denomination.value)){
                        erreurDenomination.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurDenomination.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurDenominationPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    denominationForm.appendChild(erreurDenomination)
                })

                // Verif iban
                let ibanForm = document.getElementById("ibanForm")
                let erreurIban = document.createElement("p")
                erreurIban.textContent = "L'iban doit commencer par FR suivi de 25 chiffres."
                erreurIban.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                iban.addEventListener("change", function(){
                    if(!verifIban(iban.value)){
                        erreurIban.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurIban.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurIbanPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    ibanForm.appendChild(erreurIban)
                })

                // Verif siren
                let sirenForm = document.getElementById("sirenForm")
                let erreurSiren = document.createElement("p")
                erreurSiren.textContent = "Le numéro de SIREN doit contenir 9 chiffres et passer la formule de Lunh"
                erreurSiren.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                siren.addEventListener("change", function(){
                    if(!verifSiren(siren.value)){
                        erreurSiren.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurSiren.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurSirenPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    sirenForm.appendChild(erreurSiren)
                })

                // Verif adresse
                let adresseForm = document.getElementById("adresseForm")
                let erreurAdresse = document.createElement("p")
                erreurAdresse.textContent = "L'adresse postal doit être du même format que : 1 rue des fleurs"
                erreurAdresse.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                adresseF.addEventListener("change", function(){
                    if(!verifAdresse(adresseF.value)){                    
                        erreurAdresse.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurAdresse.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurAdressePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    adresseForm.appendChild(erreurAdresse)
                })

                // Verif ville
                let villeForm = document.getElementById("villeForm")
                let erreurVille = document.createElement("p")
                erreurVille.textContent = "La ville peut contenir seulement des lettres majuscules ou minuscules, des tirets, des espaces et des accents."
                erreurVille.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                villeF.addEventListener("change", function(){
                    if(!verifVille(villeF.value)){                    
                        erreurVille.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurVille.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurVillePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    villeForm.appendChild(erreurVille)
                })

                // Verif code postal
                let codePostalForm = document.getElementById("codePostalForm")
                let erreurCodePostal = document.createElement("p")
                erreurCodePostal.textContent = "Le code postale doit contenir 5 chiffres."
                erreurCodePostal.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                codePostal.addEventListener("change", function(){
                    if(!verifCodePostal(codePostal.value)){                    
                        erreurCodePostal.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurCodePostal.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    codePostalForm.appendChild(erreurCodePostal)
                })

                // Verif description
                let descriptionForm = document.getElementById("descriptionForm")
                let erreurDescription = document.createElement("p")
                erreurDescription.textContent = "La description ne peut pas dépasser 500 caractères."
                erreurDescription.classList.add("md:text-rouge", "text-rouge", "md:ml-5")

                description.addEventListener("change", function(){
                    if(!(description.value.length < 500)){                    
                        erreurDescription.classList.remove("md:hidden", "hidden")
                    }else{
                        erreurDescription.classList.add("md:hidden", "hidden")
                    }
                    let errPHP = document.getElementById("erreurDescriptionPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    descriptionForm.appendChild(erreurDescription)
                })
            </script>
        </main>

        <?php 
        // Import du footer
        include __DIR__ . "/../../php/structure/footer_back.php";

        // Fermer la connexion à la base de données
        $dbh = null;
        ?>
        <script src="../../js/bo/changement_image_produits.js"></script>
    </body>
    <?php $coord = [
        'latitude' => $latitude,
        'longitude' => $longitude
    ]?>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const coord = <?php echo json_encode($coord);?>;
        var map = L.map('map').setView([coord.latitude, coord.longitude], 15);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map);

    </script>
<script src="../../js/fo/geolocalVendeur.js"></script>
<?php } ?>
</html>