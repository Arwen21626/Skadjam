<?php
session_start();
require_once("../../php/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once("../../php/modification_variable.php"); // fonctions qui vérifient les données des formulaires
require_once("../../../connections_params.php"); // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Préparation des données qui vont remplir les champs du formulaire
$idCompte = $_SESSION["idCompte"];
echo 'Salut 1 ! ';
foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._client cli
                                    ON c.id_compte = cli.id_compte
                                WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
    echo 'Salut 2 ! ';
    $nom = $ligne['nom_compte'];
    $prenom = $ligne['prenom_compte'];
    $pseudo = $ligne['pseudo'];
    $mail = $ligne['adresse_mail'];
    $naissance = $ligne['date_naissance'];
    $telephone = $ligne['numero_telephone'];
}

// Fermer la connexion à la base de données
$dbh = null;
?>

<!DOCTYPE html>
<html lang="fr">
<?php include ("../../php/structure/head_front.php");?>
<head>
    <title>Modification d'un compte client</title>
</head>

<body>
    <?php
    // Import du header
    include ("../../php/structure/header_front.php");
    ?>

    <h2>Modification du compte client</h2>

    <!-- voir s'il n'y a pas d'autres infos modifiables : adresse -->
    <!-- demander à Mewen pour les adresses-->

    <form action="../../php/traitement_donnees_compte_client.php" method="post"> 
        <label for="nom">Nom* :</label>
        <input type="text" name="nom" id="nom" value="<?php echo $nom;?>" required>

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom" value="<?php echo $prenom;?>" required>

        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo;?>" required>

        <label for="naissance">Date de naissance* :</label>
        <input type="date" name="naissance" id="naissance" value="<?php echo formatDate($naissance);?>" required> 

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" value="<?php echo formatTel($telephone);?>" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" value="<?php echo $mail;?>" required>

        <!-- <button type="button"><a href="./nouveau_mdp.php">Modifier le mot de passe</a></button> -->

		<button type="button"><a href="./index.php">Anuler</a></button>
        <input type="Submit" name="submit" id="submit" value="Valider">
    </form>

    <?php 
    // Import du footer
    include ("../../php/structure/footer_front.php");
    ?>

</body>
</html>
