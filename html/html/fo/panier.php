<?php 
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    require_once(__DIR__ . "/../../php/fonctions.php");

    session_start();
    if ($_SESSION["role"] === "visiteur") {
        header("location:/html/fo/connexion.php");
    }

    $idClient = $_SESSION["idCompte"];

    $rqt = $dbh->query("SELECT * FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
    $infoPanier = $rqt->fetch();

    $idPanier = $infoPanier["id_panier"];
    $nbProduitsTotal = $infoPanier["nb_produit_total"];
    $montantTotalTTC = $infoPanier["montant_total_ttc"];

    $produitsPanier = array();

    foreach ($dbh->query("SELECT id_produit, quantite_par_produit
                          FROM sae3_skadjam._contient
                          WHERE id_panier = $idPanier") as $row) {
        $produitsPanier[] = $row;
    }
    
    if (!empty($produitsPanier)) 
    {
        $infoProduitsPanier = array();

        foreach ($produitsPanier as $i => $value) 
        { 
            $rqt = $dbh->query("SELECT id_produit, libelle_produit, prix_ttc, note_moyenne
                                FROM sae3_skadjam._produit WHERE id_produit = " . $produitsPanier[$i]["id_produit"], PDO::FETCH_ASSOC);
            $infoProduit = $rqt->fetch();

            $rqt = $dbh->query("SELECT id_photo FROM sae3_skadjam._montre WHERE id_produit = " . $produitsPanier[$i]["id_produit"], PDO::FETCH_ASSOC);
            $idPhoto = $rqt->fetch()["id_photo"];

            $rqt = $dbh->query("SELECT url_photo, alt, titre FROM sae3_skadjam._photo WHERE id_photo = $idPhoto", PDO::FETCH_ASSOC);
            $infoPhoto = $rqt->fetch();

            $infoProduitsPanier[$i]["infoProduit"] = $infoProduit;
            $infoProduitsPanier[$i]["quantiteProduit"] = $produitsPanier[$i]["quantite_par_produit"];
            $infoProduitsPanier[$i]["infoPhoto"] = $infoPhoto;
        }
    }
    
   
?>

<!-- <pre>
    <?php // print_r($infoProduitsPanier); ?>
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
                    <main class="md:min-h-[545px] md:p-4 md:flex md:justify-center">
                        <h2 class="md:text-center md:self-center">Votre panier est vide</h2>
                <?php
            }
            else
            {
                ?>
                    <main class="md:min-h-[545px] md:p-4 md:grid md:grid-cols-2">

                        <div id="conteneur-produit" class="flex flex-col">
                            <?php
                                foreach ($infoProduitsPanier as $i => $value) 
                                {
                                    ?>
                                        <div class="bg-bleu p-4 m-4 shadow md:grid md:grid-cols-2">

                                            <!-- l'Image -->
                                            <div class="flex justify-center">
                                                <a href="/html/fo/details_produit.php?idProduit=<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                    <img src="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["url_photo"]; ?>" 
                                                    alt="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["alt"]; ?>" 
                                                    title="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["titre"]; ?>"
                                                    class="border">
                                                </a> 
                                            </div>
                                            
                                            <!-- Le conteneur des éléments liés au produit -->
                                            <div class="text-center md:flex md:flex-col md:justify-evenly">
                                                <div class="flex flex-col justify-center items-center">
                                                    <h4> <?php echo $infoProduitsPanier[$i]["infoProduit"]["libelle_produit"]; ?></h4>
                                                    <p class="mt-2"> <?php echo affichageNote($infoProduitsPanier[$i]["infoProduit"]["note_moyenne"]); ?> </p>
                                                </div>
                                                
                                                <div id="prix">
                                                    <p> <?php echo "Prix unitaire : " . $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] . "€"; ?> </p>
                                                    <p> 
                                                        <?php
                                                            $prixTot = ($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"]);
                                                            $prixTot = number_format($prixTot, 2, '.', '');
                                                            echo "Prix total : " . $prixTot . "€"; 
                                                        ?> 
                                                    </p>
                                                </div>
                                                <div class="flex justify-center items-center">
                                                    <form method="post" action="#" >
                                                        <input type="hidden" name="idProduit" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                        <button class="text-4xl text-center mr-4 cursor-pointer hover:text-rouge"
                                                        type="submit">
                                                            -
                                                        </button>
                                                    </form>

                                                    <p class="ml-4 mr-4 text-center"> <?php echo $infoProduitsPanier[$i]["quantiteProduit"]; ?> </p>

                                                    <form method="post" action="/php/ajouter_panier.php" >
                                                        <input type="hidden" name="idProduit" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                        <input type="hidden" name="pageDeRetour" value="panier">
                                                        <button class="text-4xl text-center ml-4 cursor-pointer hover:text-vertClair"
                                                        type="submit">
                                                            +
                                                        </button>
                                                    </form>
                                                </div>
                                                
                                                <button>Supprimer du panier</button>
                                            </div>

                                        </div>
                                    <?php
                                }
                            ?>
                            

                            
                        </div>
                    
                        
                <?php
            }
        ?>
        
    </main>
    
    <?php 
        if (!empty($produitsPanier))
        {
            ?>
                <div class="grid grid-cols-5 fixed bottom-64 w-full pointer-events-none">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div id="conteneur-info_panier" class="grid grid-rows-4 justify-center items-center pointer-events-auto bg-beige rounded-2xl pb-4">
                        <div class="inline-flex mt-4">
                            <p class="mr-2">Nombre d'article : </p>
                            <p class="ml-2"> <?php echo $nbProduitsTotal; ?> </p>
                        </div>
                        <div class="inline-flex mt-4">
                            <p class="mr-2">Sous total :</p>
                            <p class="ml-2"> <?php echo $montantTotalTTC . "€"; ?> </p>
                        </div>
                        <form class="flex justify-center" action="/php/vider_panier.php">
                            <button class="bg-beige rounded-2xl w-40 h-14 mt-4 cursor-pointer hover:bg-rouge hover:text-white border-black border shadow" type="submit">
                                Vider le panier
                            </button>
                        </form>
                        
                        <form class="flex justify-center" action="">
                            <button class="bg-beige rounded-2xl w-40 h-14 mt-4 cursor-pointer hover:text-rouge border-black border shadow" type="submit">
                                Acheter
                            </button>
                        </form>
                        
                    </div>
                    <div></div>
                </div>
            <?php
        }
    ?>
    
    
    <?php include("php/structure/footer_front.php") ?>
</body>
</html>