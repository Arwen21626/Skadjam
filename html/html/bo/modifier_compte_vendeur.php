<?php
session_start();
include __DIR__ . "/../../php/verif_role_bo.php";
include __DIR__ . "/../../01_premiere_connexion.php";
include __DIR__ . '/../../php/modification_variable.php';
include __DIR__ . '/../../php/verification_formulaire.php';

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION["idCompte"];

// Préparation des données qui vont remplir les champs du formulaire
// Récupération du comptes clients
foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._vendeur v
                                    ON c.id_compte = v.id_compte
                                WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
    // Infos compte
    $nom = $ligne['nom_compte'];
    $prenom = $ligne['prenom_compte'];
    $mail = $ligne['adresse_mail'];
    $tel = $ligne['numero_telephone'];
    $tel = "0" . substr($tel, 3);

    // Infos vendeur
    $denom = $ligne["raison_sociale"];
    $siren = $ligne["siren"];
    $iban = $ligne["iban"];
    $raisonSociale = $ligne["raison_sociale"];
    $description = isset($ligne["description_vendeur"]) ? $ligne["description_vendeur"] : "Aucune description.";
}
// Infos adresse
foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                    INNER JOIN sae3_skadjam._habite h
                        ON c.id_compte = h.id_compte
                    INNER JOIN sae3_skadjam._adresse a
                        ON h.id_adresse = a.id_adresse
                    WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $adresseData){
    $adresse = $adresseData["adresse_postale"];
    $num = $adresseData["numero_rue"];
    $numBis = $adresseData["complement_adresse"];
    $cp = $adresseData["code_postal"];
    $ville = $adresseData["ville"];
}


?>

<!DOCTYPE html>
<html lang="fr">
<?php include (__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <title>Modification du compte vendeur</title>
    <style>
        button a:hover {
            color: #000; 
        }
    </style>
</head>

<body>
    <?php include __DIR__."/../../php/structure/header_back.php"; ?>
    <main style="margin: 0" class="flex flex-col justify-center">
        <?php include __DIR__."/../../php/structure/navbar_back.php"; ?>

        <h2 class="flex justify-center text-center">Modification du compte vendeur</h2>
        <!-- Formulaire -->
        <form class="flex flex-wrap p-15 pt-0 justify-around"  action="../../php/traitement_donnees_compte_vendeur.php" method="post"> 
            <!-- Vendeur -->
            <h3>Informations vendeur :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10 @max-[768px]:ml-5 @max-[768px]:mr-5">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="nom">Nom * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2" type="text" id="nom" name="nom" value="<?= $nom; ?>" size="25" required >
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="prenom">Prénom * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="prenom" name="prenom" value="<?= $prenom; ?>" size="25" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="mail">Mail * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="email" id="mail" name="mail" value="<?= $mail; ?>" size="40" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="tel">Numéro de téléphone * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="tel" id="tel" name="tel" value="<?= $tel; ?>" size="16" required>
                </div>
            </div>

            <!-- Entreprise -->
            <h3>Informations entreprise :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="raisonSociale">Raison sociale de l'entreprise * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="raisonSociale" name="raisonSociale" value="<?= $raisonSociale; ?>" size="40" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="denomination">Nom de l'entreprise * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="denomination" name="denomination" value="<?= $denom; ?>" size="40" required>
                </div> 
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="siren">Numéro de SIREN * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="siren" name="siren" value="<?= $siren; ?>" size="11" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="iban">Numéro de IBAN * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="iban" name="iban" value="<?= $iban; ?>" placeholder="FR" size="40" required>
                </div>
            </div>

            <!-- Adresse -->
            <h3>Siège social :</h3>
            <div class="flex flex-row flex-wrap justify-between ml-10 mb-7 mr-10">
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="adresse">Adresse * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="adresse" name="adresse" value="<?= $num . (isset($numBis) ? " $numBis" : " ") . $adresse; ?>" size="60" placeholder="ex : 3 rue des camélias" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="ville">Ville * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="ville" name="ville" value="<?= $ville; ?>" size="30" required>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit @max-[768px]:mt-2">
                    <label for="cp">Code Postal * :</label>
                    <input class="ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 mb-4 @max-[768px]:ml-2 max-w-3/4 @max-[768px]:pl-2 " type="text" id="cp" name="cp" value="<?= $cp; ?>" size="10" required>
                </div>
                
            </div>

            <!-- Valider le formulaire -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1">
                <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10" type="button"><a href="./profil_vendeur.php">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40  h-14 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="Valider">
            </div>
        </form>
    </main>

    <?php 
    // Import du footer
    include __DIR__ . "/../../php/structure/footer_back.php";

    // Fermer la connexion à la base de données
    $dbh = null;
    ?>

</body>
</html>
