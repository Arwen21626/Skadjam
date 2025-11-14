<?php
    session_start();
    // à retirer
    $_SESSION["idCompte"] = 1;
    require_once "../../connections_params.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require "../../php/structure/head_front.php"; ?>
    <title>Mon profil</title>
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
                foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                                INNER JOIN
                                            sae3_skadjam._client cli 
                                                ON c.id_compte = cli.id_compte
                                            WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $client){
                    $nom = $client['nom_compte'];
                    $prenom = $client['prenom_compte'];
                    $pseudo = $client['pseudo'];
                    $mail = $client['adresse_mail'];
                    $naissance = $client['date_naissance'];
                    $telephone = $client['numero_telephone'];
                }
                // Récupérer les adresses du client
                $nbAdresse = 0;
                foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                                INNER JOIN
                                            sae3_skadjam._habite h
                                                ON c.id_compte = h.id_compte
                                                INNER JOIN
                                            sae3_skadjam._adresse a
                                                ON h.id_adresse = a.id_adresse
                                            WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $adresse){
                    $adressePostale[$nbAdresse] = $adresse['adresse_postale'];
                    $codePostal[$nbAdresse] = $adresse['code_postal'];
                    $ville[$nbAdresse] = $adresse['ville'];
                    $nbAdresse++;
                }
                $dbh = null;
            }catch(PDOException $e){
                print "Erreur : " . $e->getMessage() . "<br/>";
            }
        ?>
        <h2>Profil</h2>
        <section>
            <div class="flex flex-row">
                <h2 class="my-[0.1em] mr-4 ml-0"><?php echo $pseudo; ?></h2>
                <h3 class="my-[0.1em] mr-4 ml-0 relative top-[5px]"><?php echo "$prenom $nom"; ?></h3>
            </div>
            <div>
                <p class="m-4"><?php echo $naissance; ?></p>
                <?php for ($i=0; $i < $nbAdresse; $i++) { // Affiche toutes les adresses du client ?>
                <p class="mx-4 my-2"><?php echo "$adressePostale[$i], $codePostal[$i] $ville[$i]"; ?></p>
                <?php } ?>
                <p class="m-4"><?php echo $telephone; ?></p>
                <p class="m-4"><?php echo $mail; ?></p>
            </div>
        </section>
        <article class="flex flex-row justify-around">
            <form action="modifier_compte_client.php"><input type="submit" value="Modifier mes informations"></form>
            <form action="attendre_mail.php"><input type="submit" value="Modifier mon mot de passe"></form>
            <form action="index.php"><input type="submit" value="Se déconnecter"></form>
        </article>
    </main>
    <?php require "../../php/structure/footer_front.php"; ?>
</body>
</html>
