<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../php/verification_formulaire.php";
require_once __DIR__ . "/../../php/modification_variable.php";
require_once __DIR__ . "/../../../connections_params.php";

// Connexion BDD
$dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

$_SESSION['erreurs'] = [];
$_SESSION['old'] = $_POST;
$ligne = [];

// Vérification uniquement si formulaire envoyé
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required = ['adresse','ville','codePostal'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $_SESSION['erreurs'][$field] = 'Champ obligatoire';
        }
    }

    // Validations
    if (!empty($_POST['adresse']) && !verifAdresse($_POST['adresse'])){
        $_SESSION['erreurs']['adresse'] = 'Adresse invalide';
    }
    if (!empty($_POST['ville']) && !verifVille($_POST['ville'])){
        $_SESSION['erreurs']['ville'] = 'Ville invalide';
    }
    if (!empty($_POST['codePostal']) && !verifCp($_POST['codePostal'])){
        $_SESSION['erreurs']['codePostal'] = 'Code postal invalide';
    }
    // Si aucune erreur
    if (empty($_SESSION['erreurs'])) {

        $adresseExplode = tabAdresse($_POST['adresse']);

        $stmt = $dbh->prepare("
            INSERT INTO sae3_skadjam._adresse 
            (numero_rue, complement_adresse, adresse_postale, ville, code_postal, numero_bat, numero_appart, code_interphone) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $adresseExplode[0] ?? '',
            $adresseExplode[1] ?? '',
            $adresseExplode[2] ?? '',
            $_POST['ville'],
            $_POST['codePostal'],
            $_POST['batiment'] ?? '',
            $_POST['apart'] ?? '',
            $_POST['interphone'] ?? ''
        ]);

        $adresseId = $dbh->lastInsertId();

        $stmt = $dbh->prepare("
            INSERT INTO sae3_skadjam._habite (id_adresse, id_compte) 
            VALUES (?, ?)
        ");
        $stmt->execute([$adresseId, $idCompte]);

        header('Location: ./modifier_compte_client.php');
        exit;
    }

    // Pré-remplissage en cas d'erreur
    $adresseSepare = tabAdresse($_POST['adresse'] ?? '');

    $ligne = [
        'numero_rue' => $adresseSepare[0] ?? '',
        'complement_adresse' => $adresseSepare[1] ?? '',
        'adresse_postale' => $adresseSepare[2] ?? '',
        'ville' => $_POST['ville'] ?? '',
        'code_postal' => $_POST['codePostal'] ?? '',
        'numero_bat' => $_POST['batiment'] ?? '',
        'numero_appart' => $_POST['apart'] ?? '',
        'code_interphone' => $_POST['interphone'] ?? ''
    ];
}

$erreurs = $_SESSION['erreurs'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['old']);
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

        <h2 class="flex justify-center text-center">Nouvelle adresse</h2>
        <!-- Formulaire -->
        <form class="flex flex-wrap p-15 pt-0 justify-around"  action="./ajouter_adresse.php" method="post"> 
            <div class="flex flex-wrap mt-20">
                
                <!-- Adresse postal -->
                <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                    <label for="adressePostal">Adresse* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="adresse" name="adresse" placeholder="ex : 3 rue des camélia" value="<?php echo ($ligne['numero_rue'] === '') ? '' : ($ligne['numero_rue'].' '.$ligne['complement_adresse'].' '.$ligne['adresse_postale']);?>" required/>
                    <?php if (!empty($erreurs['adresse'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'adresse est invalide.</p>"; } ?>
                </div>

                <!-- Ville -->
                <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                    <label for="ville">Ville* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="ville" name="ville" value="<?php echo $ligne['ville'];?>" required/>
                    <?php if (!empty($erreurs['ville'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">La ville ne peut contenir que des majuscules, des minuscules, des - ou des espaces.</p>"; } ?>
                </div>

                <!-- Code postal -->
                <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                    <label for="cp">Code Postal* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="codePostal" name="codePostal" value="<?php echo $ligne['code_postal'];?>" required/>
                    <?php if (!empty($erreurs['codePostal'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le code postal doit être composé de 5 chiffres.</p>"; } ?>
                </div>

                <!-- Batiment -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="batiment">Batiment :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="batiment" name="batiment" value="<?php echo $ligne['numero_bat'];?>"/>
                    <?php if (!empty($erreurs['batiment'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">Le bâtiment est invalide.</p>"; } ?>
                </div>

                <!-- Apartement -->
                <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                    <label for="apart">Apartement :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="apart" name="apart" value="<?php echo $ligne['numero_appart'];?>"/>
                    <?php if (!empty($erreurs['apart'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'appartement est invalide.</p>"; } ?>
                </div>

                <!-- Interphone -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="interphone">Interphone :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" id="interphone" name="interphone" value="<?php echo $ligne['code_interphone'];?>"/>
                    <?php if (!empty($erreurs['interphone'])){ echo "<p class=\"text-rouge\" style=\"font-size: 0.90em\">L'interphone est invalide.</p>"; } ?>
                </div>
            </div>
            <!-- Valider le formulaire -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1">
                <button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 p-0 m-0 mr-10" type="button"><a href="./modifier_compte_client.php">Annuler</a></button>
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
