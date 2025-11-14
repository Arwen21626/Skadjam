<?php
session_start();
require_once("../../php/verification_formulaire.php"); // fonctions qui vérifient les données des formulaires
require_once("../../php/modification_variable.php"); // fonctions qui vérifient les données des formulaires
require_once("../../../connections_params.php"); // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION["idCompte"];
$idCompte = 3; // à retirer

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


        <!-- On connait pas le nombre d'adresse : faire boucle sur le nombre de couple(id_compte, id_adresse) dans _habite, les récup dans un tableau dans post et géré ses tableau dans le traitement des données-->
        <!-- Gestion des adresses -->
        <?php 
        $compteur = 1;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._habite h
                                    INNER JOIN sae3_skadjam._adresse a
                                        ON a.id_adresse = h.id_adresse
                                WHERE h.id_compte = $idCompte
                                ORDER BY a.id_adresse ASC", PDO::FETCH_ASSOC) as $ligne){?>
            <h3>Adresse numéro <?php echo $compteur;?></h3>

            <label for="adressePostal">Adresse :</label>
            <input type="text" id="adresse" name="adresse[<?php echo $compteur?>][adressePostal]" placeholder="ex : 3 rue des camélia" value="<?php echo ($ligne['numero_rue'] === '') ? '' : ($ligne['numero_rue'].' '.$ligne['complement_adresse'].' '.$ligne['adresse_postale']);?>" required>

            <label for="ville">Ville :</label>
            <input type="text" id="ville" name="adresse[<?php echo $compteur?>][ville]" value="<?php echo $ligne['ville'];?>" required>

            <label for="cp">Code Postal :</label>
            <input type="text" id="codePostal" name="adresse[<?php echo $compteur?>][codePostal]" value="<?php echo $ligne['code_postal'];?>" required>

            <label for="batiment">Batiment :</label>
            <input type="text" id="batiment" name="adresse[<?php echo $compteur?>][batiment]" value="<?php echo $ligne['numero_bat'];?>">

            <label for="apart">Apartement :</label>
            <input type="text" id="apart" name="adresse[<?php echo $compteur?>][apart]" value="<?php echo $ligne['numero_appart'];?>">

            <label for="interphone">Interphone :</label>
            <input type="text" id="interphone" name="adresse[<?php echo $compteur?>][interphone]" value="<?php echo $ligne['code_interphone'];?>">
        <?php 
            $compteur++;
        }

        ?>
        

        <?php ?>
        <!-- <button type="button"><a href="./nouveau_mdp.php">Modifier le mot de passe</a></button> -->

		<button type="button"><a href="./index.php">Anuler</a></button>
        <input type="Submit" name="submit" id="submit" value="Valider">
    </form>

    <?php 
    // Import du footer
    include ("../../php/structure/footer_front.php");

    // Fermer la connexion à la base de données
    $dbh = null;
    ?>

</body>
</html>
