<?php
session_start();
include __DIR__ . "/../../01_premiere_connexion.php";
include(__DIR__ . '/../../php/modification_variable.php');
include(__DIR__ . '/../../php/verification_formulaire.php');
//$idCompte = $_SESSION["idCompte"];
$idCompte = 1;
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
} catch (PDOException $e) {
    echo "Erreur requete : " . $e->getMessage();
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Traitement du formulaire de modification du profil vendeur
    // Récupération des données du formulaire
    $newDenom = $_POST['denom'];
    $newSiren = $_POST['siren'];
    $newDescription = $_POST['description'];
    $newNom = $_POST['nom'];
    $newPrenom = $_POST['prenom'];
    $newTel = $_POST['tel'];
    $newMail = $_POST['mail'];
    $newAdresse = modifierSiegeSocial($_POST['adresse']);
    $newVille = $newAdresse["ville"];
    $newCp = $newAdresse["cp"];
    $newAdresse = $newAdresse["adresse"];
    
    $erreurs = [];
    // Validation des données
    /* NOM */
    if (!verifNomPrenom($newNom)) $erreurs["nom"] = "lettre majuscule ou minuscule seulement";

    /* PRENOM */
    if (!verifNomPrenom($newPrenom)) $erreurs["prenom"] = "lettre majuscule ou minuscule seulement";

    /* MAIL */
    if (!verifMail($newMail)) $erreurs["mail"] = "format incorrecte";

    /* TEL */
    if (!verifTelephone($newTel)) $erreurs["tel"] = "numéro à 10 chiffres";

    /* RS */
    if (!verifDenomination($newDenom)) $erreurs["denomination"] = "autorisé majuscules, minuscules et chiffres";

    /* SIREN */
    if (!verifSiren($newSiren)) $erreurs["siren"] = "numéro SIREN invalide";

    /* ##### ADRESSE ##### */
    if (!verifCp($newCp)) $erreurs["cp"] = "code postale invalide";

    if (!verifVille($newVille)) $erreurs["ville"] = "format ville incorrect";

    if (!verifAdresse($newAdresse)) $erreurs["adresse"] = "format de l'adresse invalide";
    
    $temp = tabAdresse($newAdresse);
    $newNumero = $temp[0];
    $newCompNum = $temp[1];
    $newAdresse = $temp[2];

    print_r("$newVille $newCp");
    print_r($temp);

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
    
            // Mettre à jour l'adresse (simplifié pour cet exemple)
            $stmt = $dbh->prepare("UPDATE sae3_skadjam._adresse AS a SET adresse_postale = ?, complement_adresse = ?, numero_rue = ?, code_postal = ?, ville = ? FROM sae3_skadjam._habite AS h WHERE a.id_adresse = h.id_adresse AND h.id_compte = ?");
            $stmt->execute([$newAdresse, $newCompNum, $newNumero, $newCp, $newVille, $idCompte]);
            // et mettre à jour la table des adresses en conséquence
            
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
            <div class="flex flex-row items-center justify-between">
                <div class=" flex flex-col w-fit">
                    <?php 
                    $stmt = $dbh->prepare("SELECT url_photo, alt, titre FROM sae3_skadjam._presente pr inner join sae3_skadjam._photo ph on pr.id_photo = ph.id_photo where id_vendeur = ?");
                    $stmt->execute([$idCompte]);
                    $tabPhoto = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    if ($tabPhoto){ 
                        $photo = $tabPhoto[1];
                        ?>
                        <img class=" w-80 border-2 border-solid rounded-2xl border-beige mb-3" src="<?= "../../" .  $photo["url_photo"] ?>" alt="<?= $photo["alt"] ?>" title="<?= $photo["titre"] ?>">
                    <?php  
                    }else{?>
                    <div class="w-80 h-80">
                        <img class="mb-3 w-80 bg-beige rounded-2xl" src="../../images/logo/bootstrap_icon/image.svg" alt="aucune image" title="aucune image">
                    </div>
                    
                    <?php }
                    ?>
                    <input type="file" id="image" name="image" accept="image/png, image/jpeg image/webp" hidden>
                    <label class="cursor-pointer w-80 rounded-2xl bg-beige p-2 text-center"  for="image">Ajouter une image</label>
                </div>
                <div class=" mt-5 w-1/3">
                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Entreprise :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $denom ?></p>
                        <input type="text" name="denom" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $denom ?>">
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Adresse du siège social :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= "$num $numBis $adresse, $ville, $cp" ?></p>
                        <input type="text" name="adresse" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= "$num $numBis $adresse, $ville, $cp" ?>" placeholder="X [bis] rue camélia, Paris, 75011">
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Numéro SIREN :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $siren ?></p>
                        <input type="text" name="siren" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $siren ?>">
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
                        <input type="text" name="nom" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $nom ?>">
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Prénom :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $prenom ?></p>
                        <input type="text" name="prenom" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $prenom ?>">
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
                        <input type="text" name="tel" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $tel ?>">
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">E-Mail :</p>
                            <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $mail ?></p>
                        <input type="text" name="mail" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $mail ?>">
                    </div>

                </div>
            </div>
            <div class=" mt-8 mb-20 modif-attribut">
                <div class=" flex flex-row items-center">
                    <h3 class=" mb-2">Description :</h3>
                    <?php include __DIR__ . "/../../php/structure/bouton_modifier_vendeur.php"; ?>
                </div>
                <p class="attribut-text ml-7"><?= $description ?></p>
                <textarea name="description" class="champ-text ml-5 hidden border-2 border-solid rounded-md border-beige pl-3 w-full h-40"><?= $description ?></textarea>
            </div>
            <div class="flex flex-row justify-around mt-8 mb-8 @max-[768px]:flex-col @max-[768px]:items-center">
                <input type="reset" value="Annuler" class="cursor-pointer w-64 border-2 border-solid rounded-md border-beige pl-3">
                <input type="submit" value="Valider" class="cursor-pointer w-64 border-2 border-solid rounded-md border-beige pl-3 @max-[768px]:mt-2" id="valider">
            </div>
        </form>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
<script>
let valider = false;
let ancienEtatValider = false;
let nbModifActive = 0;
let ancienEtatChamp = false;
let newEtatChamp = false;
let ancienTexte = "";
let texte = "";
const boutonValider = document.getElementById("valider");
boutonValider.disabled = !valider;
if (!valider){
    boutonValider.classList.add("bg-gray-400");
    boutonValider.classList.remove("bg-beige", "cursor-pointer", "hover:bg-darkbeige");
}

document.querySelectorAll(".modif-attribut .bouton-modifier").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        const boutonModifier = container.querySelector(".bouton-modifier"); // bouton modidier
        const groupeBouton = container.querySelector(".groupe-bouton"); // groupe de bouton valider/annuler
        nbModifActive += 1;
        if(!boutonValider.disabled){
            ancienEtatValider = true;
            boutonValider.disabled = true;
            boutonValider.classList.add("bg-gray-400");
            boutonValider.classList.remove("bg-beige", "cursor-pointer", "hover:bg-darkbeige");
        }

        ancienTexte = paragraph.textContent;
        
        paragraph.classList.toggle("hidden");

        champ.classList.toggle("hidden");
        champ.classList.toggle("block");

        groupeBouton.classList.toggle("hidden");
        groupeBouton.classList.toggle("flex");  
        
        boutonModifier.classList.toggle("hidden");
        boutonModifier.classList.toggle("block");
    });
});

