<?php
session_start();
require_once __DIR__ . "/../../01_premiere_connexion.php";

// Vérifie si le bouton 'Se déconnecter à été appuyé'
if (isset($_POST['logout'])) {
    // Supprime toutes les variables de session
    session_unset();

    // Détruit la session
    session_destroy();

    // Redirection vers la page principale
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Mon profil</title>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main>
        <?php
        // Vérifier si le client est connecter
        if(isset($_SESSION["idCompte"])) {
            // Connexion à la session
            $id = (int) $_SESSION["idCompte"];

            try{
                $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
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
                    $numRue[$nbAdresse] = $adresse['numero_rue'];
                    $adressePostale[$nbAdresse] = $adresse['adresse_postale'];
                    $batiment[$nbAdresse] = " " . $adresse['numero_bat'];
                    $appartement[$nbAdresse] = " " . $adresse['numero_appart'];
                    $codePostal[$nbAdresse] = $adresse['code_postal'];
                    $ville[$nbAdresse] = $adresse['ville'];
                    $nbAdresse++;
                }
                $dbh = null;
            }catch(PDOException $e){
                echo "Erreur : " . $e->getMessage();
            }
            ?>
            <h2 class="flex justify-center text-center">Mon profil</h2>
            <section>
                <!-- Affichage des informations du client (sauf son mot de passe) -->
                <div class="flex flex-row">
                    <h2 class="mt-1 mb-1 mr-4 ml-0"><?php echo $pseudo; ?></h2>
                    <h3 class="mt-1 mb-1 mr-4 ml-0 relative top-4 text-vertFonce -z-1"><?php echo $prenom; ?> <?php echo $nom; ?></h3>
                </div>
                <div>
                    <p class="m-4"><?php echo $naissance; ?></p>
                    <?php for ($i=0; $i < $nbAdresse; $i++) { // Affiche toutes les adresses du client ?>
                        <p class="mt-2 mb-2 mr-4 ml-4"><?php echo "$numRue[$i] $adressePostale[$i]$batiment[$i]$appartement[$i], $codePostal[$i] $ville[$i]"; ?></p>
                    <?php } ?>
                    <p class="m-4"><?php echo $telephone; ?></p>
                    <p class="m-4"><?php echo $mail; ?></p>
                </div>
            </section>
            <article class="flex flex-row justify-around mb-5">
                <!-- Modifier les informations du client (sauf le mot de passe) -->
                <form action="modifier_compte_client.php" method="post">
                    <input class="border-2 border-vertClair rounded-xl p-2" type="submit" value="Modifier mes informations">
                </form>

                <!-- Modifier le mot de passe du client -->
                <form action="nouveau_mdp.php">
                    <?php $_SESSION['adresse_mail'] = $mail; ?>
                    <input class="border-2 border-vertClair rounded-xl p-2" type="submit" value="Modifier mon mot de passe">    
                </form>

                <!-- Déconnexion -->
                <form action="profil_client.php" method="post">
                    <input type="hidden" id="logout" name="logout" value="true">
                    <input class="border-2 border-vertClair rounded-xl p-2" type="submit" value="Se déconnecter">
                </form>
            </article>
        <?php
        }else{
            // Si non connecté, l'emmener à la page de connexion à la place
            header("Location: connexion.php");
        }
        ?>
    </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
