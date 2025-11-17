<?php
session_start(); // Création de la session

require_once(__DIR__."/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once(__DIR__."/modification_variable.php"); // fonctions qui vérifient les données des formulaires
require_once(__DIR__."/../../connections_params.php"); // données de connexion à la base de données

// Traitement du formulaire seulement si toutes les données sont saisie
if(isset($_POST['pseudo']) && isset($_POST['naissance']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['telephone']) && isset($_POST['mail'])){
    try{
        // Connexion à la base de données
        $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass); 
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Vérification que toutes les données commune à la création et à la modification d'un compte client sont correcte
        if (verifPseudo($_POST['pseudo']) && verifAge($_POST['naissance']) && verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['telephone']) && (verifMail($_POST['mail']))){
            // Traitement des données saisie
            $nom = htmlentities(formatPrenom($_POST['nom']));
            $prenom = htmlentities(formatPrenom($_POST['prenom']));
            $mail = htmlentities($_POST['mail']);
            $telephone = htmlentities(formatTel($_POST['telephone']));
            $pseudo = htmlentities($_POST['pseudo']);
            $naissance = htmlentities(formatDate($_POST['naissance']));
            
            // S'il y a eu une erreur lors de l'execution
            $erreur = false;

            // Si le visiteur est entrain de créer son compte
            if (isset($_POST['mdp']) && isset($_POST['verifMdp'])){
                if(verifMotDePasse($_POST['mdp']) && confirmationMotDePasse($_POST['mdp'], $_POST['verifMdp']) && mailUnique($_POST['mail'])){
                    $mdp = password_hash(htmlentities($_POST['mdp']), PASSWORD_DEFAULT);

                    $aujourdhui = date('d/m/Y');
                    
                    // Insertion des données dans la base de données et création du panier
                    $nouvCompte = $dbh->prepare("WITH creation_compte AS (
                                                        INSERT INTO sae3_skadjam._compte(nom_compte, prenom_compte, adresse_mail, motDePasse, numero_telephone, bloque) 
                                                        VALUES ('$nom', '$prenom', '$mail', '$mdp', '$telephone', 'false') RETURNING id_compte
                                                    )
                                                    INSERT INTO sae3_skadjam._client(id_compte, pseudo, date_naissance) 
                                                        SELECT id_compte, '$pseudo', '$naissance'
                                                            FROM creation_compte");

                    $nouvCompte->execute();

                    foreach($dbh->query("SELECT cli.id_compte, cli.id_panier 
                                            FROM sae3_skadjam._client cli 
                                            INNER JOIN sae3_skadjam._compte c 
                                                ON c.id_compte = cli.id_compte 
                                            WHERE adresse_mail = '$mail'", PDO::FETCH_ASSOC) as $ligne){
                        $idCompte = $ligne['id_compte'];
                        $idPanier = $ligne['id_panier'];
                    }

                    $nouvPanier = $dbh->prepare("INSERT INTO sae3_skadjam._panier(id_panier, nb_produit_total, montant_total_ttc, date_derniere_modif, id_client)
                                                    VALUES($idPanier, '0', '0.0', '$aujourdhui', $idCompte)");
                    $nouvPanier->execute();

                    // Sauvegarde de l'id du compte client dans le cookie de session
                    $_SESSION["idCompte"] = $idCompte;
                }
                // Gestion des erreur spécifique à la création d'un compte
                elseif (!verifMotDePasse($_POST['mdp'])){
                    $erreur = true;
                    echo 'Erreur : ';
                    echo "le format de votre mot de passe n'est pas correct. ";
                    echo "il doit contenir au minimum une majuscule, une minuscule, un chiffre et un caractère spéciale (parmi : - @ _ # $ . £ ! ? % * + : ; , & ~ | ^) et 10 caractères";
                }
                elseif (!confirmationMotDePasse($_POST['mdp'], $_POST['verifMdp'])){
                    $erreur = true;
                    echo 'Erreur : ';
                    echo "le mot de passe de vérification ne correspond pas à votre mot de passe.";
                    echo "il doit être identique à votre mot de passe.";
                }
                elseif(!mailUnique($_POST['mail'])){
                    $erreur = true;
                    echo 'Erreur : ';
                    echo "le mail saisie existe déjà. ";
                }
            }
            // Si le client est entrain de modifier son compte
            else{
                $idCompte = $_SESSION['idCompte'];

                // Récupération de l'ancien email
                foreach($dbh->query("SELECT c.adresse_mail 
                                        FROM sae3_skadjam._compte c 
                                        WHERE id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
                    $ancienMail = $ligne['adresse_mail'];
                }
                
                // Vérification de l'email et de l'adresse
                if ((mailUnique($mail) || $ancienMail === $mail)){

                    // Modification du compte
                    $modifCompte = $dbh->prepare("UPDATE sae3_skadjam._compte
                                                    SET nom_compte = '$nom', prenom_compte = '$prenom', adresse_mail = '$mail', numero_telephone = '$telephone'
                                                    WHERE id_compte = $idCompte");

                    $modifClient = $dbh->prepare("UPDATE sae3_skadjam._client
                                                    SET pseudo = '$pseudo', date_naissance = '$naissance'
                                                    WHERE id_compte = $idCompte");
                    $modifCompte->execute();
                    $modifClient->execute();

                    // Modification des adresses
                    $compteur = 1;
                    foreach($dbh->query("SELECT h.id_adresse
                                            FROM sae3_skadjam._habite h
                                            WHERE id_compte = $idCompte
                                            ORDER BY id_adresse ASC", PDO::FETCH_ASSOC) as $ligne){
                        if(isset($_POST['adresse'][$compteur]['adressePostal']) && isset($_POST['adresse'][$compteur]['ville']) && isset($_POST['adresse'][$compteur]['codePostal'])){

                            $numRue = htmlentities(tabAdresse($_POST['adresse'][$compteur]['adressePostal'])[0]);
                            $nomRue = htmlentities(tabAdresse($_POST['adresse'][$compteur]['adressePostal'])[2]);
                            $complement = htmlentities(tabAdresse($_POST['adresse'][$compteur]['adressePostal'])[1]);
                            $numBat = htmlentities($_POST['adresse'][$compteur]['batiment']);
                            $numApart = htmlentities($_POST['adresse'][$compteur]['apart']);
                            $interphone = htmlentities($_POST['adresse'][$compteur]['interphone']);
                            $codePostal = htmlentities($_POST['adresse'][$compteur]['codePostal']);
                            $ville = htmlentities($_POST['adresse'][$compteur]['ville']);


                            if (verifAdresse($numRue.' '.$complement.' '.$nomRue) && verifVille($ville) && verifCp($codePostal)){
                            
                                $idAdresse = $ligne['id_adresse'];
                                $modifAdresse = $dbh->prepare("UPDATE sae3_skadjam._adresse
                                                                SET numero_rue = $numRue, numero_bat = '$numBat', numero_appart = '$numApart', code_interphone = '$interphone', code_postal = $codePostal, complement_adresse = '$complement', ville = '$ville', adresse_postale = '$nomRue'
                                                                WHERE id_adresse = $idAdresse");

                                $modifAdresse->execute();
                            }
                            // Erreurs consernant le format de l'adresse
                            elseif(!verifAdresse($numRue.' '.$complement.' '.$nomRue)){
                                $erreur = true;
                                echo "Erreur : sur l'adresse numéro $compteur, le format de l'adresse postale n'est pas correcte. ";
                                echo "Exemple : 3 bis rue des camélia";
                            }
                            elseif(!verifVille($ville)){
                                $erreur = true;
                                echo "Erreur : sur l'adresse numéro $compteur, le format de la ville n'est pas correcte. ";
                                echo "Elle ne peut contenir que des lettres, des espaces et des -";
                            }
                            elseif(!verifCp($codePostal)){
                                $erreur = true;
                                echo "Erreur : sur l'adresse numéro $compteur, le format du code postale n'est pas correcte. ";
                                echo "Il doit contnire exactement 5 chiffres.";
                            }
                        }
                        // erreur si l'un des champs obligatoire des adresses n'est pas rempli
                        else{
                            $erreur = true;
                            echo "vous n'avez pas rempli tous les champs obligatoires de l'adresse numéro $compteur, elle n'a donc pas été modifier.";
                        }
                        $compteur++;
                    }
                }
                // Erreur consernant l'unicité du mail
                elseif(!mailUnique($mail) || !($ancienMail === $mail)){
                    $erreur = true;
                    echo "Erreur : le mail saisie existe déjà. ";
                }
                
                
            }
            // Fermer la connexion à la base de données
            $dbh = null;

            // Redirection vers la page d'accueil
            if (!$erreur && isset($_POST['mdp']) && isset($_POST['verifMdp'])){ // Si c'est la création d'un compte
                header("location: ../html/fo/index.php");
            } 
            elseif(!$erreur && !isset($_POST['mdp']) && !isset($_POST['verifMdp'])){ // Si c'est la modification d'un compte
                header("location: ../html/fo/page_client.php");
            }

        }
        // Messages d'erreurs si l'un des champs est mal rempli
        else{ 
            echo "Erreur : ";
            if (!verifNomPrenom($_POST['nom'])){
                echo "le format de votre nom n'est pas correct. ";
                echo "il ne peut contenir que des majuscules, des minuscules, des - ou des espaces.";
            }
            elseif (!verifNomPrenom($_POST['prenom'])){
                echo "le format de votre prénom n'est pas correct. ";
                echo "il ne peut contenir que des majuscules, des minuscules, des - et des espaces.";
            }
            elseif (!verifMail($_POST['mail'])){
                echo "le format de votre mail n'est pas correct. ";
                echo "exemple : Charlotte@gmail.com";
            }
            elseif (!verifTelephone($_POST['telephone'])){
                echo "le format de votre numéro de téléphone n'est pas correct. ";
                echo "il doit commencer par 0 suivi de 9 chiffres.";
            }
            elseif(!verifPseudo($_POST['pseudo'])){
                echo "le format de votre pseudo n'est pas correcte. ";
                echo "il ne peut que continir des majuscules, des minuscules, des chiffres ou des - ou des espace.";
            }
            elseif(!verifDate($_POST['naissance'])){
                echo "le format de votre date de naissance n'est pas correcte. ";
                echo "il doit être de la forme : aaaa-mm-jj.";
            }
            elseif(!verifAge($_POST['naissance'])){
                echo "vous avez moins de 18 ans.";
            }
            else{
                echo "inconnu.";
            }
            ?>

            <a href="../html/fo/creation_compte_client.php">Retour</a>
        <?php }
    }
    catch(PDOException $e){
        echo "Erreur dans l'envoie des données dans la base de données.";
        echo $e; // Potentiellement temporaire

        die();
    }
    
}
else{
    echo "vous n'avez pas rempli tous les champs obligatoires.";
}
