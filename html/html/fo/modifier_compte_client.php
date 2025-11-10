<?php
session_start();

require_once("../../php/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once("../../php/modification_variable.php"); // fonctions qui vérifient les données des formulaires
require_once("../../connections_params.php");; // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Préparation des données qui vont remplir les champ du formulaire
echo "Salut1 ! ";
$idCompte = $_SESSION["id_compte"];
echo $idCompte;
echo "Salut2 ! ";

foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._client cli
                                    ON c.id_compte = cli.id_compte
                                WHERE id_compte = $idCompte") 
                    as $ligne){
 
    echo "Salut3 ! ";
    print_r($ligne);
}
echo "Salut4 ! ";

//remplisage des variables
$nom = "";
$prenom = "";
$pseudo = "";
$mail = "";
$naissance = "";
$telephone = "";
$motDePasse = "";
echo "Salut5 ! ";

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
        <input type="text" name="nom" id="nom" value="<?php echo "Salut";?>" required>

        <label for="prenom">Prenom* :</label>
        <input type="text" name="prenom" id="prenom" required>

        <label for="pseudo">Pseudo :</label>
        <input type="text" name="pseudo" id="pseudo" required>

        <label for="naissance">Date de naissance* :</label>
        <input type="date" name="naissance" id="naissance" required> 

        <label for="telephone">Telephone* :</label>
        <input type="tel" name="telephone" id="telephone" placeholder="0604030201" pattern="0[0-9]{9}" required>

        <label for="mail">Adresse email* :</label>
        <input type="mail" name="mail" id="mail" required>

        <label for="mdp">Mot de passe* :</label>
        <input type="password" name="mdp" id="mdp" required>
        <p> 1 majuscule, 1 minuscule, 1 chiffre, 1 caractère spécial, 10 caractères minimum</p>

        <label for="verifMdp">Vérification du mot de passe* :</label>
        <input type="password" name="verifMdp" id="verifMdp" required>

		<button type="button"><a href="index.php">Anuler</a></button>
        <input type="Submit" name="submit" id="submit" value="S'inscrire">
    </form>

    <?php 
    // Import du footer
    include ("../../php/structure/footer_front.php");
    ?>

</body>
</html>
