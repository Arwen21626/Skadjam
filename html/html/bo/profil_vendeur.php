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

//information compte
$stmt = $dbh->prepare("SELECT nom_compte, prenom_compte, adresse_mail, numero_telephone FROM sae3_skadjam._compte WHERE id_compte = ?");
$stmt->execute([$idCompte]);
$compte = $stmt->fetch(PDO::FETCH_ASSOC);
$nom = $compte["nom_compte"];
$prenom = $compte["prenom_compte"];
$mail = $compte["adresse_mail"];
$tel = $compte["numero_telephone"];

//information vendeur
$stmt = $dbh->prepare("SELECT raison_sociale, siren, description_vendeur FROM sae_skadjam._vendeur where id_compte = ?");
$stmt->execute([$idCompte]);
$vendeur = $stmt->fetch(PDO::FETCH_ASSOC);
$denom = $vendeur["raison_sociale"];
$siren = $vendeur["siren"];
$description = $vendeur["description_vendeur"];

//info adresse
$stmt = $dbh->prepare("SELECT adresse_postale, complement_adresse, numero_rue, code_postal")



?>
<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . "/../../php/structure/head_back.php" ?>
<head>
    <title>Profil</title>
</head>
<body>
    <?php 
    require_once __DIR__ . "/../../php/structure/header_back.php";
    require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>
    <main>
        <h2>Profil</h2>
        <form method="POST" enctype="multipart/form-data">
            <div>
                <div class=" flex flex-col items-center">
                    <?php 
                    $stmt = $dbh->prepare("SELECT url_photo, alt, titre FROM sae3_skadjam._presente pr inner join sae3_skadjam._photo ph on pr.id_photo = ph.id_photo where id_vendeur = ?");
                    $stmt->execute([$idCompte]);
                    $tabPhoto = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($tabPhoto){ 
                        $photo = $tabPhoto[1];
                        ?>
                        <img class="border-2 border-solid rounded-2xl border-beige mb-3" src="<?= "../../" .  $photo["url_photo"] ?>" alt="<?= $photo["alt"] ?>" title="<?= $photo["titre"] ?>" width="390px" height="390px">
                    <?php  
                    }else{

                    }
                    ?>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg image/webp" hidden>
                    <label class="cursor-pointer w-85 rounded-2xl bg-beige p-2"  for="image">Ajouter une image</label>
                </div>
                <div>
                    <div>
                        <p>Entreprise :</p>
                        <p></p>
                    </div>
                </div>
            </div>
        </form>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
</html>