<?php require_once __DIR__ . "/../../01_premiere_connexion.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Réinitialiser mon mot de passe</title>
</head>
<body>
    <?php require __DIR__ . "/../../php/structure/header_front.php"; ?>
    <h2 class="flex justify-center text-center">Mot de passe oublié</h2>
    <?php if($_POST['mail'] === null){ ?>
    <form class="flex flex-col p-15 pt-0 justify-around justify-items-center" action="reinitialiser_mdp.php" method="post">
        <label>Adresse mail :</label>
        <br>
        <input class="border-4 border-vertClair rounded-2xl w-3/4 p-1 pl-3"  type="email" name='mail' id='mail' required>
        <p>Si un compte à cette adresse existe, vous recevrez un mail contenant un lien pour la réinitialisation.</p>
        <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
            <input class="border-2 border-vertClair rounded-2xl w-40 h-14 p-0 m-0 md:mr-10" type="submit" value="Recevoir un mail">
        </div>
    </form>
    <?php }else{ ?>
        <p>Mail entré : <?php echo $_POST['mail']; ?></p>
    <p>
    <?php
    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Le mail est-il présent dans la BDD ?
        $mailPresent = false;
        foreach($dbh->query("SELECT adresse_mail FROM sae3_skadjam._compte", PDO::FETCH_ASSOC) as $mail){
            if($mail['adresse_mail'] === $_POST['mail']){
                $mailPresent = true;
            }
        }

        if($mailPresent){
            if(isset($_POST['mail'])){
                $retour = mail($_POST['mail'],"Alizon : réinitialiser votre mot de passe","test");
                if($retour){
                    echo "Vérifiez votre adresse mail.";
                }else{
                    echo "Erreur : le mail n'a pas été envoyé.";
                }
            }else{
                echo "Erreur : Aucun mail n'a été entré ou une erreur est survenue.";
            }
        }else{
            echo "Ce mail ne correspond à aucun compte.";
        }
        $dbh = null;
    }catch(PDOException $e){
        print "Erreur : " . $e->getMessage() . "<br/>";
    }
    ?>
    </p>
    <?php }
        require __DIR__ . "/../../php/structure/footer_front.php";
    ?>
</body>
</html>
