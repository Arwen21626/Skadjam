<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";

require_once __DIR__ . "/../../php/verification_formulaire.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__ . "/../../php/modification_variable.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__ . "/../../../connections_params.php"; // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

$erreurs = $_SESSION['erreurs'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['old']);

// Préparation des données qui vont remplir les champs du formulaire
// Récupération du comptes clients
foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._client cli
                                    ON c.id_compte = cli.id_compte
                                WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
    $nom = $ligne['nom_compte'];
    $prenom = $ligne['prenom_compte'];
    $pseudo = $ligne['pseudo'];
    $mail = $ligne['adresse_mail'];
    $naissance = $ligne['date_naissance'];
    $telephone = $ligne['numero_telephone'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . "/../../php/structure/head_front.php";?>
<head>
    <title>Modification du compte client</title>
    <style>
        button a:hover {
            color: #000; 
        }
    </style>
</head>

<body>
    <?php
    // Import du header
    include __DIR__."/../../php/structure/header_front.php";
    ?>
    <main style="margin: 0" class="flex flex-col justify-center">
        <?php
        // Import de la bar de navigation
        include __DIR__."/../../php/structure/navbar_front.php";
        ?>

        <h2 class="flex justify-center text-center">Modification du compte client</h2>
        <!-- Formulaire -->
        <form class="md:flex md:flex-wrap p-15 pt-0 justify-around"  action="../../php/traitement_donnees_compte_client.php" method="post"> 
            <div class="md:flex md:flex-wrap mt-20">
                <!-- Nom -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="nom">Nom* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="nom" id="nom" value="<?php echo $nom;?>" required>
                    <?php if (isset($erreurs['nom'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le nom ne peut contenir que des majuscules, des minuscules, des - ou des espaces.</p>"; } ?>
                </div>

                <!-- Prenom -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="prenom">Prenom* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="prenom" id="prenom" value="<?php echo $prenom;?>" required>
                    <?php if (isset($erreurs['prenom'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le prénom ne peut contenir que des majuscules, des minuscules, des - ou des espaces.</p>"; } ?>
                </div>

                <!-- Pseudo -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="pseudo">Pseudo* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo;?>" required>
                    <?php if (isset($erreurs['pseudo'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le pseudo ne peut contenir que des majuscules, des minuscules, des - ou des espaces.</p>"; } ?>
                </div>
                
                <!-- Date de naissance -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="naissance">Date de naissance* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="date" name="naissance" id="naissance" value="<?php echo formatDate($naissance);?>" required>
                    <?php if (isset($erreurs['naissance'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">La date de naissance doit être de la forme : aaaa-mm-jj.</p>"; } ?>
                </div>

                <!-- Téléphone -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="telephone">Telephone* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" value="<?php echo formatTel($telephone);?>" required>
                    <?php if (isset($erreurs['telephone'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres.</p>"; } ?>
                </div>

                <!-- Adresse email -->
                <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="mail">Adresse email* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="mail" name="mail" id="mail" value="<?php echo $mail;?>" required>
                    <?php if (isset($erreurs['mail'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'adresse email est invalide.</p>"; } ?>
                </div>

                <!-- Gestion des adresses -->
                <?php 
                $compteur = 1;
                foreach($dbh->query("SELECT * FROM sae3_skadjam._habite h
                                            INNER JOIN sae3_skadjam._adresse a
                                                ON a.id_adresse = h.id_adresse
                                        WHERE h.id_compte = $idCompte
                                        ORDER BY a.id_adresse ASC", PDO::FETCH_ASSOC) as $ligne){?>
                    <div class="flex flex-col md:flex-row mt-20">
                        <div class="basis-2/3 flex flex-wrap">
                            <h3 class="basis-1/1  min-w-3xs">Adresse numéro <?php echo $compteur;?></h3>
                            
                            <!-- Adresse postal -->
                            <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="adressePostal">Adresse :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="adresse" name="adresse[<?php echo $compteur?>][adressePostal]" placeholder="ex : 3 rue des camélia" value="<?php echo ($ligne['numero_rue'] === '') ? '' : ($ligne['numero_rue'].' '.$ligne['complement_adresse'].' '.$ligne['adresse_postale']);?>" required>
                                <?php if (isset($erreurs['adresse_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'adresse est invalide.</p>"; } ?>
                            </div>

                            <!-- Ville -->
                            <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="ville">Ville :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="ville" name="adresse[<?php echo $compteur?>][ville]" value="<?php echo $ligne['ville'];?>" required>
                                <?php if (isset($erreurs['ville_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">La ville ne peut contenir que des majuscules, des minuscules, des - ou des espaces.</p>"; } ?>
                            </div>

                            <!-- Code postal -->
                            <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="cp">Code Postal :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="codePostal" name="adresse[<?php echo $compteur?>][codePostal]" value="<?php echo $ligne['code_postal'];?>" required>
                                <?php if (isset($erreurs['codePostal_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le code postal doit être composé de 5 chiffres.</p>"; } ?>
                            </div>

                            <!-- Batiment -->
                            <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                                <label for="batiment">Batiment :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="batiment" name="adresse[<?php echo $compteur?>][batiment]" value="<?php echo $ligne['numero_bat'];?>">
                                <?php if (isset($erreurs['batiment_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le bâtiment est invalide.</p>"; } ?>
                            </div>

                            <!-- Apartement -->
                            <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="apart">Apartement :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="apart" name="adresse[<?php echo $compteur?>][apart]" value="<?php echo $ligne['numero_appart'];?>">
                                <?php if (isset($erreurs['apart_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'appartement est invalide.</p>"; } ?>
                            </div>

                            <!-- Interphone -->
                            <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                                <label for="interphone">Interphone :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="interphone" name="adresse[<?php echo $compteur?>][interphone]" value="<?php echo $ligne['code_interphone'];?>">
                                <?php if (isset($erreurs['interphone_'.$compteur])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'interphone est invalide.</p>"; } ?>
                            </div>
                        </div>
                        <div class="flex items-center justify-end">
                            <button class="cursor-pointer border-4 border-rouge rounded-2xl w-80 h-14 p-0 m-0 mt-5" type="button"><a href="validation_suppression_adresse.php?idAdresse=<?php echo $ligne['id_adresse'];?>">Supprimer cette adresse</a></button>
                        </div>
                    </div>
                <?php 
                    $compteur++;
                }
            ?>
            </div>
            <button class="cursor-pointer border-4 border-beige rounded-2xl w-80 h-14 p-0 m-0 mt-5" type="button"><a href="./ajouter_adresse.php">Ajouter une adresse</a></button>

            <!-- Valider le formulaire -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1">
                <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10" type="button"><a href="./profil_client.php">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40  h-14 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="Valider">
            </div>
        </form>
    </main>

    <?php 
    // Import du footer
    include __DIR__ . "/../../php/structure/footer_front.php";

    // Fermer la connexion à la base de données
    $dbh = null;
    ?>

</body>
</html>
