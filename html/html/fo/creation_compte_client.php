<?php
require_once("../../php/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once("A NE SURTOUT PAS COMMIT !!!!!!!!!!.php"); // données de connexion à la base de données
require_once("../../php/modification_variable.php"); // fonctions qui vérifient les données des formulaires

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>

    <?php
    // Traitement du formulaire seulement si toutes les données sont saisie
    if(isset($_POST['pseudo']) && isset($_POST['naissance']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['telephone']) && isset($_POST['mdp']) && isset($_POST['verifMdp'])){
        try{
            // Création de la session
            session_start();

            // Connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); 
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Vérification que toutes les données saisie sont correcte
            if (verifPseudo($_POST['pseudo']) && verifAge($_POST['naissance']) && verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['telephone']) && verifMail($_POST['mail']) && verifMotDePasse($_POST['mdp']) && confirmationMotDePasse($_POST['mdp'], $_POST['verifMdp']) && mailUnique($_POST['mail'])){
                // Traitement des données saisie
                $nom = htmlentities(format_prenom($_POST['nom']));
                $prenom = htmlentities(format_prenom($_POST['prenom']));
                $mail = htmlentities($_POST['mail']);
                $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
                $telephone = htmlentities(format_tel($_POST['telephone']));
                $pseudo = htmlentities($_POST['pseudo']);
                $naissance = htmlentities(format_date($_POST['naissance']));

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

                // à réparer car insert 0 rows
                $nouvPanier = $dbh->prepare("INSERT INTO sae3_skadjam._panier(id_panier, nb_produit_total, montant_total_ttc, date_derniere_modif, id_client)
                                                VALUES($idPanier, '0', '0.0', '$aujourdhui', $idCompte)");
                $nouvPanier->execute();

                // Fermer la connexion à la base de données
                $dbh = null;

                // Redirection vers la page d'accueil
                header("location: index.php");
            }
            // Messages d'erreurs si l'un des champs n'est pas rempli'
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
                elseif (!verifMotDePasse($_POST['mdp'])){
                    echo "le format de votre mot de passe n'est pas correct. ";
                    echo "il doit contenir au minimum une majuscule, une minuscule, un chiffre et un caractère spéciale (parmis : - @ _ # $ . £ ! ? % * + : ; , & ~ | ^) et 10 caractères";
                }
                elseif (!confirmationMotDePasse($_POST['mdp'], $_POST['verifMdp'])){
                    echo "le mot de passe de vérification ne correspond pas à votre mot de passe.";
                    echo "il doit être identique à votre mot de passe.";
                }
                elseif(!verifPseudo($_POST['pseudo'])){
                    echo "le format de votre pseudo n'est pas correcte. ";
                    echo "il ne peut que continir des majuscules, des minuscules, des chiffres ou des - ou des espace.";
                }
                elseif(!verifDate($_POST['naissance'])){
                    echo "le format de votre date de naissance n'est pas correcte. ";
                    echo "il doit être de la forme : aaaa-mm-jj.";
                }
                elseif(!mailUnique($_POST['mail'])){
                    echo "le mail saisie existe déjà. ";
                }
                elseif(!verifAge($_POST['naissance'])){
                    echo "vous avez moins de 18 ans.";
                }
                else{
                    echo "inconu.";
                }
                ?>

                <a href="./creation_compte_client.php">Retour</a>
            <?php }
        }
        catch(PDOException $e){
            print "Erreur dans l'envoie des données dans la base de données.";
            die();
        }
        
    }


    // Formulaire
    else{
        
    ?>

    <h2>Création du compte client</h2>

    <form action="./creation_compte_client.php" method="post"> 
        <label for="nom">Nom* :</label>
        <input type="text" name="nom" id="nom" required>

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="naissance">Date de naissance* :</label>
        <input type="date" name="naissance" id="naissance" required> 

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" required>

        <label for="mdp">Mot de passe* :</label>
        <input type="password" name="mdp" id="mdp" required>
        <p> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>

        <label for="verifMdp">Vérification du mot de passe* :</label>
        <input type="password" name="verifMdp" id="verifMdp" required>

        <p>Vous avez déjà un compte ?</p>
        <a href='se_connecter_compte_client.php'>Connectez vous</a>   <!-- lien à revoir en fonction du nom du fichier qui permet de se connecter -->

<!--bouton annuler-->
        <input type="Submit" name="submit" id="submit" value="S'inscrire">
    </form>
    <?php }?>

</body>
</html>
