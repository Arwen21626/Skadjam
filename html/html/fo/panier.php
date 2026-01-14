<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    require_once(__DIR__ . "/../../php/fonctions.php");

    if ($_SESSION["role"] === "client") 
    {

        $idClient = $_SESSION["idCompte"];

        $rqt = $dbh->query("SELECT * FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
        $infoPanier = $rqt->fetch();

        $idPanier = $infoPanier["id_panier"];
        $nbProduitsTotal = 0;
        $montantTotalTTC = 0;

        $produitsPanier = array();

        foreach ($dbh->query("SELECT id_produit, quantite_par_produit
                            FROM sae3_skadjam._contient
                            WHERE id_panier = $idPanier
                            ORDER BY id_produit ASC") as $row) {
            $produitsPanier[] = $row;
        }
        

        if (!empty($produitsPanier)) 
        {
            $infoProduitsPanier = array();

            foreach ($produitsPanier as $i => $value) 
            { 
                
                $rqt = $dbh->query("SELECT id_produit, libelle_produit, prix_ttc, note_moyenne, quantite_stock
                                    FROM sae3_skadjam._produit WHERE id_produit = " . $produitsPanier[$i]["id_produit"], PDO::FETCH_ASSOC);
                $infoProduit = $rqt->fetch();

                $rqt = $dbh->query("SELECT id_photo FROM sae3_skadjam._montre WHERE id_produit = " . $produitsPanier[$i]["id_produit"], PDO::FETCH_ASSOC);
                $idPhoto = $rqt->fetch()["id_photo"];

                $rqt = $dbh->query("SELECT url_photo, alt, titre FROM sae3_skadjam._photo WHERE id_photo = $idPhoto", PDO::FETCH_ASSOC);
                $infoPhoto = $rqt->fetch();

                $infoProduitsPanier[$i]["infoProduit"] = $infoProduit;
                $infoProduitsPanier[$i]["infoPhoto"] = $infoPhoto;

                if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] >= $produitsPanier[$i]["quantite_par_produit"])    
                {
                    $infoProduitsPanier[$i]["quantiteProduit"] = $produitsPanier[$i]["quantite_par_produit"];
                }
                else
                {
                    $infoProduitsPanier[$i]["quantiteProduit"] = $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];
                    
                    $rqt = $dbh->prepare("UPDATE sae3_skadjam._contient SET
                                          quantite_par_produit = ?
                                          WHERE id_panier = ? AND id_produit = ?");
                    $rqt->execute([$infoProduitsPanier[$i]['infoProduit']['quantite_stock'], $idPanier, $infoProduit["id_produit"]]);
                }
                

                $montantTotalTTC += $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"];
                $nbProduitsTotal += $infoProduitsPanier[$i]["quantiteProduit"];
            }

            

            //Mise à jour des attributs nb_produit_total & montant_total_ttc du panier
            $dbh->query("UPDATE sae3_skadjam._panier SET nb_produit_total = $nbProduitsTotal, montant_total_ttc = $montantTotalTTC WHERE id_panier = $idPanier");
        }
        
        $lienBtnValiderPanier = "/html/fo/recapitulatif_commande.php";
    }
    else if ($_SESSION['role'] === 'visiteur' && $_SESSION['panier']['nb_produit_total'] > 0) 
    {
        $infoProduitsPanier = array();
        $nbProduitsTotal = 0;
        $montantTotalTTC = 0;


        foreach ($_SESSION['panier']['contient'] as $i => $prod) 
        {
            $rqt = $dbh->query("SELECT id_produit, libelle_produit, prix_ttc, note_moyenne, quantite_stock
                                    FROM sae3_skadjam._produit WHERE id_produit = " . $prod['id'], PDO::FETCH_ASSOC);
            $infoProduit = $rqt->fetch(); 
            
            $rqt = $dbh->query("SELECT id_photo FROM sae3_skadjam._montre WHERE id_produit = " . $prod['id'], PDO::FETCH_ASSOC);
            $idPhoto = $rqt->fetch()["id_photo"];

            $rqt = $dbh->query("SELECT url_photo, alt, titre FROM sae3_skadjam._photo WHERE id_photo = $idPhoto", PDO::FETCH_ASSOC);
            $infoPhoto = $rqt->fetch();

            $infoProduitsPanier[$i]["infoProduit"] = $infoProduit;
            $infoProduitsPanier[$i]["infoPhoto"] = $infoPhoto;

            if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] >= $prod["quantite_par_produit"])    
            {
                $infoProduitsPanier[$i]["quantiteProduit"] = $prod["quantite_par_produit"];
            }
            else
            {
                $infoProduitsPanier[$i]["quantiteProduit"] = $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];
                $_SESSION['panier']['contient'][$i]['quantite_par_produit'] = $infoProduitsPanier[$i]['infoProduit']['quantite_stock'];  
            }

            $infoProduitsPanier[$i]["quantiteProduit"] = $prod["quantite_par_produit"];

            $montantTotalTTC += $infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"];
            $nbProduitsTotal += $infoProduitsPanier[$i]["quantiteProduit"];
        }

        //Mise à jour des attributs nb_produit_total & montant_total_ttc du panier
        $_SESSION['panier']['nb_produit_total'] = $nbProduitsTotal;
        $_SESSION['panier']['montant_total_ttc'] = $montantTotalTTC;

        $lienBtnValiderPanier = "/html/fo/connexion.php";
    }
