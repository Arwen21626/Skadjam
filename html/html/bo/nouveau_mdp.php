<?php
session_start();
include __DIR__ . "/../../php/verif_role_bo.php";
include __DIR__ . "/../../01_premiere_connexion.php";
include __DIR__ . "/../../php/verification_formulaire.php";
if (isset($_SESSION["idCompte"]) && $_SESSION["role"] === "vendeur"){
    $idCompte = $_SESSION["idCompte"];
    $role = $_SESSION["role"];

    if ($_SERVER["REQUEST_METHOD"] === "POST"){
        $nouveau_mdp = $_POST["mdp"];
        $confirmation = $_POST["confirmation"];

        $erreur = [];

        /* MDP */
        if (!verifMotDePasse($nouveau_mdp)) $erreur["mdp"] = "Doit inclure tous les éléments si dessous";
        if (!confirmationMotDePasse($confirmation, $nouveau_mdp)) $erreur["conf"] = "Le mot de passe est différent";
        if (empty($erreur)){
            try{
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._compte SET mot_de_passe = ? WHERE id_compte = ?");
                $stmt->execute([password_hash($nouveau_mdp, PASSWORD_DEFAULT), $idCompte]);
                header("Location: profil_vendeur.php");
            }catch (PDOException $e) {
                print "Erreur update!: " . $e->getMessage() . "<br/>";
                die();
            }
        }

    }

    require_once __DIR__ . "/../../php/structure/head_back.php"
    ?>

    <!DOCTYPE html>
    <html lang="fr" class="h-1/1">
    <head>
        
        <title>nouveau mot de passe</title>
    </head>
    <body class="relative h-1/1 flex flex-col">
        <?php require_once __DIR__ . "/../../php/structure/header_back.php" ?>
        <main class="flex-1 flex flex-col justify-center items-center">
            <h2>Nouveau mot de passe</h2>
            <form method="POST" class="flex flex-col justify-center items-center flex-1 w-fit">
                <div class="flex flex-col items-start mt-6 w-fit ">
                    <label for="mdp">Entrer le nouveau mot de passe :</label>
                    <div class="zone-mdp flex flex-row items-center mb-4">
                        <input type="password" name="mdp" id="mdp" required <?= isset($nouveau_mdp) ? "value=\"$nouveau_mdp\" " : "" ?> class="champ-mdp ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-150">
                        <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                    </div>
                    <?php echo (isset($erreur["mdp"])) ? "<p class=\"text-rouge\">" . $erreur["mdp"] . " </p>" : '' ?>
                    <p>1 majuscule, 1 minuscule, 1 chiffre,<br> 1 caractère spécial, 10 caractères minimum</p>
                </div>
                <div class="flex flex-col items-start mt-6 w-fit ">
                    <label for="confirmation">Confirmer le mot de passe :</label>
                    <div class="zone-mdp flex flex-row items-center mb-4">
                        <input type="password" name="confirmation" id="confirmation" required <?= isset($confirmation) ? "value=\"$confirmation\" " : "" ?> class="champ-mdp ml-5 border-4 border-solid rounded-2xl border-beige p-1 pl-3 w-150">
                        <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                    </div>
                    <?php echo (isset($erreur["conf"])) ? "<p class=\"text-rouge\">" . $erreur["conf"] . " </p>" : '' ?>
                </div>
    
                <div class="flex felx-row w-full justify-between mt-10 mb-10">
                    <a href="profil_vendeur.php" class="cursor-pointer text-center block w-64 border-4 border-solid rounded-2xl border-beige p-1 pl-3">Annuler</a>
                    <input type="submit" value="Valider" class="cursor-pointer w-64 border-4 border-solid rounded-2xl border-beige p-1 pl-3">
                </div>
            </form>
        </main>
        <footer class="aboslute bottom-0">
            <p class="hidden">footer en bas</p>
        </footer>
        <?php require_once __DIR__ . "/../../php/structure/footer_back.php" ?>
    </body>

    <script src="../../js/bo/visibilite_mdp.js"></script>

    </html>

<?php
}
?>
