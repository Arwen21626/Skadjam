<?php
session_start();
require_once __DIR__ . "/../../01_premiere_connexion.php";
require __DIR__ . '/../../php/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../php/PHPMailer/src/SMTP.php';
require __DIR__ . '/../../php/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// permet à $mailer d'envoyer des e-mails
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
    <!-- Affichage d'un champ input pour insérer une adresse mail -->
    <?php
    try{
        $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Si l'e-mail est présent, essaie d'envoyer un message à cette adresse
        if(isset($_POST['mail'])){
            ?><p><?php 
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
                $mailer->Body    = "Pour changer votre mot de passe, cliquez sur ce lien : http://10.253.5.109/html/fo/nouveau_mdp.php";

                // Envoi
                $mailer->send();

                // Permet de savoir quel compte doit changer de mot de passe
                $_SESSION["adresse_mail"] = $_POST["mail"];
                    
                echo "Vérifiez votre boîte de réception ainsi que vos spams."; ?></p><?php
            }catch(Exception $e){
                echo "Erreur : le mail n'a pas été envoyé."; ?></p><?php
            }
        }else{
    ?>
    <main class="md:min-h-[800px] min-h-[600px]">
        <form class="flex flex-col p-15 pt-0 justify-around align-center" action="reinitialiser_mdp.php" method="post">
            <div class="flex flex-col p-0 m-0 align-center self-center w-3/4">
                <label>Adresse mail :</label>
                <input class="border-4 border-vertClair rounded-2xl w-1/1 p-1 pl-3"  type="email" name='mail' id='mail'  required>
                <!--
                Si $_POST['mail'] est null, cela signifie qu'aucun n'à été envoyé.
                Alors on affiche un texte expliquant qu'un e-mail sera envoyé à l'adresse qu'ils entrent et un bouton "Recevoir un mail"
                Sinon
                On affiche un texte expliquant qu'il y a eu un problème et le label du bouton est changé en "Rééssayer"
                -->
                <p class="mt-10">Si un compte à cette adresse existe, vous recevrez un mail contenant un lien pour la réinitialisation.</p>
            </div>
            <div class="flex mt-10 justify-between md:justify-end w-1/1">
                <a href="../../index.php" class="text-center block border-4 border-solid p-1 pl-3 border-vertClair rounded-2xl w-40 cursor-pointer">Annuler</a>
                <input class="border-4 border-vertClair rounded-2xl w-60 h-14 p-0 m-0 md:mr-10 cursor-pointer" type="submit" value="Recevoir un mail">
            </div>
            <?php
                }
                $dbh = null;
            }catch(PDOException $e){
                print "Erreur : " . $e->getMessage();
            }
            ?>
        </form>
    </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
