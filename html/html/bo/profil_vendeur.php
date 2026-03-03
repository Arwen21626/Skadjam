<?php
    session_start();
    include __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ . "/../../01_premiere_connexion.php";
    include __DIR__ . '/../../php/modification_variable.php';
    include __DIR__ . '/../../php/verification_formulaire.php';

    // Vérifie si le bouton 'Se déconnecter à été appuyé'
    if (isset($_POST['logout'])) {
        // Supprime toutes les variables de session
        session_unset();

        // Détruit la session
        session_destroy();

        // Redirection vers la page principale
        header("Location: ../../index.php");
        exit();
    }

    // Vérifier si le client est connecter
    if(isset($_SESSION["idCompte"])) {
        // Connexion à la session
        $idVendeur = $_SESSION["idCompte"];

        try{
            $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            // Récupérer toutes les infos du client
            foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON c.id_compte = v.id_compte
                                    WHERE c.id_compte = $idVendeur", PDO::FETCH_ASSOC) as $vendeur){
                // Infos compte
                $nom = $vendeur['nom_compte'];
                $prenom = $vendeur['prenom_compte'];
                $mail = $vendeur['adresse_mail'];
                $tel = $vendeur['numero_telephone'];
                $tel = "0" . substr($tel, 3);

                // Infos vendeur
                $denom = $vendeur["raison_sociale"];
                $siren = $vendeur["siren"];
                $description = $vendeur["description_vendeur"];
            }
            // Infos photo
            $tabPhoto = null;

            $reqPhoto = $dbh->prepare("SELECT ph.url_photo, ph.alt, ph.titre
                                        FROM sae3_skadjam._presente pr
                                        INNER JOIN sae3_skadjam._photo ph
                                            ON pr.id_photo = ph.id_photo
                                        WHERE pr.id_vendeur = $idVendeur");
            $reqPhoto->execute();
            $tabPhoto = $reqPhoto->fetch();

            // Infos adresse
            foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._habite h
                                    ON c.id_compte = h.id_compte
                                INNER JOIN sae3_skadjam._adresse a
                                    ON h.id_adresse = a.id_adresse
                                WHERE c.id_compte = $idVendeur", PDO::FETCH_ASSOC) as $adresseData){
                $adresse = $adresseData["adresse_postale"];
                $num = $adresseData["numero_rue"];
                $numBis = $adresseData["complement_adresse"];
                $cp = $adresseData["code_postal"];
                $ville = $adresseData["ville"];
            }
    }catch(PDOException $e){
        echo "Erreur : " . $e->getMessage();
        exit;
    } 
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . "/../../php/structure/head_back.php" ?>
<head>
    <title>Profil</title>
</head>
<body>
    <?php 
    require_once __DIR__ . "/../../php/structure/header_back.php";
    require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>
    <main class="relative flex flex-col items-center">
        <h2 class="m-8">Mon Profil</h2>
        <div class="flex flex-row items-center justify-between">
            <div class=" flex flex-col w-fit">
                <?php if ($tabPhoto && !empty($tabPhoto['url_photo'])) { ?>
                    <div class="container-image relative flex items-center justify-center w-80 border-4 border-solid rounded-2xl border-beige mb-3">
                        <img class="image-vendeur w-80 rounded-xl" src="<?= '../..' . htmlspecialchars($tabPhoto['url_photo']) ?>" alt="<?= htmlspecialchars($tabPhoto['alt']) ?>" title="<?= htmlspecialchars($tabPhoto['alt']) ?>">
                    </div>
                <?php } else { ?>
                    <div class="container-image vide relative flex items-center justify-center w-80 h-80 mb-3 bg-beige rounded-2xl">
                        <img class="image-vendeur w-80 rounded-2xl" src="../../images/logo/bootstrap_icon/image.svg" alt="aucune image" title="aucune image">
                    </div>
                <?php } ?>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp" hidden>
            </div>

            <div class="mt-5 w-1/3">
                <div class="py-4">
                    <p class="w-80"><p class="text-left">Entreprise :</p>
                    <h4 class="attribut-text ml-7"><?php echo htmlentities($denom); ?></h4>
                </div>

                <div class="py-4">
                    <p class="w-80">Adresse du siège social :</p>
                    <h4 class="attribut-text ml-7"><?= "$num $numBis $adresse, $ville, $cp" ?></h4>
                </div>

                <div class="py-4">
                    <p class="w-80">Numéro SIREN :</p>
                    <h4 class="attribut-text ml-7"><?= $siren ?></h4>
                </div>
            </div>
        </div>
        <div class="flex flex-row items-center justify-between mt-8">
            <div class="w-1/3">
                <h3 class="mb-2">Propriétaire</h3>
                <div class="mb-3 modif-attribut">
                    <p class="w-80">Nom :</p>
                    <h4 class="attribut-text ml-7"><?= $nom ?></h4>
                </div>

                <div class="mb-3 modif-attribut">
                    <p class="w-80">Prénom :</p>
                    <h4 class="attribut-text ml-7"><?= $prenom ?></h4>
                </div>
            </div>
            <div class="w-1/3">
                <h3 class="mb-2">Contact</h3>
                <div class="mb-3 modif-attribut">
                    <p class="w-80">Numéro de téléphone :</p>
                    <h4 class="attribut-text ml-7"><?= $tel ?></h4>
                </div>

                <div class="mb-3 modif-attribut">
                    <p class="w-80">E-Mail :</p>
                    <h4 class="attribut-text ml-7"><?= $mail ?></h4>
                </div>
            </div>
        </div>
        <div class="description mt-8 mb-20 modif-attribut flex flex-col items-center">
            <h3 class="mb-2">Description :</h3>
            <p class="attribut-text mt-4"><?= $description != '' ? $description : 'Aucune description.'; ?></p>
        </div>
        <div class="flex flex-row justify-around items-center mt-7 mb-15">
                <!-- Modifier les informations du vendeur (sauf le mot de passe) -->
                <form action="modifier_compte_vendeur.php" method="post">
                    <input class="cursor-pointer border-4 rounded-xl p-2 m-1 border-beige w-75" type="submit" value="Modifier mes informations">
                </form>

                <!-- Modifier le mot de passe du vendeur -->
                <form action="nouveau_mdp.php">
                    <?php $_SESSION['adresse_mail'] = $mail; ?>
                    <input class="cursor-pointer border-4 rounded-xl p-2 m-1 border-beige w-75" type="submit" value="Modifier mon mot de passe">    
                </form>

                <!-- Déconnexion -->
                <form action="profil_vendeur.php" method="post">
                    <input type="hidden" id="logout" name="logout" value="true">
                    <input class="cursor-pointer border-4 rounded-xl p-2 m-1 border-beige w-75" type="submit" value="Se déconnecter">
                </form>
            </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>

<script src="../../js/bo/profil_vendeur.js" defer></script>

</html>
<?php }else{ header("Location:../../index.php"); } ?>