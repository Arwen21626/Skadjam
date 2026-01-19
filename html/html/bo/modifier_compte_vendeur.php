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

$idCompte = $_SESSION["idCompte"]; ?>
<!DOCTYPE html>
<html lang="fr">
<?php include (__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <title>Modification du compte vendeur</title>
    <style>
        button a:hover {
            color: #000; 
        }
    </style>
</head>
<?php
// Traitement du formulaire seulement si toutes les données sont saisie
if (isset($_POST["nom"]) && isset($_POST["prenom"]) && isset($_POST["mail"]) && isset($_POST["tel"]) && isset($_POST["denomination"]) && isset($_POST["raisonSociale"]) && isset($_POST["iban"]) && isset($_POST["adresse"]) && isset($_POST["ville"]) && isset($_POST["cp"]) && isset($_POST["siren"])){
    // Récupération des données du formulaire
    try{
        // Vérification que toutes les données commune à la création et à la modification d'un compte client sont correcte
        if (verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['tel']) && verifDenomination($_POST['denomination']) && verifDenomination($_POST['raisonSociale']) && verifIban($_POST['iban']) && verifSiren($_POST['siren']) && verifCp($_POST['cp']) && verifVille($_POST['ville']) && verifAdresse($_POST['adresse'])){
            //récuperer les attributs du post
            $nom = htmlentities(formatPrenom($_POST["nom"]));
            $prenom = htmlentities(formatPrenom($_POST["prenom"]));
            $mail = htmlentities($_POST["mail"]);
            $tel = htmlentities(formatTel($_POST["tel"]));
            $denomination = htmlentities($_POST["denomination"]);
            $raisonSociale = htmlentities($_POST["raisonSociale"]);
            $iban = htmlentities($_POST["iban"]);
            $adresse = htmlentities($_POST["adresse"]);
            $ville = htmlentities($_POST["ville"]);
            $cp = htmlentities($_POST["cp"]);
            $siren = htmlentities($_POST["siren"]);
            $description = isset($_POST["description"]) ? $_POST["description"] : "";
            $temp = tabAdresse($adresse);
            $numero = $temp[0];
            $compNum = $temp[1];
            $adresse = $temp[2];

            // S'il y a eu une erreur lors de l'execution
            $erreur = false;

            // Récupération de l'ancien email
            foreach($dbh->query("SELECT c.adresse_mail 
                                    FROM sae3_skadjam._compte c 
                                    WHERE id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
                $ancienMail = $ligne['adresse_mail'];
            }
            // Vérification de l'email et de l'adresse
            if(mailUnique($mail) || $ancienMail === $mail){
                // Modification du compte
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
                    if(isset($_POST['adressePostal']) && isset($_POST['ville']) && isset($_POST['codePostal'])){

                        $numRue = htmlentities(tabAdresse($_POST['adressePostal'])[0]);
                        $nomRue = htmlentities(tabAdresse($_POST['adressePostal'])[2]);
                        $complement = htmlentities(tabAdresse($_POST['adressePostal'])[1]);
                        $numBat = htmlentities($_POST['batiment']);
                        $numApart = htmlentities($_POST['apart']);
                        $interphone = htmlentities($_POST['interphone']);
                        $codePostal = htmlentities($_POST['codePostal']);
                        $ville = htmlentities($_POST['ville']);

                        if (verifAdresse($numRue.' '.$complement.' '.$nomRue) && verifVille($ville) && verifCp($codePostal)){
                            $idAdresse = $ligne['id_adresse'];
                            $modifAdresse = $dbh->prepare("UPDATE sae3_skadjam._adresse
                                                            SET numero_rue = $numRue, numero_bat = '$numBat', numero_appart = '$numApart', code_interphone = '$interphone', code_postal = $codePostal, complement_adresse = '$complement', ville = '$ville', adresse_postale = '$nomRue'
                                                            WHERE id_adresse = $idAdresse");
                            $modifAdresse->execute();
                        // Erreurs concernant le format de l'adresse
                        }else if(!verifAdresse($numRue.' '.$complement.' '.$nomRue)){
                            $erreur = true;
                            echo "Erreur : sur l'adresse, le format de l'adresse postale n'est pas correcte. ";
                            echo "Exemple : 3 bis rue des camélia";
                        }else if(!verifVille($ville)){
                            $erreur = true;
                            echo "Erreur : sur l'adresse, le format de la ville n'est pas correcte. ";
                            echo "Elle ne peut contenir que des lettres, des espaces et des -";
                        }else if(!verifCp($codePostal)){
                            $erreur = true;
                            echo "Erreur : sur l'adresse, le format du code postale n'est pas correcte. ";
                            echo "Il doit contenir exactement 5 chiffres.";
                        }
                    // erreur si l'un des champs obligatoire des adresses n'est pas rempli
                    }else{
                        $erreur = true;
                        echo "vous n'avez pas rempli tous les champs obligatoires de l'adresse, elle n'a donc pas été modifier.";
                    }
                }
            // Erreur concernant l'unicité du mail
            }else if(!mailUnique($mail) && $ancienMail !== $mail){
                $erreur = true;
                echo "Erreur : le mail saisie existe déjà. ";
            }

            $urlPhoto = '/images/logo/bootstrap_icon/image.svg';

            // Récupération de la photo existante (si elle existe)
            $reqPhoto = $dbh->prepare("SELECT ph.id_photo, ph.url_photo
                                        FROM sae3_skadjam._presente pr
                                        INNER JOIN sae3_skadjam._photo ph
                                            ON pr.id_photo = ph.id_photo
                                        WHERE pr.id_vendeur = $idCompte");
            $reqPhoto->execute();

            $photoExistante = $reqPhoto->fetch();

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


            // Fermer la connexion à la base de données
            $dbh = null;

            // Redirection vers la page d'accueil
            header("location: profil_vendeur.php");

        // Messages d'erreurs si l'un des champs est mal rempli
        }else{ 
            echo "Erreur : ";
            if(!verifNomPrenom($_POST['nom'])){
                echo "le format de votre nom n'est pas correct. ";
                echo "il ne peut contenir que des majuscules, des minuscules, des - ou des espaces.";
            }else if(!verifNomPrenom($_POST['prenom'])){
                echo "le format de votre prénom n'est pas correct. ";
                echo "il ne peut contenir que des majuscules, des minuscules, des - et des espaces.";
            }else if(!verifMail($_POST['mail'])){
                echo "le format de votre mail n'est pas correct. ";
                echo "exemple : Charlotte@gmail.com";
            }else if(!mailUnique($_POST['mail'])){
                echo "le mail saisie existe déjà. ";
            }else if(!verifTelephone($_POST['tel'])){
                echo "le format de votre numéro de téléphone n'est pas correct. ";
                echo "il doit commencer par 0 suivi de 9 chiffres.";
            }else if(!verifAdresse($_POST['adresse'])){
                echo "le format de votre adresse n'est pas correct. ";
                echo "exemple : 3 bis rue des camélias";
            }else if(!verifDenomination($_POST['denomination'])){
                echo "le format de votre dénomination n'est pas correct. ";
                echo "il ne peut contenir que des lettres, des chiffres, des - et des espaces.";
            }else if(!verifDenomination($_POST['raisonSociale'])){
                echo "le format de votre raison sociale n'est pas correct. ";
                echo "il ne peut contenir que des lettres, des chiffres, des - et des espaces.";
            }else if(!verifIban($_POST['iban'])){
                echo "le format de votre IBAN n'est pas correct. ";
                echo "il doit commencer par FR suivi de 12 chiffres et de 11 caractères alphanumériques.";
            }else if(!verifSiren($_POST['siren'])){
                echo "le format de votre SIREN n'est pas correct. ";
                echo "il doit contenir exactement 9 chiffres.";
            }else if(!verifCp($_POST['cp'])){
                echo "le format de votre code postal n'est pas correct. ";
                echo "il doit contenir exactement 5 chiffres.";
            }else if(!verifVille($_POST['ville'])){
                echo "le format de votre ville n'est pas correct. ";
                echo "il ne peut contenir que des lettres, des - et des espaces.";
            }else{
                echo "inconnu.";
            } ?>
            <a href="modifier_compte_vendeur.php">Retour</a>
        <?php 
        }
    }catch(PDOException $e){
        echo "Erreur dans l'envoie des données dans la base de données.";
        echo $e->getMessage();
        die();
    }
    
}else{

    // Préparation des données qui vont remplir les champs du formulaire
    // Récupération du comptes clients
    foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON c.id_compte = v.id_compte
                                    WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
        // Infos compte
        $nom = $ligne['nom_compte'];
        $prenom = $ligne['prenom_compte'];
        $mail = $ligne['adresse_mail'];
        $tel = $ligne['numero_telephone'];
        $tel = "0" . substr($tel, 3);

        // Infos vendeur
        $denom = $ligne["raison_sociale"];
        $siren = $ligne["siren"];
        $iban = $ligne["iban"];
        $raisonSociale = $ligne["raison_sociale"];
        $description = $ligne["description_vendeur"] != '' ? $ligne["description_vendeur"] : "Aucune description.";
    }
    // Infos photo
    foreach($dbh->query("SELECT * FROM sae3_skadjam._presente pr
                        INNER JOIN sae3_skadjam._photo ph
                            ON pr.id_photo = ph.id_photo
                        WHERE pr.id_vendeur = $idCompte", PDO::FETCH_ASSOC) as $photo){
        $url = $photo['url_photo'];
        $alt = $photo['alt'];
        $title = $photo['titre'];
    }
    // Infos adresse
    foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                        INNER JOIN sae3_skadjam._habite h
                            ON c.id_compte = h.id_compte
                        INNER JOIN sae3_skadjam._adresse a
                            ON h.id_adresse = a.id_adresse
                        WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $adresseData){
        $adresse = $adresseData["adresse_postale"];
        $num = $adresseData["numero_rue"];
        $numBis = $adresseData["complement_adresse"];
        $cp = $adresseData["code_postal"];
        $ville = $adresseData["ville"];
    } ?>
    <body>
        <?php include __DIR__."/../../php/structure/header_back.php"; ?>
        <main style="margin: 0" class="flex flex-col justify-center">
            <?php include __DIR__."/../../php/structure/navbar_back.php"; ?>

            <h2 class="flex justify-center text-center">Modification du compte vendeur</h2>
            <!-- Formulaire -->
            <form class="flex flex-col flex-wrap p-15 pt-0 justify-around" action="modifier_compte_vendeur.php" method="post" enctype="multipart/form-data">
                <!-- Photo de profil -->
                <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                    <input type="file" id="photo" name="photo" class="hidden">
                    <!-- label qui agit comme bouton -->
                    <label id="labelImage" for="photo" class="bg-beige w-60 h-60 rounded-2xl image-produit cursor-pointer" style="background-image: url('../../images<?= isset($url) ? $url : '/logo/bootstrap_icon/image.svg'; ?>'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                    <label class="cursor-pointer" for="photo"><h4><strong>Photo de profil *</strong></h4></label>
                </div>

                <!-- Vendeur -->
                <h3>Informations vendeur :</h3>
                <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10 @max-[768px]:ml-5 @max-[768px]:mr-5">
                    <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                        <label for="nom">Nom * :</label>
                        <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="nom" name="nom" value="<?= $nom; ?>" size="30" required >
                    </div>
                    <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                        <label for="prenom">Prénom * :</label>
                        <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="prenom" name="prenom" value="<?= $prenom; ?>" size="30" required>
                    </div>
                    <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                        <label for="mail">Mail * :</label>
                        <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="email" id="mail" name="mail" value="<?= $mail; ?>" size="30" required>
                    </div>
                    <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                        <label for="tel">Numéro de téléphone * :</label>
                        <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="tel" id="tel" name="tel" value="<?= $tel; ?>" size="10" required>
                    </div>
                </div>

                <!-- Entreprise -->
                <h3>Informations entreprise :</h3>
                <div class="flex flex-col flex-wrap">
                    <div class="flex flex-row no-wrap justify-between ml-10 mr-10 @max-[768px]:ml-5 @max-[768px]:mr-5">
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="raisonSociale" name="raisonSociale" value="<?= $raisonSociale; ?>" size="30" required>
                        </div>
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="denomination">Nom de l'entreprise * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="denomination" name="denomination" value="<?= $denom; ?>" size="30" required>
                        </div> 
                    </div>
                    <div class="flex flex-row no-wrap justify-between ml-10 mb-7 mr-10 @max-[768px]:ml-5 @max-[768px]:mr-5">
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="siren">Numéro de SIREN * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="siren" name="siren" value="<?= $siren; ?>" size="10" required>
                        </div>
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="iban">Numéro de IBAN * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" type="text" id="iban" name="iban" value="<?= $iban; ?>" placeholder="FR" size="30" required>
                        </div>
                    </div>
                </div>

                <!-- Adresse -->
                <h3>Siège social :</h3>
                <div class="flex flex-col no-wrap justify-between ml-10 mb-7 mr-10">
                    <div class="flex flex-row no-wrap justify-between">
                        <div class="flex flex-col no-wrap items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="adresse">Adresse * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="adresse" name="adresse" value="<?= $num . (isset($numBis) ? " $numBis" : " ") . $adresse; ?>" size="50" placeholder="ex : 3 rue des camélias" required>
                        </div>
                    </div>
                    <div class="flex flex-row no-wrap justify-between">
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="ville">Ville * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="ville" name="ville" value="<?= $ville; ?>" size="50" required>
                        </div>
                        <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                            <label for="cp">Code Postal * :</label>
                            <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="cp" name="cp" value="<?= $cp; ?>" size="10" required>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <h3>Description :</h3>
                <div class="flex flex-col no-wrap justify-between ml-10 mb-7 mr-10">
                    <textarea class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 w-1/1" id="description" name="description" rows="5"><?= isset($description) ? $description : ''; ?></textarea>
                </div>

                <!-- Valider le formulaire -->
                <div class="flex mt-10 justify-center md:justify-end w-1/1">
                    <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10" type="button"><a href="./profil_vendeur.php">Annuler</a></button>
                    <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40  h-14 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="Valider">
                </div>
            </form>
        </main>

        <?php 
        // Import du footer
        include __DIR__ . "/../../php/structure/footer_back.php";

        // Fermer la connexion à la base de données
        $dbh = null;
        ?>
        <script src="../../js/bo/changement_image_produits.js"></script>
    </body>
    </html>
<?php } ?>