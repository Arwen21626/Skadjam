<?php require_once "../../connections_params.php"; ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/fo/general_front.css">
    <title>Réinitialiser mon mot de passe</title>
</head>
<body>
    <h2>Mot de passe oublié</h2>
    <p>Mail entré : <?php echo $_POST['mail'] ?></p>
    <p>
    <?php
    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $mails = $dbh->prepare('SELECT adresseMail FROM compte');
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
</body>
</html>
