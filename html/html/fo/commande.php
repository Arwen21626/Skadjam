<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabInfoCommandes = null;           
        //récupère toutes les infos des tables produits et photos
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._commande c
                            WHERE c.id_client = $idCompte"
                            , PDO::FETCH_ASSOC) as $row){
            $tabInfoCommandes[] = $row;
        } 
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de la commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>
</body>
</html>