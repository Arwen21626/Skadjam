<?php require_once "../../connections_params.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require "../../php/structure/head_front.php"; ?>
    <title>Réinitialiser mon mot de passe</title>
</head>
<body>
    <?php
        require "../../php/structure/header_front.php";
    ?>
    <h2>
        <?php
        if($_POST["titre"] === null){
            echo "Mot de passe oublié";
        }else{
            echo $_POST["titre"];
        }
        ?>
    </h2>
    <?php if($_POST["mail"] === null){ ?>
    <form action="reinitialiser_mdp.php" method="post">
        <label>Adresse mail :</label>
        <br>
        <input type="email" name="mail" id="mail" required>
        <br>
        <br>
        <p>Si un compte à cette adresse existe, vous recevrez un mail contenant un lien pour la réinitialisation.</p>
        <br>
        <br>
        <input type="submit" value="Recevoir un mail">
    </form>
    <?php }else{ ?>
        <p>Mail entré : <?php echo $_POST["mail"]; ?></p>
    <p>
    <?php
    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $mails = $dbh->prepare('SELECT adresse_mail FROM _compte');
        $mailPresent = false;

        foreach ($mails as $mail) {
            if($mail === $_POST['mail']){
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
        require "../../php/structure/footer_front.php";
    ?>
</body>
</html>
