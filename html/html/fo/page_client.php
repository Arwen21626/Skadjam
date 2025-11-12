<?php
    session_start();
    require_once "../../connections_params.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css">
    <title>Mon compte</title>
</head>
<body>
    <?php require "../../php/structure/header_front.php" ?>
    <?php require "../../php/structure/navbar_front.php"; ?>
    <main>
        <?php
            // Connexion à la session
            $id = (int) $_SESSION['id_compte'];

            try{
                $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Récupérer toutes les infos du client
                $infosClient = $dbh->prepare('SELECT id_compte, nom_compte, prenom_compte, adresse_mail, numero_telephone FROM sae3_skadjam._compte WHERE id_compte = :id_compte LIMIT 1');
                $infosClient->execute([':id_compte' => $id]);
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
    </main>
    <?php require "../../php/structure/footer_front.php" ?>
</body>
</html>
