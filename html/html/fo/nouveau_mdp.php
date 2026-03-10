<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../php/verification_formulaire.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";

$erreur = false;
try{
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Récupère l'adresse mail de profil_client ou de reinitialiser_mdp
    $mail = $_SESSION['adresse_mail'];

    // Traitement du formulaire seulement si toutes les données son saisie
    if(isset($_POST['mdp']) && isset($_POST['verifMdp'])){
        // Vérification que toutes les données saisie son correcte
        if(verifMotDePasse($_POST['mdp']) && ($_POST['mdp'] === $_POST['verifMdp'])){
            $mdp = password_hash(htmlentities($_POST['mdp']), PASSWORD_DEFAULT);
            // Enregistrer le nouveau mdp dans la BDD
            $nouvMdp = $dbh->prepare("UPDATE sae3_skadjam._compte
                                            SET mot_de_passe = :mdp
                                            WHERE adresse_mail = :mail");
            $nouvMdp->execute([
                ':mdp'  => $mdp,
                ':mail' => $mail
            ]);
            // Supprime toutes les variables de session
            session_unset();

            // Détruit la session
            session_destroy();

            // Redirection vers la page de connexion
            header("Location: http://localhost:8888/html/fo/connexion.php");
            exit();
        }else{ 
            $erreur = true;
        }
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Nouveau mot de passe</title>
</head>
<body class="h-1/1">
    <?php require __DIR__ . "/../../php/structure/header_front.php"; ?>
    <main class="flex flex-col items-center">
        
        <h2>Nouveau mot de passe</h2>
        
        <form class="md:w-1/2 p-15 pt-0" action="nouveau_mdp.php" method="post"> 
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mdp">Mot de passe* :</label>
                <div class="zone-mdp flex flex-row">
                    <input class="champ-mdp border-4 border-vertClair rounded-2xl w-1/1 p-1 pl-3" type="password" name="mdp" id="mdp" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
                <p style="font-size: 0.90em"> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
            </div>
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="verifMdp">Vérification du mot de passe* :</label>
                <div class="zone-mdp flex flex-row">
                    <input class="champ-mdp border-4 border-vertClair rounded-2xl w-1/1 p-1 pl-3" type="password" name="verifMdp" id="verifMdp" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
            </div>

            <!-- Ne s'affiche que si le mot de passe entré ne respecte pas la structure ordonnée ou qu'il est différent de la vérification du mot de passe -->
            <div class="flex w-fit flex-col mt-6 items-start ">
                <?php if($erreur){ ?>
                    <p class="text-rouge">Votre mot de passe ne respecte pas la structure ordinaire ou ne correspond pas à sa vérification.</p>
                <?php } ?>
            </div>

            <div class="flex justify-around">
                <a href="<?= $_SESSION['role'] === 'client' ? 'profil_client.php' : 'connexion.php'; ?>" class="text-center block border-2 border-solid border-vertClair rounded-2xl w-40 py-2.5 mr-6 cursor-pointer">Annuler</a>
                <input class="border-2 border-vertClair rounded-2xl w-40 h-14 p-0 m-0 cursor-pointer" type="submit" value="Confirmer">
            </div>
        </form>
    </main>
    <?php
    }catch(PDOException $e){
        print "Erreur : " . $e->getMessage() . "<br/>";
    }
    require __DIR__ . "/../../php/structure/footer_front.php";
    ?>
</body>
<script src="../../js/bo/visibilite_mdp.js"></script>

</html>
