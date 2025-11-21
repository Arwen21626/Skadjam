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
    
    if (!empty($produitsPanier)) {
        $infoProduitsPanier = array();

        for ($i=0; $i < count($produitsPanier); $i++) 
        { 
            
        }
    }
    
   
?>

<!-- <pre>
    <?php  ?>
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

    

        <?php
            if (empty($produitsPanier)) {
                ?>
                    <main class="md:min-h-[480px] md:p-4 md:flex md:justify-center">
                    <h2 class="md:text-center md:self-center">Votre panier est vide</h2>
                <?php
            }
            else
            {
                ?>
                    <main class="md:min-h-[480px] md:p-4 md:grid md:grid-cols-2">
                    <div id="conteneur-produit">
                        <div class="md:grid md:grid-cols-2">
                            <img src="/images/photo_importees/Cidre1763393021.webp" alt="place_holder" title="place_holder"
                            class="h-auto w-1/2 self-center">
                            <div>
                                <h4>"Nom du produit"</h4>
                                <div id="note-prix">
                                    <p>"Prix"€</p>
                                </div>
                                <p>"Quantité"</p>
                                <button>Supprimer du panier</button>
                            </div>
                        </div>
                    </div>
                
                    <div id="conteneur-info_panier">

                    </div>
                <?php
            }
        ?>
        
    </main>

    <?php include("php/structure/footer_front.php") ?>
</body>
</html>