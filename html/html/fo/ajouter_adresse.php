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
    <title>Modification de mon compte</title>
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
        <?php
            // Sécurisation des valeurs
            $numero_rue         = htmlspecialchars($ligne['numero_rue'] ?? '');
            $complement_adresse = htmlspecialchars($ligne['complement_adresse'] ?? '');
            $adresse_postale    = htmlspecialchars($ligne['adresse_postale'] ?? '');
            $ville              = htmlspecialchars($ligne['ville'] ?? '');
            $code_postal        = htmlspecialchars($ligne['code_postal'] ?? '');
            $numero_bat         = htmlspecialchars($ligne['numero_bat'] ?? '');
            $numero_appart      = htmlspecialchars($ligne['numero_appart'] ?? '');
            $code_interphone    = htmlspecialchars($ligne['code_interphone'] ?? '');

            $adresse_complete = trim($numero_rue . ' ' . $complement_adresse . ' ' . $adresse_postale);
        ?>

        <form class="flex flex-wrap p-15 pt-0 justify-around" action="./ajouter_adresse.php" method="post"> 
            <div class="flex flex-wrap mt-20">
                
                <!-- Adresse postal -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="adresse">Adresse* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="text" id="adresse" name="adresse" placeholder="1 rue des Fleurs" value="<?= $adresse_complete ?>" required/>
                    <?php if (!empty($erreurs['adresse'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">
                            L'adresse ne peut contenir que des chiffres, lettres majuscules ou minuscules, 
                            virgules et espaces.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Ville -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="ville">Ville* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="Lannion" type="text" id="ville" name="ville" value="<?= $ville ?>" required/>
                    <?php if (!empty($erreurs['ville'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">
                            La ville ne peut contenir que des majuscules, des minuscules, des tirets ou des espaces.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Code postal -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="codePostal">Code Postal* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="22300" type="text" id="codePostal" name="codePostal" value="<?= $code_postal ?>" required/>
                    <?php if (!empty($erreurs['codePostal'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">
                            Le code postal doit être composé de 5 chiffres.
                        </p>
                    <?php endif; ?>
                </div>

                <!-- Batiment -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="batiment">Bâtiment :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="3C" type="text" id="batiment" name="batiment" value="<?= $numero_bat ?>"/>
                    <?php if (!empty($erreurs['batiment'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">Le bâtiment est invalide.</p>
                    <?php endif; ?>
                </div>

                <!-- Appartement -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="apart">Appartement :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="22C" type="text" id="apart" name="apart" value="<?= $numero_appart ?>"/>
                    <?php if (!empty($erreurs['apart'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">L'appartement est invalide.</p>
                    <?php endif; ?>
                </div>

                <!-- Interphone -->
                <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                    <label for="interphone">Interphone :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="1234" type="text" id="interphone" name="interphone" value="<?= $code_interphone ?>"/>
                    <?php if (!empty($erreurs['interphone'])): ?>
                        <p class="text-rouge" style="font-size: 0.90em">L'interphone est invalide.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Boutons -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1">
                <a href="./modifier_compte_client.php"
                class="flex items-center justify-center cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 mr-10">
                Annuler
                </a>

                <input class="cursor-pointer border-2 border-vertFonce rounded-2xl w-40 h-14 md:mr-10"
                    type="submit"
                    name="submit"
                    value="Valider">
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
