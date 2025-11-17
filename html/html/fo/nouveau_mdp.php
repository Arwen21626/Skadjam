<?php
session_start();
require_once __DIR__ . "/../../php/verification_formulaire.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Nouveau mot de passe</title>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";

    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $id = $_POST['id'];

        // Traitement du formulaire seulement si toutes les données son saisie
        if(isset($_POST['mdp']) && isset($_POST['verifMdp'])){
            // Vérification que toutes les données saisie son correcte
            if(verifMotDePasse($_POST['mdp']) && ($_POST['mdp'] === $_POST['verifMdp'])){
                $mdp = password_hash(htmlentities($_POST['mdp']), PASSWORD_DEFAULT);
                // Enregistrer le nouveau mdp dans la BDD
                $nouvMdp = $dbh->prepare("UPDATE sae3_skadjam._compte 
                                                    SET motDePasse = $mdp
                                                    WHERE id_compte = $id");
                $nouvMdp->execute();
                // Redirection vers la page de connexion
                header("Location: connexion.php");
            }else{ 
                echo "Erreur";
            }
        }else{ ?>
            <h2>Nouveau mot de passe</h2>

            <form action="nouveau_mdp.php" method="post"> 
                <label for="mdp">Mot de passe* :</label>
                <input type="password" name="mdp" id="mdp" required>
                <ul>
                    <li>1 majuscule,</li>
                    <li>1 minuscule,</li>
                    <li>1 chiffre,</li>
                    <li>1 caractère spécial,</li>
                    <li>10 caractère minimum</li>
                </ul>

                <label for="verifMdp">Vérification du mot de passe* :</label>
                <input type="password" name="verifMdp" id="verifMdp" required>

                <input type="submit" value="Confirmer">
            </form><?php
        }
    }catch(PDOException $e){
        print "Erreur : " . $e->getMessage() . "<br/>";
    }
    require __DIR__ . "/../../php/structure/footer_front.php";
    ?>
</body>
</html>