?>

<!-- <pre>
    
</pre> -->

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
            if ($_SESSION['role'] === 'client') {

                if (empty($produitsPanier)) {
                    ?>
                        <main class="min-h-[360px] md:min-h-[620px] md:p-4 flex justify-center">
                            <h2 class="md:text-center self-center">Votre panier est vide</h2>
                    <?php
                }
                else
                {
                    ?>
                        <main class="md:min-h-[620px] md:p-4 md:grid md:grid-cols-2 md:relative">

                            <div id="conteneur-produit" class="flex flex-col">
                                <?php
                                    foreach ($infoProduitsPanier as $i => $value) 
                                    {
                                        ?>
                                            <!-- Balise représentant une card produit -->
                                            <div id="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>" class="bg-bleu p-2 md:p-4 m-4 shadow md:grid md:grid-cols-2 produit">

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
                                                <div class="text-center md:flex md:flex-col md:justify-evenly prod-info stock:<?php echo $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];?>">

                                                    <div class="flex flex-col justify-center items-center mb-3 md:mb-0">
                                                        <h4> <?php echo $infoProduitsPanier[$i]["infoProduit"]["libelle_produit"]; ?></h4>
                                                        <p class="mt-2"> <?php echo affichageNote($infoProduitsPanier[$i]["infoProduit"]["note_moyenne"]); ?></p>
                                                    </div>
                                                    
                                                    <div class="prix">
                                                        <p class="mb-0.5 md:mb-0 prix-u"> <?php echo "Prix unitaire : " . number_format($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"], 2, ',', '') . "€"; ?></p>
                                                        <p class="mt-0.5 md:mt-0 prix-tot"> 
                                                            <?php
                                                                $prixTot = ($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"]);
                                                                $prixTot = number_format($prixTot, 2, ',', '');
                                                                echo "Prix total : " . $prixTot . "€"; 
                                                            ?>
                                                        </p>
                                                    </div>

                                                    <div class="flex justify-center items-center mb-2 md:mb-0 quantite-prod">
                                                        

                                                        <button class="text-4xl text-center mr-4 cursor-pointer hover:text-rouge retrait"
                                                        type="button">
                                                            -
                                                        </button>
                                                        

                                                        <input type="text" 
                                                        value="<?php echo $infoProduitsPanier[$i]["quantiteProduit"]; ?>"
                                                        class="w-16 ml-4 mr-4 text-center border rounded-sm quantite-prod-input">

                                                        
                                                        <button class="text-4xl text-center ml-4 cursor-pointer hover:text-vertClair ajout"
                                                        type="button">
                                                            +
                                                        </button>
                                                        
                                                    </div>
                                                                                                        
                                                    <button class="rounded-2xl border border-black w-48 h-16 self-center cursor-pointer supprimer"
                                                    type="submit">
                                                        Supprimer du panier
                                                    </button>

                                                </div>

                                            </div>
                                        <?php
                                    }
                                ?>     
                        </div>
                        
                        <div class="flex bottom-14 border-b md:border-none md:w-full md:grid md:grid-cols-1 lg:grid-cols-3 fixed md:sticky md:bottom-64 pointer-events-none md:h-16 md:top-1/3">
                            <div></div>
                            <div id="conteneur-info_panier" class="flex w-full flex-wrap justify-evenly md:flex-nowrap bg-beige pb-4 pointer-events-auto md:grid md:grid-rows-4 md:justify-center md:items-center md:rounded-2xl" >
                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 nb-prod-total">
                                    <p class="mr-2">Nombre d'article : </p>
                                    <p class="ml-2"><?php echo $nbProduitsTotal; ?></p>
                                </div>

                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 sous-total">
                                    <p class="mr-2">Sous total :</p>
                                    <p class="ml-2"><?php echo number_format($montantTotalTTC, 2, ',', '') . "€"; ?></p>
                                </div>

                                <form class="flex justify-center" method="get" action="/php/vider_panier.php">
                                    <input type="hidden" name="typeVider" value="normal">
                                    <button class="bg-beige rounded-2xl w-32 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                        Vider le panier
                                    </button>
                                </form>
                                
                                <form class="flex justify-center valider-panier" method="get" action="<?php echo $lienBtnValiderPanier;?>">
                                    <input type="hidden" name="idPanier" value="<?= htmlspecialchars($idPanier) ?>">
                                    <button class="bg-beige rounded-2xl w-28 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                        Valider le panier
                                    </button>
                                </form>
                                
                            </div>
                        </div>
                    <?php
                }
            }
            else if ($_SESSION['role'] === 'visiteur') {

                if ($_SESSION['panier']['nb_produit_total'] === 0) {
                    ?>
                        <main class="min-h-[360px] md:min-h-[620px] md:p-4 flex justify-center">
                            <h2 class="md:text-center self-center">Votre panier est vide</h2>
                    <?php
                }
                else
                {
                    ?>
                        <main class="md:min-h-[620px] md:p-4 md:grid md:grid-cols-2 md:relative">

                            <div id="conteneur-produit" class="flex flex-col">
                                <?php
                                    foreach ($infoProduitsPanier as $i => $value) 
                                    {
                                        ?>
                                            <!-- Balise représentant une card produit -->
                                            <div id="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>" class="bg-bleu p-2 md:p-4 m-4 shadow md:grid md:grid-cols-2 produit">

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
                                                <div class="text-center md:flex md:flex-col md:justify-evenly prod-info stock:<?php echo $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];?>">

                                                    <div class="flex flex-col justify-center items-center mb-3 md:mb-0">
                                                        <h4> <?php echo $infoProduitsPanier[$i]["infoProduit"]["libelle_produit"]; ?></h4>
                                                        <p class="mt-2"> <?php echo affichageNote($infoProduitsPanier[$i]["infoProduit"]["note_moyenne"]); ?></p>
                                                    </div>
                                                    
                                                    <div class="prix">
                                                        <p class="mb-0.5 md:mb-0 prix-u"><?php echo "Prix unitaire : " . number_format($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"], 2, ',', '') . "€"; ?></p>
                                                        <p class="mt-0.5 md:mt-0 prix-tot"> 
                                                            <?php
                                                                $prixTot = ($infoProduitsPanier[$i]["infoProduit"]["prix_ttc"] * $infoProduitsPanier[$i]["quantiteProduit"]);
                                                                $prixTot = number_format($prixTot, 2, ',', '');
                                                                echo "Prix total : " . $prixTot . "€"; 
                                                            ?>
                                                        </p>
                                                    </div>

                                                    <div class="flex justify-center items-center mb-2 md:mb-0 quantite-prod">
                                                        

                                                        <button class="text-4xl text-center mr-4 cursor-pointer hover:text-rouge retrait"
                                                        type="button">
                                                            -
                                                        </button>
                                                        

                                                        <input type="text" 
                                                        value="<?php echo $infoProduitsPanier[$i]["quantiteProduit"]; ?>"
                                                        class="w-16 ml-4 mr-4 text-center quantite-prod-input">

                                                        
                                                        <button class="text-4xl text-center ml-4 cursor-pointer hover:text-vertClair ajout"
                                                        type="button">
                                                            +
                                                        </button>
                                                        
                                                    </div>

                                                    <button class="rounded-2xl border border-black w-48 h-16 self-center cursor-pointer supprimer"
                                                    type="submit">
                                                        Supprimer du panier
                                                    </button>
                                                    
                                                </div>

                                            </div>
                                        <?php
                                    }
                                ?>     
                            </div>
                        
                            <div class="flex bottom-14 border-b md:border-none md:w-full md:grid md:grid-cols-1 lg:grid-cols-3 fixed md:sticky md:bottom-64 pointer-events-none md:h-16 md:top-1/3">
                                <div></div>
                                <div id="conteneur-info_panier" class="flex w-full flex-wrap justify-evenly md:flex-nowrap bg-beige pb-4 pointer-events-auto md:grid md:grid-rows-4 md:justify-center md:items-center md:rounded-2xl" >
                                    <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 nb-prod-total">
                                        <p class="mr-2">Nombre d'article : </p>
                                        <p class="ml-2"><?php echo $_SESSION['panier']['nb_produit_total']; ?></p>
                                    </div>

                                    <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 sous-total">
                                        <p class="mr-2">Sous total :</p>
                                        <p class="ml-2"><?php echo number_format($_SESSION['panier']['montant_total_ttc'], 2, ',', '') . "€";?></p>
                                    </div>

                                    <form class="flex justify-center" method="get" action="/php/vider_panier.php">
                                        <input type="hidden" name="typeVider" value="normal">
                                        <button class="bg-beige rounded-2xl w-32 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                            Vider le panier
                                        </button>
                                    </form>
                                    
                                    <form class="flex justify-center valider-panier" method="get" action="<?php echo $lienBtnValiderPanier;?>">
                                        <input type="hidden" name="veutAcheter" value="V">
                                        <button class="bg-beige rounded-2xl w-28 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                            Valider le panier
                                        </button>
                                    </form>
                                    
                                </div>
                                <div></div>
                            </div>
                    <?php
                }
            }
    ?>
    
    </main>
    
    <?php include(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>

<script src="/js/fo/panier.js"></script>

</html>