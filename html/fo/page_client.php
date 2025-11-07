<?php session_start(); ?>
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
        if (empty($_SESSION['idCompte'])) {
            // client non connecté -> redirection vers connexion
            header('Location: connexion_client.php');
            exit;
        }

        $id = (int) $_SESSION['idCompte'];

        try{
            $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Récupérer toutes les infos du client
            $infosClient = $dbh->prepare('SELECT idCompte, nomCompte, prenomCompte, adresseMail, numeroTelephone FROM compte WHERE idCompte = :idCompte LIMIT 1');
            $infosClient->execute([':idCompte' => $id]);
            $client = $infosClient->fetch(PDO::FETCH_ASSOC);

            $dbh = null;
        }catch(PDOException $e){
            print "Erreur : " . $e->getMessage() . "<br/>";
        }
    ?>
    <h2>Mon compte</h2>
    <article>
        <section class="nomComplet">
            <img src="<?php echo $photo ?>" alt="Photo de profil" width="50" height="50">
            <h3><?php echo $client['nomCompte'] ?></h3>
            <h3><?php echo $client['prenomCompte'] ?></h3>
        </section>
        <h3><?php echo $client['numeroTelephone'] ?></h3>
        <h3><?php echo $client['adresseMail'] ?></h3>
    </article>
    <article>
        <form action="modifier_client.php"><input type="submit" value="Modifier"></form>
        <form action="index.php"><input type="submit" value="Se déconnecter"></form>
    </article>
</body>
</html>
