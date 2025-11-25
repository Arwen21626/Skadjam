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
    $nbProduitsTotal = 0;
    $montantTotalTTC = 0;

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

            $montantTotalTTC += $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"];
            $montantTotalTTC = number_format($montantTotalTTC, 2, ".", "");
            $nbProduitsTotal += $infoProduitsPanier[$i]["quantiteProduit"];
        }

        //Mise à jour des attributs nb_produit_total
        $dbh->query("UPDATE sae3_skadjam._panier SET nb_produit_total = $nbProduitsTotal, montant_total_ttc = $montantTotalTTC WHERE id_panier = $idPanier");
    } 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include(__DIR__ . "/../../php/structure/head_front.php");?>
    <title>Panier</title>
</head>
<body>
    <?php include(__DIR__ . "/../../php/structure/header_front.php") ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php") ?>

    

        <?php
            if (empty($produitsPanier)) {
                ?>
                    <main class="min-h-[360px] md:min-h-[545px] md:p-4 flex justify-center">
                        <h2 class="md:text-center self-center">Votre panier est vide</h2>
                <?php
            }
            else
            {
                ?>
                    <main class="md:min-h-[545px] md:p-4 md:grid md:grid-cols-2 md:relative">

                        <div id="conteneur-produit" class="flex flex-col">
                            <?php
                                foreach ($infoProduitsPanier as $i => $value) 
                                {
                                    ?>
                                        <div id="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>" class="bg-bleu p-2 md:p-4 m-4 shadow md:grid md:grid-cols-2">

                                            <!-- l'Image -->
                                            <div class="flex justify-center mb-3 md:mb-0">
                                                <a class="flex justify-center" href="/html/fo/details_produit.php?idProduit=<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                    <img src="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["url_photo"]; ?>" 
                                                    alt="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["alt"]; ?>" 
                                                    title="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["titre"]; ?>"
                                                    class="border self-center w-3/4 h-auto md:w-full md:h-auto">
                                                </a> 
                                            </div>
                                            
                                            <!-- Le conteneur des éléments liés au produit -->
                                            <div class="text-center md:flex md:flex-col md:justify-evenly">

                                                <div class="flex flex-col justify-center items-center mb-3 md:mb-0">
                                                    <h4> <?php echo $infoProduitsPanier[$i]["infoProduit"]["libelle_produit"]; ?></h4>
                                                    <p class="mt-2"> <?php echo affichageNote($infoProduitsPanier[$i]["infoProduit"]["note_moyenne"]); ?> </p>
                                                </div>
                                                
                                                <div id="prix">
                                                    <p class="mb-0.5 md:mb-0"> <?php echo "Prix unitaire : " . $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] . "€"; ?> </p>
                                                    <p class="mt-0.5 md:mt-0"> 
                                                        <?php
                                                            $prixTot = ($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"]);
                                                            $prixTot = number_format($prixTot, 2, '.', '');
                                                            echo "Prix total : " . $prixTot . "€"; 
                                                        ?> 
                                                    </p>
                                                </div>
                                                <div class="flex justify-center items-center mb-2 md:mb-0">
                                                    <form method="post" action="/php/retrait_panier.php" >
                                                        <input type="hidden" name="idProduit" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                        <input type="hidden" name="quantiteProd" value="<?php echo $infoProduitsPanier[$i]["quantiteProduit"]; ?>">
                                                        <input type="hidden" name="prixTTC" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"]; ?>">
                                                        <input type="hidden" name="quantiteTot" value="<?php echo $nbProduitsTotal; ?>">
                                                        <input type="hidden" name="prixTot" value="<?php echo $montantTotalTTC; ?>">
                                                        <input type="hidden" name="typeRetrait" value="decrement">

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
                                                
                                                <form class="mt-2 md:mt-0" method="post" action="/php/retrait_panier.php">
                                                    <input type="hidden" name="idProduit" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                    <input type="hidden" name="quantiteProd" value="<?php echo $infoProduitsPanier[$i]["quantiteProduit"]; ?>">
                                                    <input type="hidden" name="prixTTC" value="<?php echo $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"]; ?>">
                                                    <input type="hidden" name="quantiteTot" value="<?php echo $nbProduitsTotal; ?>">
                                                    <input type="hidden" name="prixTot" value="<?php echo $montantTotalTTC; ?>">
                                                    <input type="hidden" name="typeRetrait" value="suppression">

                                                    <button class="rounded-2xl border border-black w-48 h-16 self-center cursor-pointer hover:bg-rouge hover:text-white"
                                                    type="submit">
                                                        Supprimer du panier
                                                    </button>
                                                </form>
                                                
                                            </div>

                                        </div>
                                    <?php
                                }
                            ?>
                            

                            
                        </div>
                    
                        
                <?php
            }
        ?>
        
    
    
    <?php 
        if (!empty($produitsPanier))
        {
            ?>
                <div class="flex bottom-14 border-b md:border-none md:w-full md:grid md:grid-cols-3 fixed md:sticky md:bottom-64 pointer-events-none md:h-16 md:top-1/3">
                    <div></div>
                    <div id="conteneur-info_panier" class="flex w-full flex-wrap justify-evenly md:flex-nowrap bg-beige pb-4 pointer-events-auto md:grid md:grid-rows-4 md:justify-center md:items-center md:rounded-2xl" >
                        <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4">
                            <p class="mr-2">Nombre d'article : </p>
                            <p class="ml-2"> <?php echo $nbProduitsTotal; ?> </p>
                        </div>

                        <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4">
                            <p class="mr-2">Sous total :</p>
                            <p class="ml-2"> <?php echo $montantTotalTTC . "€"; ?> </p>
                        </div>

                        <form class="flex justify-center" method="post" action="/php/vider_panier.php">
                            <input type="hidden" name="typeVider" value="normal">
                            <button class="bg-beige rounded-2xl w-32 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer hover:bg-rouge hover:text-white border-black border shadow" type="submit">
                                Vider le panier
                            </button>
                        </form>
                        
                        <form class="flex justify-center" action="/html/fo/paiement.php">
                            <button class="bg-beige rounded-2xl w-20 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer hover:bg-bleu hover:text-vertFonce border-black border shadow" type="submit">
                                Acheter
                            </button>
                        </form>
                        
                    </div>
                    <div></div>
                </div>
            <?php
        }
    ?>
    
    </main>
    
    <?php include(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>
</html>