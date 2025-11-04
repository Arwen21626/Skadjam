<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/general_front.css">
    <title>Mon compte</title>
</head>
<body>
    <?php
        include "connexion_client.php";
        try{
            $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
            
        }catch(PDOException $e){
            print "Erreur : " . $e->getMessage() . "<br>" ;
            die();
        }
    ?>
    <h2>Mon compte</h2>
    <article>
        <section>
            <img src="<?php echo $photo ?>" alt="Photo de profil" width="50" height="50">
            <h3><?php echo $nom ?></h3>
            <h3><?php echo $prenom ?></h3>
        </section>
        <h3><?php echo $telephone ?></h3>
        <h3><?php echo $mail ?></h3>
    </article>
    <article>
        <form action="modifier_client.php"><input type="submit" value="Modifier"></form>
        <form action="index.php"><input type="submit" value="Se déconnecter"></form>
    </article>
</body>
</html>
