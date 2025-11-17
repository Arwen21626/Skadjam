<?php
session_start();
include __DIR__ . "/../../01_premiere_connexion.php";
//$idCompte = $_SESSION["idCompte"];
$idCompte = 1;

$denom = "";
$siren = "";
$description = "";

$adresse = "";
$num = "";
$numBis = "";
$ville = "";
$cp = "";


$nom = "";
$prenom = "";
$tel = "";
$mail = "";

try {
    //information compte
    $stmt = $dbh->prepare("SELECT nom_compte, prenom_compte, adresse_mail, numero_telephone FROM sae3_skadjam._compte WHERE id_compte = ?");
    $stmt->execute([$idCompte]);
    $compte = $stmt->fetch(PDO::FETCH_ASSOC);
    $nom = $compte["nom_compte"];
    $prenom = $compte["prenom_compte"];
    $mail = $compte["adresse_mail"];
    $tel = $compte["numero_telephone"];
    
    //information vendeur
    $stmt = $dbh->prepare("SELECT raison_sociale, siren, description_vendeur FROM sae3_skadjam._vendeur where id_compte = ?");
    $stmt->execute([$idCompte]);
    $vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
    $denom = $vendeur["raison_sociale"];
    $siren = $vendeur["siren"];
    $description = isset($vendeur["description_vendeur"]) ? $vendeur["description_vendeur"] : "Aucune description.";
    
    //info adresse
    $stmt = $dbh->prepare("SELECT adresse_postale, complement_adresse, numero_rue, code_postal, ville FROM sae3_skadjam._adresse a INNER JOIN sae3_skadjam._habite h ON a.id_adresse = h.id_adresse WHERE id_compte = ?");
    $stmt->execute([$idCompte]);
    $adresseData = $stmt->fetch(PDO::FETCH_ASSOC);
    $adresse = $adresseData["adresse_postale"];
    $num = $adresseData["numero_rue"];
    $numBis = $adresseData["complement_adresse"];
    $cp = $adresseData["code_postal"];
    $ville = $adresseData["ville"];
} catch (PDOException $e) {
    echo "Erreur requete : " . $e->getMessage();
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
    <main class=" flex flex-col items-center">
        <h2 class="m-8">Profil</h2>
        <form method="POST" enctype="multipart/form-data" class=" w-2/3 @max-[768px]:w-7/8">
            <div class="flex flex-row justify-between">
                <div class=" flex flex-col w-fit">
                    <?php 
                    $stmt = $dbh->prepare("SELECT url_photo, alt, titre FROM sae3_skadjam._presente pr inner join sae3_skadjam._photo ph on pr.id_photo = ph.id_photo where id_vendeur = ?");
                    $stmt->execute([$idCompte]);
                    $tabPhoto = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($tabPhoto){ 
                        $photo = $tabPhoto[1];
                        ?>
                        <img class=" w-80 border-2 border-solid rounded-2xl border-beige mb-3 @max-[768px]:" src="<?= "../../" .  $photo["url_photo"] ?>" alt="<?= $photo["alt"] ?>" title="<?= $photo["titre"] ?>" width="390px" height="390px">
                    <?php  
                    }else{ ?>
                    
                    <img class="mb-3" src="../../images/logo/bootstrap_icon/image.svg" alt="aucune image" title="aucune image" width="390px" height="390px">
                    
                    <?php }
                    ?>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg image/webp" hidden>
                    <label class="cursor-pointer w-80 rounded-2xl bg-beige p-2 text-center"  for="image">Ajouter une image</label>
                </div>
                <div class=" mt-5">
                    <div class=" mb-3">
                        <p class="underline">Entreprise :</p>
                        <p class=" ml-7 mt-2"><?= $denom ?></p>
                    </div>
                    <div class=" mb-3">
                        <p class="underline">Adresse du siège social :</p>
                        <p class=" ml-7 mt-2"><?= "$num $numBis $adresse, $ville, $cp" ?></p>
                    </div>
                    <div class=" mb-3">
                        <p class="underline">Numéro SIREN :</p>
                        <p class=" ml-7 mt-2"><?= $siren ?></p>
                    </div>
                </div>
            </div>
            <div class="flex flex-row justify-between mt-8">
                <div>
                    <h3>Propriétaire</h3>
                    <div class="mb-3">
                        <p class="underline">Nom :</p>
                        <p class=" ml-7 mt-2"><?= $nom ?></p>
                    </div class="mb-3">
                    <div>
                        <p class="underline">Prénom :</p>
                        <p class=" ml-7 mt-2"><?= $prenom ?></p>
                    </div>
                </div>
                <div>
                    <h3>Contact</h3>
                    <div class="mb-3">
                        <p class="underline">Numéro de téléphone :</p>
                        <p class=" ml-7 mt-2"><?= $tel ?></p>
                    </div>
                    <div class="mb-3">
                        <p class="underline">E-Mail :</p>
                        <p class=" ml-7 mt-2"><?= $mail ?></p>
                    </div>
                </div>
            </div>
            <div class=" mt-8 mb-20">
                <h3>Description :</h3>
                <p class=" ml-7 mt-2"><?= $description ?></p>
            </div>
        </form>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
</html>