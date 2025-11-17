<?php
require_once __DIR__ . "/../../01_premiere_connexion.php";
require __DIR__ . '/../../php/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../php/PHPMailer/src/SMTP.php';
require __DIR__ . '/../../php/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

$mailer = new PHPMailer(true);
?>
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
    <form class="flex flex-col p-15 pt-0 justify-around align-center" action="reinitialiser_mdp.php" method="post">
        <div class="flex flex-col p-0 m-0 align-center self-center w-3/4">
            <label>Adresse mail :</label>
            <input class="border-4 border-vertClair rounded-2xl w-1/1 p-1 pl-3"  type="email" name='mail' id='mail' required>
            <p class="mt-10">Si un compte à cette adresse existe, vous recevrez un mail contenant un lien pour la réinitialisation.</p>
        </div>
        <div class="flex mt-10 justify-center md:justify-end w-1/1">
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

            // L'e-mail est-il présent dans la BDD ?
            $mailPresent = false;
            foreach($dbh->query("SELECT adresse_mail FROM sae3_skadjam._compte", PDO::FETCH_ASSOC) as $mail){
                if($mail['adresse_mail'] === $_POST['mail']){
                    $mailPresent = true;
                }
            }

            // Si l'e-mail est présent, essaie d'envoyer un message à cette adresse
            if($mailPresent){
                if(isset($_POST['mail'])){
                    try {
                        // Configuration SMTP
                        $mailer->isSMTP();
                        $mailer->Host       = 'smtp.gmail.com';
                        $mailer->SMTPAuth   = true;
                        $mailer->Username   = 'alizon.reinitialisation@gmail.com';
                        $mailer->Password   = 'jjab jifb lmms dfuz';
                        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mailer->Port       = 587;

                        // Expéditeur
                        $mailer->setFrom('alizon.reinitialisation@gmail.com', 'Alizon');
                    
                        // Destinataire
                        $mailer->addAddress($_POST['mail']);

                        // Contenu
                        $mailer->isHTML(false);
                        $mailer->Subject = 'Alizon : reinitialiser votre mot de passe';
                        $mailer->Body    = "Pour changer votre mot de passe, cliquez sur ce lien : http://localhost:8888/html/html/fo/nouveau_mdp.php";

                        // Envoi
                        $mailer->send();
                        echo "Vérifiez votre boîte de réception ainsi que vos spams.";
                    } catch (Exception $e) {
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
        } ?>
    </p>
    <?php }
        require __DIR__ . "/../../php/structure/footer_front.php";
    ?>
</body>
</html>
