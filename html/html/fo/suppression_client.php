<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";

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
?>
<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
<head>
    <title>Mon profil</title>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="min-h-[600px]">
            <h2>Voulez-vous vraiment supprimer votre compte ?</h2>
            <h3 class="text-center">Cette action est irréversible.</h3>
            <div class="mt-15 flex flex-row justify-around mb-15">
                <a href="../../php/supprimer_compte_client.php"><button class="border-4 border-rouge rounded-xl w-auto h-14 px-7 cursor-pointer">Supprimer mon compte</button></a>
                <a href="profil_client.php"><button class="border-4 border-vertClair rounded-xl w-auto h-14 px-7 cursor-pointer">Retour sur mon profil</button></a>
            </div>
        </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
