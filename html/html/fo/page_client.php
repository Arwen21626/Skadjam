<?php
    session_start();
    // à retirer
    $_SESSION["idCompte"] = 1;
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
    <?php require "../../php/structure/header_front.php"; ?>
    <?php require "../../php/structure/navbar_front.php"; ?>
    <main>
        <?php
            // Connexion à la session
            $id = (int) $_SESSION["idCompte"];

            try{
                $dbh = new PDO("$driver:host=$server;dbname=$dbname",$user,$pass);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Récupérer toutes les infos du client
                foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c INNER JOIN sae3_skadjam._client cli ON c.id_compte = cli.id_compte WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $client){
                    $nom = $client['nom_compte'];
                    $prenom = $client['prenom_compte'];
                    $pseudo = $client['pseudo'];
                    $mail = $client['adresse_mail'];
                    $naissance = $client['date_naissance'];
                    $telephone = $client['numero_telephone'];
                }

                $dbh = null;
            }catch(PDOException $e){
                print "Erreur : " . $e->getMessage() . "<br/>";
            }
        ?>
        <h2>Mon compte</h2>
        <article>
            <section class="nomComplet">
                <h3><?php echo $pseudo ?></h3>
                <p><?php echo $prenom ?></p>
                <p><?php echo $nom ?></p>
            </section>
            <p><?php echo $naissance ?></p>
            <p><?php echo $telephone ?></p>
            <p><?php echo $mail ?></p>
        </article>
        <article>
            <form action="modifier_client.php"><input type="submit" value="Modifier"></form>
            <form action="index.php"><input type="submit" value="Se déconnecter"></form>
        </article>
    </main>
    <?php require "../../php/structure/footer_front.php"; ?>
</body>
</html>
