<?php
require_once("../php/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once("A NE SURTOUT PAS COMMIT !!!!!!!!!!.php"); // données de connexion à la base de données

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
    if(isset($_POST['naissance']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['mail']) && isset($_POST['telephone']) && isset($_POST['mdp']) && isset($_POST['verifMdp'])){
        try{
            // Création de la session
            session_start();

            // connexion à la base de données
            $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); 
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Vérification que toutes les données saisie sont correcte
            if (verifNomPrenom($_POST['pseudo']) && verifNomPrenom($_POST['naissance']) && verifNomPrenom($_POST['nom']) && verifNomPrenom($_POST['prenom']) && verifTelephone($_POST['telephone']) && verifMail($_POST['mail']) && verifMotDePasse($_POST['mdp']) && ($_POST['mdp'] === $_POST['verifMdp'])){
                // Traitement des données pour celle qui en ont besoin
                $nom = htmlentities($_POST['nom']);
                $prenom = htmlentities($_POST['prenom']);
                $mail = htmlentities($_POST['mail']);
                $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);
                $telephone = htmlentities($_POST['telephone']);
                $pseudo = htmlentities($_POST['pseudo']);
                $naissance = htmlentities($_POST['naissance']);

                // Insertion des données dans la base de données
//                 WITH insertion AS (INSERT INTO _document(content, author) VALUES ('Hello Thomas. Tu reprends le foot cette année ?', 'Artur') RETURNING iddoc)
//   INSERT INTO _comment(iddoc, ref) SELECT iddoc, 6 FROM insertion;
                $insertNouvDept = $dbh->prepare("INSERT INTO sae3_skadjam._compte(nom_compte, prenom_compte, adresse_mail, motDePasse, numero_telephone, bloque)
                                                    VALUES('$nom', '$prenom', '$mail', '$mdp', '$telephone', 'false')");
                $insertNouvDept = $dbh->prepare("INSERT INTO sae3_skadjam._panier(nb_produit_total_ttc, montant_total_ttc, date_derniere_modif, id_client)
                                                    VALUES('$nom', '$prenom', '$mail', '$mdp', '$telephone', 'false')");
                $insertNouvDept = $dbh->prepare("INSERT INTO sae3_skadjam._client(id_compte, pseudo, date_naissance, id_panier)
                                                    VALUES('$nom', '$pseudo', '$naissance', '$id_panier')");
                $insertNouvDept->execute();

                // Fermer la connexion à la base de données
                $dbh = null;

                // Redirection vers la page d'accueil
                header("location: index.php");
            }
            // Messages d'erreurs si l'un des champs n'est pas rempli'
            else{ 
                echo "Erreur : ";
                if (!verifNomPrenom($_POST['nom'])){
                    echo "le format de votre nom n'est pas correct.";
                    echo "il ne doit contenir que : ";                //a mettre partout et a remplir
                }
                elseif (!verifNomPrenom($_POST['prenom'])){
                    echo "le format de votre prénom n'est pas correct.";
                }
                elseif (!verifMail($_POST['mail'])){
                    echo "le format de votre mail n'est pas correct.";
                }
                elseif (!verifTelephone($_POST['telephone'])){
                    echo "le format de votre numéro de téléphone n'est pas correct.";
                }
                elseif (!verifMotDePasse($_POST['mdp'])){
                    echo "le format de votre mot de passe n'est pas correct.";
                }
                elseif (!($_POST['verifMdp'] === $_POST['mdp'])){
                    echo "le mot de passe de vérification ne correspond pas à votre mot de passe.";
                }
                elseif(!verifPseudo($_POST['pseudo'])){
                    echo "le format de votre pseudo n'est pas correcte.";
                }
                elseif(!verifDate($_POST['date'])){
                    echo "le format de votre date de naissance n'est pas correcte.";
                }
                else{
                    echo "inconu.";
                }?>

                <a href="./creation_compte_client.php">Retour</a>
            <?php }
        }
        catch(PDOException $e){
            print "La connexion à la base de données à échouer.";
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
        <input type="text" name="pseudo" id="pseudo">

        <label for="naissance">Date de naissance* :</label>
        <input type="date" name="naissance" id="naissance" required> vérifier qu'il à plus de 18 ans

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="+33604030201" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" required>

        <label for="mdp">Mot de passe* :</label>
        <input type="password" name="mdp" id="mdp" required>
        <p> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractère minimum</p>

        <label for="verifMdp">Vérification du mot de passe* :</label>
        <input type="password" name="verifMdp" id="verifMdp" >

        <p>Vous avez déjà un compte ?</p>
        <a href='se_connecter_compte_client.php'>Connectez vous</a>   <!-- lien à revoir en fonction du nom du fichier qui permet de se connecter -->

<!--bouton annuler-->
        <input type="Submit" name="submit" id="submit" value="S'inscrire">
    </form>
    <?php }?>

</body>
</html>
