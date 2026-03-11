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
                $latitude = $adresseData["latitude"];
                $longitude = $adresseData["longitude"];
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
</head>
<body>

    <?php 
        require_once __DIR__ . "/../../php/structure/header_back.php";
        require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>

    <main class="flex flex-col self-center w-9/10 mx-auto space-y-10">
        <h2 class="m-8">Mon Profil</h2>
        <div class="flex flex-row justify-around space-x-40">
            <!-- Photo de profil -->
            <div class=" flex flex-col">
                <?php if ($tabPhoto && !empty($tabPhoto['url_photo'])) { ?>
                    <div class="container-image flex items-center justify-center w-80 border-4 border-solid rounded-2xl border-beige mb-3">
                        <img class="image-vendeur w-80 rounded-xl" src="<?= '../..' . htmlspecialchars($tabPhoto['url_photo']) ?>" alt="Photo de profil" title="<?= htmlspecialchars($tabPhoto['alt']) ?>">
                    </div>
                <?php } else { ?>
                    <div class="container-image vide flex items-center justify-center w-80 h-80 mb-3 bg-beige rounded-2xl">
                        <img class="image-vendeur w-60 rounded-2xl" src="../../images/logo/bootstrap_icon/image.svg" alt="aucune image" title="aucune image">
                    </div>
                <?php } ?>
                <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp" hidden>
            </div>
            <!-- Carte -->
            <div id="map" class=" w-5/10 h-80 z-0"></div>
        </div>
        <div class="flex flex-row self-center space-x-40 justify-between w-9/10">
            <!-- Infos proprio -->
            <div class="flex flex-col space-y-5 ">
                <h3>Propriétaire</h3>
                <div class="flex flex-col space-y-5">
                    <!-- Nom -->
                    <div class="flex flex-row space-x-5">
                        <p class="font-bold">Nom :</p>
                        <p><?= $nom ?></p>
                    </div>
                    <!-- Prénom -->
                    <div class="flex flex-row space-x-5">
                        <p class="font-bold">Prénom :</p>
                        <p><?= $prenom ?></p>
                    </div>
                </div>
            </div>

            <!-- Infos entreprise -->
            <div class="flex flex-col space-y-5">
                <h3>Entreprise</h3>
                <div class="flex flex-col space-y-5">
                    <!-- Nom -->
                    <div class="flex flex-row space-x-5">
                        <p class="font-bold">Nom :</p>
                        <p><?php echo $denom; ?></p>
                    </div>
                    <!-- Adresse -->
                    <div class="flex flex-col space-x-5">
                        <p class="font-bold">Adresse du siège social :</p>
                        <p><?= "$num $numBis $adresse, $ville, $cp" ?></p>
                    </div>
                </div>
            </div>

            <!-- Infos contact -->
            <div class="flex flex-col space-y-5">
                <h3>Contact</h3>
                <div class="flex flex-col space-y-5">
                    <!-- Téléphone -->
                    <div class="flex flex-col space-x-5">
                        <p class="font-bold">Numéro de téléphone :</p>
                        <p><?= $tel ?></p>
                    </div>
                    <!-- Mail -->
                    <div class="flex flex-col space-x-5">
                        <p class="font-bold">E-Mail :</p>
                        <p><?= $mail ?></p>
                    </div>
                </div>
            </div>
        </div>
    
        
        <!-- Description -->
        <div>
            <h3 class="font-bold text-center">Description :</h3>
            <p class="break-words"><?= $description != '' ? $description : 'Aucune description.'; ?></p>
        </div>


              
        <div class="grid grid-cols-3 gap-4 mb-15">
            <!---1ère ligne de boutons---> 
            <!-- Modifier le mot de passe du vendeur -->
            <form action="nouveau_mdp.php">
                <?php $_SESSION['adresse_mail'] = $mail; ?>
                <input class="border-vertFonce border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer" type="submit" value="Modifier mon mot de passe">    
            </form>

            <!-- Modifier les informations du vendeur (sauf le mot de passe) -->
            <form action="modifier_compte_vendeur.php" method="post">
                <input class="border-vertFonce border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer" type="submit" value="Modifier mes informations">
            </form>

            <!---Statistiques--->
            <form action="statistiques.php" method="post">
                <input class="border-vertFonce border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer" type="submit" value="Mes statistiques">
            </form> 
            
            <!---2ème ligne de boutons---> 
            <!-- Supprimer le compte du vendeur -->
            <form action="suppression_vendeur.php" method="post">
                <input class="border-rouge border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer" type="submit" value="Supprimer mon compte">
            </form>

            <!---Retour--->
            <a href="index_vendeur.php">
                <button class="border-vertFonce border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer">Retour</button>
            </a>

            <!-- Déconnexion -->
            <form action="profil_vendeur.php" method="post">
                <input type="hidden" id="logout" name="logout" value="true">
                <input class="border-vertFonce border-2 rounded-2xl w-75 h-14 p-2 m-1 cursor-pointer" type="submit" value="Se déconnecter">
            </form>
        </div>

        
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
<?php $coord = [
    'latitude' => $latitude,
    'longitude' => $longitude
]?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const coord = <?php echo json_encode($coord);?>;

    var map = L.map('map').setView([coord.latitude, coord.longitude], 15);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    var pointerFonce = L.icon({
        iconUrl: '../../images/logo/pointeurVertFonce.png',
        iconSize: [45, 70],
    });

    var marker = L.marker([coord.latitude, coord.longitude], {
        icon: pointerFonce,
    })

    map.addLayer(marker)
</script>
<script src="../../js/bo/profil_vendeur.js" defer></script>

</html>
<?php }else{ header("Location:../../index.php"); } ?>