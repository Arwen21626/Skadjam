<?php 
session_start();
include __DIR__ . '/../../01_premiere_connexion.php';
if (isset($_POST["mail"])){
    $mail = $_POST["mail"];
    $mdp = $_POST["mdp"];
    $stmt = $dbh->prepare("SELECT c.motdepasse, c.id_compte FROM sae3_skadjam._compte c inner join sae3_skadjam._vendeur v on c.id_compte = v.id_compte where adresse_mail = ?");
    $stmt->execute([$mail]);
    $passHash = $stmt->fetchColumn(0);
    $idCompte = $stmt->fetchColumn(1);
    $passCorrect = password_verify($mdp, $passHash);

    if ($passCorrect){
        header("location:index_vendeur.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<?php require_once __DIR__ . "/../../php/structure/head_back.php"?>
<head>
    
    <title>connexion vendeur</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_back.php"; ?>
    <main>
        <h2>Connexion</h2>
        <form method="POST">
            <div class="flex flex-col items-center ml-10 mb-7 mr-10">

                <div class="flex w-fit flex-col items-start">
                    <label for="mail">Adresse mail :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-bleu pl-3 mb-5" type="text" name="mail" id="mail" required>
                </div>

                <div class="flex w-fit flex-col mt-6 items-start">
                    <label for="mdp">Mot de passe :</label>
                    <input class="ml-5 border-5 border-solid rounded-2xl border-bleu pl-3" type="password" name="mdp" id="mdp" required>
                    <a href=""></a>
                    <p class="underline! self-end cursor-pointer">mot de passe oublié ?</p>
                </div class="flex w-fit flex-col mt-6 items-start">

                <div class=" justify-self-center mt-8 mb-8">
                    <input type="submit" value="Se connecter" class="cursor-pointer w-64 border-5 border-solid rounded-2xl border-bleu pl-3">
                </div>
            </div>
        </form>
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore vendeur ? </p>
            <a href="crea_compte_vendeur.php" class="underline!">Créer un compte</a>
        </div>
    </main>
</body>
</html>