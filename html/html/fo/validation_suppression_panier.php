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
    <title>Panier</title>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="min-h-[600px]">
            <h2>Voulez-vous vraiment vider votre panier ?</h2>
            <div class="mt-100 md:mt-15 flex flex-row justify-around">
                <a href="panier.php"><button class="border-2 border-vertClair rounded-xl w-auto h-14 px-7 cursor-pointer">Annuler</button></a>
                <form class="flex justify-center" method="get" action="/php/vider_panier.php">
                    <input type="hidden" name="typeVider" value="normal">
                    <button class="border-2 border-rouge rounded-xl w-auto h-14 px-7 cursor-pointer" type="submit">
                        Confirmer
                    </button>
                </form>
            </div>
        </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>