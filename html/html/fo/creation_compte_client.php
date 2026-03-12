<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";

$erreurs = $_SESSION['erreurs'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['old']);
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_front.php";?>
<head>
    <title>Création d'un compte client</title>
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

        <h2 class="flex justify-center text-center">Création du compte client</h2>

        <!-- Formulaire -->
        <form class="flex flex-wrap p-15 pt-0 justify-around" action="../../php/traitement_donnees_compte_client.php" method="post">
            <!-- Ajout d'un attribut au POST nécessaire à la redirection sur le panier en cas de volonté d'achat -->
            <?php if (isset($_GET['veutAcheter'])) { ?>
                <input type="hidden" name="veutAcheter" value="V">
            <?php } ?>
            <!-- Nom -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="nom">Nom* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="nom" id="nom" value="<?= htmlspecialchars($old['nom'] ?? '') ?>" required>
                <?php if (isset($erreurs['nom'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô.</p>"; } ?>
            </div>

            <!-- Prénom -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="prenom">Prénom* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="prenom" id="prenom" value="<?= htmlspecialchars($old['prenom'] ?? '') ?>" required>
                <?php if (isset($erreurs['prenom'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>Le prénom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces les accents : é, ç, è, ë, ê, à, ï, î, ä, â, ù, ü, û, ö, ô.</p>"; } ?>
            </div>

            <!-- Pseudo -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="pseudo">Pseudo* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="text" name="pseudo" id="pseudo"value="<?= htmlspecialchars($old['pseudo'] ?? '') ?>" required>
                <?php if (isset($erreurs['pseudo'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>Le pseudo ne peut que contenir des majuscules, des minuscules, des chiffres, des tirets, tirets du bas ou des espaces.</p>"; } ?>
            </div>

            <!-- Date de naissance -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="naissance">Date de naissance* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="date" name="naissance" id="naissance" value="<?= htmlspecialchars($old['naissance'] ?? '') ?>" required> 
                <?php if (isset($erreurs['naissance'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>La date de naissance doit être de la forme : jj/mm/aaaa.</p>"; } ?>
            </div>

            <!-- Téléphone -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="telephone">Telephone* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" maxlength="10" type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" value="<?= htmlspecialchars($old['telephone'] ?? '') ?>" required>
                <?php if (isset($erreurs['telephone'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres.</p>"; } ?>
            </div>

            <!-- Adresse email -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mail">Adresse email* :</label>
                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="mail" name="mail" id="mail" value="<?= htmlspecialchars($old['mail'] ?? '') ?>" required>
                <?php if (isset($erreurs['mail'])) { echo "<p class='text-rouge' style='font-size: 0.90em'>L'adresse doit être au format : adresse@e.mail </p>"; } ?>
            </div>

            <!-- Mot de passe -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="mdp">Mot de passe* :</label>
                <div class="zone-mdp flex flex-row ">
                    <input class="champ-mdp border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="password" name="mdp" id="mdp" value="<?= htmlspecialchars($old['mdp'] ?? '') ?>" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
                <p style="font-size: 0.90em" class="<?php if (isset($erreurs['mdp'])) { echo "text-rouge"; } ?>"> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>
            </div>

            <!-- Vérification du mot de passe -->
            <div class="flex flex-col basis-1/3 m-5 min-w-3xs">
                <label for="verifMdp">Vérification du mot de passe* :</label>
                <div class="zone-mdp flex flex-row ">
                    <input class="champ-mdp border-4 border-beige rounded-2xl w-1/1 p-1 pl-3" type="password" name="verifMdp" id="verifMdp" required>
                    <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                </div>
                <?php if (isset($verifMdp)) { echo "<p class='text-rouge' style='font-size: 0.90em'>La vérification doit être identique à votre mot de passe.</p>"; } ?>
            </div>

            <!-- Acceptation des CGU -->
            <div class="flex flex-row flex-wrap items-center mt-2 mb-2">
                <label for="cgu" class="underline! cursor-pointer hover:text-rouge">J'ai lu et j'acccepte les conditions générales d'utilisation</label>
                <input type="checkbox" id="cgu" name="cgu" required class="cursor-pointer ml-5 w-5 h-5">
            </div>

            <!-- Validation ou pas du formulaire -->
            <div class="flex mt-10 justify-center md:justify-end w-1/1 ">
                <button class="cursor-pointer border-2 border-vertClair md:rounded-2xl w-40 md:h-14 rounded-xl h-10 p-0 m-0 mr-10" type="button"><a href="/index.php">Annuler</a></button>
                <input class="cursor-pointer border-2 border-vertClair md:rounded-2xl w-40 md:h-14 rounded-xl h-10 p-0 m-0 md:mr-10" type="Submit" name="submit" id="submit" value="S'inscrire">
            </div>
        </form>
        <div class="flex flex-row flex-wrap justify-center">
            <!-- Lien vers la page de connexion -->
            <div class="hover:text-rouge flex flex-row flex-wrap justify-center ml-10 mr-10 mb-10">
                <a href="connexion.php">Vous avez déjà un compte ?<span class="underline">Connectez vous</span></a>
            </div>

            <!-- Lien vers la création d'un compte vendeur -->
            <div class="hover:text-rouge flex flex-row flex-wrap justify-center ml-10 mr-10 mb-10">
                <a href="../bo/crea_compte_vendeur.php">Vous êtes un vendeur ?<span class="underline">Créer un compte vendeur</span></a>
            </div>
        </div>
    </main>

    <?php 
    // Import du footer
    include __DIR__."/../../php/structure/footer_front.php";
    ?>

</body>
<script src="../../js/bo/visibilite_mdp.js"></script>
</html>
