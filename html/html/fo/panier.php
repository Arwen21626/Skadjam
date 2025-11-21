<?php 
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    session_start();
    if ($_SESSION["role"] === "visiteur") {
        header("location:/html/fo/connexion.php");
    }

    $idClient = $_SESSION["idCompte"];

    $rqt = $dbh->query("SELECT * FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
    $infoPanier = $rqt->fetch();

    $idPanier = $infoPanier["id_panier"];
    $produitsPanier = array();

    foreach ($dbh->query("SELECT id_produit, quantite_par_produit
                          FROM sae3_skadjam._contient
                          WHERE id_panier = $idPanier") as $row) {
        $produitsPanier[] = $row;
    }
    
    $infoProduitsPanier = array();

    for ($i=0; $i < count($produitsPanier); $i++) 
    { 
        
    }
   
?>

<!-- <pre>
    <?php //print_r($produitsPanier);  ?>
</pre> -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include("php/structure/head_front.php");?>
    <title>Panier</title>
</head>
<body>
    <?php include("php/structure/header_front.php") ?>
    <?php include("php/structure/navbar_front.php") ?>

    <main class="min-h-[480px] p-4 flex justify-center">
        <h2 class="text-center self-center">Votre panier est vide</h2>
    </main>

    <?php include("php/structure/footer_front.php") ?>
</body>
</html>