document.querySelectorAll(".modif-attribut .bouton-valider, .modif-attribut .bouton-annuler").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        const boutonModifier = container.querySelector(".bouton-modifier"); // bouton modidier
        const groupeBouton = container.querySelector(".groupe-bouton"); // groupe de bouton valider/annuler
        
        nbModifActive -= 1;

        paragraph.classList.toggle("hidden");

        champ.classList.toggle("hidden");
        champ.classList.toggle("block");

        groupeBouton.classList.toggle("hidden");
        groupeBouton.classList.toggle("flex");  
        
        boutonModifier.classList.toggle("hidden");
        boutonModifier.classList.toggle("block");
    });
});

document.querySelectorAll(".modif-attribut .bouton-valider").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea

        if(boutonValider.disabled && newEtatChamp){
            ancienEtatChamp = newEtatChamp;
            if(nbModifActive === 0){
                boutonValider.disabled = false;
                boutonValider.classList.remove("bg-gray-400");
                boutonValider.classList.add("bg-beige", "cursor-pointer", "hover:bg-darkbeige");
            }
        }
        
        texte = champ.value;
        paragraph.textContent = texte;

    });
});
document.querySelectorAll(".modif-attribut .bouton-annuler").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        newEtatChamp = ancienEtatChamp
        if(ancienEtatValider && nbModifActive === 0){
            boutonValider.disabled = false;
            boutonValider.classList.remove("bg-gray-400");
            boutonValider.classList.add("bg-beige", "cursor-pointer", "hover:bg-darkbeige");
        }

        champ.value = ancienTexte;

    });
});

document.querySelectorAll('.modif-attribut .champ-text').forEach(input => {
    input.addEventListener('input', () => {
        newEtatChamp = true
    });
});

</script>

</html>