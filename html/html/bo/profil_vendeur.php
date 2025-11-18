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
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/valider cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/valider:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/valider:block">
                                </button>
                                <button type="button" class="bouton-annuler group/annuler cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 block group-hover/annuler:hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square-fill.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 hidden group-hover/annuler:block">
                                </button>
                            </div>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $denom ?></p>
                        <input type="text" name="denom" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $denom ?>">
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Adresse du siège social :</p>
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= "$num $numBis $adresse, $ville, $cp" ?></p>
                        <input type="text" name="adresse" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= "$num $numBis $adresse, $ville, $cp" ?>">
                    </div>

                    <div class=" mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Numéro SIREN :</p>
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
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
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $nom ?></p>
                        <input type="text" name="nom" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $nom ?>">
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">Prénom :</p>
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
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
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $tel ?></p>
                        <input type="text" name="tel" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $tel ?>">
                    </div>

                    <div class="mb-3 modif-attribut">
                        <div class=" flex flex-row items-center">
                            <p class="underline">E-Mail :</p>
                            <button type="button" class="bouton-modifier group/pen cursor-pointer">
                                <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                                <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                            </button>
                            <div class=" flex flex-row ">
                                <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                                </button>
                                <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                                </button>
                            </div>
                        </div>
                        <p class="attribut-text ml-7 mt-2"><?= $mail ?></p>
                        <input type="text" name="mail" class="champ-text w-full ml-5 hidden border-2 border-solid rounded-md border-beige pl-3" value="<?= $mail ?>">
                    </div>

                </div>
            </div>
            <div class=" mt-8 mb-20 modif-attribut">
                <div class=" flex flex-row items-center">
                    <h3 class=" mb-2">Description :</h3>
                    <button type="button" class="bouton-modifier group/pen cursor-pointer">
                        <img src="../../images/logo/bootstrap_icon/pencil.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 block group-hover/pen:hidden">
                        <img src="../../images/logo/bootstrap_icon/pencil-fill.svg" alt="modifier" title="modifier" class=" w-6! h-6! ml-4 hidden group-hover/pen:block">
                    </button>
                    <div class=" flex flex-row ">
                        <button type="button" class="bouton-valider group/check cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 block group-hover/check:hidden">
                                    <img src="../../images/logo/bootstrap_icon/check-square-fill.svg" alt="valider" title="valider" class=" w-6! h-6! ml-4 hidden group-hover/check:block">
                        </button>
                        <button type="button" class="bouton-annuler group/cross cursor-pointer hidden">
                                    <img src="../../images/logo/bootstrap_icon/x-square.svg" alt="annuler" title="annuler" class=" w-6! h-6! ml-4 group-hover/cross:hidden">
                        </button>
                    </div>
                </div>
                <p class="attribut-text ml-7"><?= $description ?></p>
                <textarea name="description" class="champ-text ml-5 hidden border-2 border-solid rounded-md border-beige pl-3 w-full h-40"><?= $description ?></textarea>
            </div>
        </form>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
</body>
<script>
document.querySelectorAll(".modif-attribut .bouton-modifier").forEach(button => {
    button.addEventListener("click", () => {
        const container = button.closest(".modif-attribut"); // parent
        const paragraph = container.querySelector("p.attribut-text"); // texte en p
        const champ = container.querySelector(".champ-text") // texte en input ou textarea
        const boutonValider = container.querySelector(".bouton-valider"); // bouton valider
        const boutonAnnuler = container.querySelector(".bouton-annuler"); // bouton annuler
        paragraph.classList.toggle("hidden");
        champ.classList.toggle("hidden");
        champ.classList.toggle("block");
    });
});
</script>

</html>