<?php
session_start(); // Création de la session
$idCompte = $_SESSION["idCompte"];

require_once __DIR__."/verification_formulaire.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__."/modification_variable.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__."/../../connections_params.php"; // données de connexion à la base de données

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
            $idPhoto = htmlentities($_POST["idPhoto"]);
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
            }else if(!mailUnique($mail) && !($ancienMail === $mail)){
                $erreur = true;
                echo "Erreur : le mail saisie existe déjà. ";
            }

            $nom_photo_finale = basename($urlPhoto);

            if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                $typesAutorises = ['image/jpeg', 'image/png', 'image/webp'];
                if (in_array($_FILES['photo']['type'], $typesAutorises)) {
                    // Supprimer ancienne photo
                    $anciennePhotoPath = __DIR__ . '/../../images/photo_importees/' . basename($urlPhoto);
                    if (file_exists($anciennePhotoPath) && strpos($urlPhoto, 'image.svg') === false) {
                        unlink($anciennePhotoPath);
                    }
                    // Nouvelle photo
                    $ext = explode('/', $_FILES['photo']['type'])[1];
                    $nom_photo_finale = explode(' ', trim($nom))[0] . '_' . time() . '.' . $ext;
                    $destination = __DIR__ . '/../../images/photo_importees';
                    move_uploaded_file(
                        $_FILES['photo']['tmp_name'],
                        $destination . '/' . $nom_photo_finale
                    );
                }
            }



            // Fermer la connexion à la base de données
            $dbh = null;

            // Redirection vers la page d'accueil
            header("location: ../html/bo/profil_vendeur.php");

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
            <a href="../html/bo/modifier_compte_vendeur.php">Retour</a>
        <?php 
        }
    }catch(PDOException $e){
        echo "Erreur dans l'envoie des données dans la base de données.";
        echo $e->getMessage();
        die();
    }
    
}else{
    echo "vous n'avez pas rempli tous les champs obligatoires.";
}