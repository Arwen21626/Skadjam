<?php
session_start();
include __DIR__ . "/../../01_premiere_connexion.php";
include(__DIR__ . '/../../php/modification_variable.php');
include(__DIR__ . '/../../php/verification_formulaire.php');

if (isset($_SESSION["role"]) && $_SESSION["role"] == "vendeur"){
$idCompte = $_SESSION["idCompte"];  
if (!isset($denom)) {
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
}


try {
    //information compte
    $stmt = $dbh->prepare("SELECT nom_compte, prenom_compte, adresse_mail, numero_telephone FROM sae3_skadjam._compte WHERE id_compte = ?");
    $stmt->execute([$idCompte]);
    $compte = $stmt->fetch(PDO::FETCH_ASSOC);
    $nom = $compte["nom_compte"];
    $prenom = $compte["prenom_compte"];
    $mail = $compte["adresse_mail"];
    $tel = $compte["numero_telephone"];
    $tel = "0" . substr($tel, 3);
    
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

    //image vendeur
    $stmt = $dbh->prepare("SELECT ph.id_photo, url_photo, alt, titre FROM sae3_skadjam._presente pr inner join sae3_skadjam._photo ph on pr.id_photo = ph.id_photo where id_vendeur = ? ORDER BY ph.id_photo");
    $stmt->execute([$idCompte]);
    $tabPhoto = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $tabPhoto = $tabPhoto ? $tabPhoto[0] : null;


} catch (PDOException $e) {
    echo "Erreur requete : " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["deconnexion"])){
        session_unset();
        session_destroy();
        header("Location:../../index.php");
        exit();
    }else{

        // Traitement du formulaire de modification du profil vendeur
        // Récupération des données du formulaire
        $temps  = time();
    
    
        $newDenom = $_POST['denom'];
        $newSiren = $_POST['siren'];
        $newDescription = $_POST['description'];
        $newNom = $_POST['nom'];
        $newPrenom = $_POST['prenom'];
        $newTel = $_POST['tel'];
        $newMail = $_POST['mail'];
        $newAdresse = modifierSiegeSocial($_POST['adresse']);
        $newVille = $newAdresse['ville'];
        $newCp = $newAdresse['cp'];
        $newAdresse = $newAdresse['adresse'];
        $image = $_FILES['image'];
        $imageSupprimee = ($_POST['imageSupprimee']==="true")? true : false ; // 'true' ou 'false'
     
        if ($image['size'] === 0){
            $image = null;
        }else{
            $urlPhoto = "images/images_vendeur/" . $temps;
            $imageAlt = explode(".",$image['name'])[0];
            $imageTitre = explode(".",$image['name'])[0];
        }
    
        $erreurs = [];
        // Validation des données
        /* NOM */
        if (!verifNomPrenom($newNom)) $erreurs["nom"] = "Lettre majuscule ou minuscule seulement";
    
        /* PRENOM */
        if (!verifNomPrenom($newPrenom)) $erreurs["prenom"] = "Lettre majuscule ou minuscule seulement";
    
        /* MAIL */
        if (!verifMail($newMail)) $erreurs["mail"] = "Format incorrecte";
    
        /* TEL */
        if (!verifTelephone($newTel)) $erreurs["tel"] = "Numéro à 10 chiffres";
    
        /* RS */
        if (!verifDenomination($newDenom)) $erreurs["denomination"] = "Autorisé majuscules, minuscules et chiffres";
    
        /* SIREN */
        if (!verifSiren($newSiren)) $erreurs["siren"] = "Numéro SIREN invalide";
    
        /* ##### ADRESSE ##### */
        if (!verifCp($newCp)) $erreurs["cp"] = "Code postale invalide";
    
        if (!verifVille($newVille)) $erreurs["ville"] = "Format ville incorrect";
    
        if (!verifAdresse($newAdresse)) $erreurs["adresse"] = "Format de l'adresse invalide";
        
        // Vérifier la taille
        if ($image) {
            if ($image['error'] !== UPLOAD_ERR_OK) $erreurs['imageTelechargement'] = "Erreur lors du téléchargement de l'image.";
            
            $formatAutorise = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($image['type'], $formatAutorise))  $erreurs['imageFormat'] = "Format d'image non autorisé. Seuls les formats JPEG, PNG et WEBP sont autorisés.";
            
            if ($image['size'] > 5 * 1024 * 1024) $erreurs['imageTaille'] = "L'image dépasse la taille maximale de 5 Mo.";
    
        }
    
        $temp = tabAdresse($newAdresse);
        $newNumero = $temp[0];
        $newCompNum = $temp[1];
        $newAdresse = $temp[2];
    
        // Mettre à jour la base de données avec les nouvelles valeurs
        if (empty($erreurs)) {
        
    
            try {
                $dbh->beginTransaction();
    
                // Mettre à jour les informations du compte
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._compte SET nom_compte = ?, prenom_compte = ?, adresse_mail = ?, numero_telephone = ? WHERE id_compte = ?");
                $stmt->execute([$newNom, $newPrenom, $newMail, formatTel($newTel), $idCompte]);
        
                // Mettre à jour les informations du vendeur
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._vendeur SET raison_sociale = ?, siren = ?, description_vendeur = ? WHERE id_compte = ?");
                $stmt->execute([$newDenom, $newSiren, $newDescription, $idCompte]);
        
                // Mettre à jour l'adresse 
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._adresse AS a SET adresse_postale = ?, complement_adresse = ?, numero_rue = ?, code_postal = ?, ville = ? FROM sae3_skadjam._habite AS h WHERE a.id_adresse = h.id_adresse AND h.id_compte = ?");
                $stmt->execute([$newAdresse, $newCompNum, $newNumero, $newCp, $newVille, $idCompte]);
    
                //si l'image est supprimee
                if ($imageSupprimee){
    
                    //supprimer le lien entre l'image et le compte
                    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._presente WHERE id_photo = ?");
                    $stmt->execute([$tabPhoto['id_photo']]);
    
                    //supprimer l'image
                    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._photo where id_photo = ?");
                    $stmt->execute([$tabPhoto['id_photo']]);
                }else{
                    if ($image){
                        move_uploaded_file($image['tmp_name'], __DIR__ . "/../../" . $urlPhoto);
                        if ($tabPhoto) {
                            // Mettre à jour la photo existante
                            $stmt = $dbh->prepare("UPDATE sae3_skadjam._photo SET url_photo = ?, alt = ?, titre = ? WHERE id_photo = ?");
                            $stmt->execute([$urlPhoto, $imageAlt, $imageTitre, $tabPhoto['id_photo']]);
    
                        }else {
                            // Insérer une nouvelle photo
                            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._photo (url_photo, alt, titre) VALUES (?, ?, ?) RETURNING id_photo");
                            $stmt->execute([$urlPhoto, $imageAlt, $imageTitre]);
                            $newPhotoId = $stmt->fetchColumn();
            
                            // Lier la nouvelle photo au vendeur
                            $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._presente (id_vendeur, id_photo) VALUES (?, ?)");
                            $stmt->execute([$idCompte, $newPhotoId]);
                        }
                    }
                }
    
                
    
                $dbh->commit();
    
                // Rediriger ou afficher un message de succès
                header("Location: profil_vendeur.php");
                exit;
            } catch (PDOException $e) {
                echo "Erreur lors de la mise à jour : " . $e->getMessage();
                exit;
            }
        }
    }

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
        <h2 class="m-8">Profil</h2>
        <form method="POST" id="form-profil-vendeur" enctype="multipart/form-data" class=" w-2/3 @max-[768px]:w-7/8">
            <div class="flex flex-row items-center justify-between">
                <div class=" flex flex-col w-fit">
                    <?php 
                    if ($tabPhoto){ 
                        $photo = $tabPhoto;
                        ?>
                        <div class="container-image relative flex items-center justify-center w-80 border-4 border-solid rounded-2xl border-beige mb-3">
                            <img class="image-vendeur w-80 rounded-2xl" src="<?= "../../" .  $photo["url_photo"] ?>" alt="<?= $photo["alt"] ?>" title="<?= $photo["titre"] ?>">

                            <button type="button" class="bouton-poubelle group/poubelle cursor-pointer ml-4 float-right absolute top-2 right-2 bg-beige rounded-sm p-1">
                                <img src="../../images/logo/bootstrap_icon/trash.svg" alt="supprimer-image" title="supprimer-image" class=" w-8! h-8! block group-hover/poubelle:hidden">
                                <img src="../../images/logo/bootstrap_icon/trash-fill.svg" alt="supprimer-image" title="supprimer-image" class=" w-8! h-8! hidden group-hover/poubelle:block">
                            </button>
                                    
                        </div>

                    <?php  
                    }else{?>
                        <div class="container-image vide relative flex items-center justify-center w-80 h-80 mb-3 bg-beige rounded-2xl">
                            <img class="image-vendeur w-80 rounded-2xl" src="../../images/logo/bootstrap_icon/image.svg" alt="aucune image" title="aucune image">
                            <button type="button" class="bouton-poubelle group/poubelle cursor-pointer ml-4 float-right absolute top-2 right-2 bg-beige rounded-sm p-1 hidden">
                                <img src="../../images/logo/bootstrap_icon/trash.svg" alt="supprimer-image" title="supprimer-image" class=" w-8! h-8! block group-hover/poubelle:hidden">
                                <img src="../../images/logo/bootstrap_icon/trash-fill.svg" alt="supprimer-image" title="supprimer-image" class=" w-8! h-8! hidden group-hover/poubelle:block">
                            </button>
                        
                        </div>

                        
                    <?php } ?>

                    <input type="file" id="image" name="image" accept="image/png, image/jpeg, image/webp" hidden>
                    
                    <?php
                    if ($tabPhoto){ ?>
                        <label class="label-image cursor-pointer w-80 rounded-2xl  bg-beige p-2 text-center"  for="image">Modifier l'image</label>
                    <?php
                    } else { ?>
                        <label class="label-image cursor-pointer w-80 rounded-2xl bg-beige p-2 text-center"  for="image">Ajouter une image</label>
                    <?php
                    }
                    
                    ?>
                    <button type="button" class="bouton-annuler-image <?= ($tabPhoto) ? "image-modifie" : "image-ajoute" ?> hidden cursor-pointer w-80 rounded-2xl bg-beige p-2 text-center mt-2">Annuler la modification</button>
                </div>

                <div class=" mt-5 w-1/3">
                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Entreprise :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2 "><?= $denom ?></p>
                        <input type="text" name="denom" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $denom ?>">
                        <?= (isset($erreurs["denomination"])) ? "<p class=\"text-rouge\">" . $erreurs["denomination"] . " </p>" : '' ?>
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Adresse du siège social :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= "$num $numBis $adresse, $ville, $cp" ?></p>
                        <input type="text" name="adresse" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= "$num $numBis $adresse, $ville, $cp" ?>" placeholder="XX [bis] rue camélia, Paris, 75011">
                        <?= (isset($erreurs["adresse"])) ? "<p class=\"text-rouge\">" . $erreurs["adresse"] . " </p>" : '' ?>
                        <?= (isset($erreurs["ville"])) ? "<p class=\"text-rouge\">" . $erreurs["ville"] . " </p>" : '' ?>
                        <?= (isset($erreurs["cp"])) ? "<p class=\"text-rouge\">" . $erreurs["cp"] . " </p>" : '' ?>
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Numéro SIREN :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $siren ?></p>
                        <input type="text" name="siren" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $siren ?>">
                        <?= (isset($erreurs["siren"])) ? "<p class=\"text-rouge\">" . $erreurs["siren"] . " </p>" : '' ?>
                    </div>

                </div>
            </div>
            <div class="flex flex-row items-center justify-between mt-8">
                <div class=" w-1/3">
                    <h3 class=" mb-2">Propriétaire</h3>
                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Nom :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $nom ?></p>
                        <input type="text" name="nom" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $nom ?>">
                        <?= (isset($erreurs["nom"])) ? "<p class=\"text-rouge\">" . $erreurs["nom"] . " </p>" : '' ?>
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Prénom :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $prenom ?></p>
                        <input type="text" name="prenom" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $prenom ?>">
                        <?= (isset($erreurs["prenom"])) ? "<p class=\"text-rouge\">" . $erreurs["prenom"] . " </p>" : '' ?>
                    </div>

                </div>
                <div class=" w-1/3">
                    <h3 class=" mb-2">Contact</h3>
                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Numéro de téléphone :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $tel ?></p>
                        <input type="text" name="tel" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $tel ?>">
                        <?= (isset($erreurs["tel"])) ? "<p class=\"text-rouge\">" . $erreurs["tel"] . " </p>" : '' ?>
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">E-Mail :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $mail ?></p>
                        <input type="text" name="mail" class="champ-text w-full ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3" value="<?= $mail ?>">
                        <?= (isset($erreurs["mail"])) ? "<p class=\"text-rouge\">" . $erreurs["mail"] . " </p>" : '' ?>
                    </div>

                </div>
            </div>
            <div class="description mt-8 mb-20 modif-attribut">
                <div class=" flex flex-row items-center">
                    <h3 class=" mb-2">Description :</h3>
                    <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                </div>
                <p class="attribut-text ml-7"><?= $description ?></p>
                <textarea name="description" class="textarea champ-text ml-5 hidden border-4 border-solid rounded-2xl p-1 border-beige pl-3 w-full h-40"><?= $description ?></textarea>
            </div>
            <div class="flex flex-row justify-around mt-8 mb-8 @max-[768px]:flex-col @max-[768px]:items-center">
                <a href="profil_vendeur.php" class="cursor-pointer text-center block w-64 border-4 border-solid rounded-2xl border-beige p-1 pl-3">Annuler</a>
                <input type="submit" value="Valider" class="cursor-pointer w-64 border-4 border-solid rounded-2xl p-1 border-beige pl-3 @max-[768px]:mt-2" id="valider">
            </div>
        </form>
        <div class=" absolute top-5 right-7 flex flex-col items-stretch w-fit">
            <form method="post" >
                <input type="submit" name="deconnexion" id="deconnexion" value="Se déconnecter" class="cursor-pointer w-full border-4 border-solid rounded-2xl p-1 border-beige mb-4">
            </form>
            <a href="nouveau_mdp.php">
                <button class="cursor-pointer border-4 border-solid rounded-2xl p-1 border-beige pl-3 pr-3">
                    Modifier le mot de passe
                </button>

            </a>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>

<script src="../../js/bo/profil_vendeur.js" defer></script>

</html>
<?php }else{
    header("Location:../../index.php");
    }
    ?>