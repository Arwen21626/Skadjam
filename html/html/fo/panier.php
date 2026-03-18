<?php 
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    require_once(__DIR__ . "/../../php/fonctions.php");

    $peutValider = true;

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

                $rqt = $dbh->query("SELECT r.id_remise, r.pourcentage_remise, p.id_produit, p.prix_remise
                                    FROM sae3_skadjam._produit p
                                        LEFT JOIN sae3_skadjam._reduit rd ON rd.id_produit = p.id_produit
                                        LEFT JOIN sae3_skadjam._remise r ON r.id_remise = rd.id_remise
                                    WHERE p.id_produit = ".$produitsPanier[$i]["id_produit"], PDO::FETCH_ASSOC);
                $infoRemise = $rqt->fetch();

                $infoProduitsPanier[$i]["infoProduit"] = $infoProduit;
                $infoProduitsPanier[$i]["infoPhoto"] = $infoPhoto;
                $infoProduitsPanier[$i]["infoRemise"] = $infoRemise;

                if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] > $produitsPanier[$i]["quantite_par_produit"])    
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

                    if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] == 0) {
                        $peutValider = false;
                    }
                }
                

                $montantTotalTTC += $infoProduitsPanier[$i]["infoRemise"]["prix_remise"] * $infoProduitsPanier[$i]["quantiteProduit"];
                $nbProduitsTotal += $infoProduitsPanier[$i]["quantiteProduit"];
            }

            

            //Mise à jour des attributs nb_produit_total & montant_total_ttc du panier
            $dbh->query("UPDATE sae3_skadjam._panier SET nb_produit_total = $nbProduitsTotal, montant_total_ttc = $montantTotalTTC WHERE id_panier = $idPanier");
        }
        
        $lienBtnValiderPanier = "/html/fo/recapitulatif_commande.php";
    }
    else if ($_SESSION['role'] === 'visiteur') 
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

            $rqt = $dbh->query("SELECT r.id_remise, r.pourcentage_remise, p.id_produit, p.prix_remise
                                    FROM sae3_skadjam._produit p
                                        LEFT JOIN sae3_skadjam._reduit rd ON rd.id_produit = p.id_produit
                                        LEFT JOIN sae3_skadjam._remise r ON r.id_remise = rd.id_remise
                                    WHERE p.id_produit = ".$prod['id'], PDO::FETCH_ASSOC);
            $infoRemise = $rqt->fetch();

            $infoProduitsPanier[$i]["infoProduit"] = $infoProduit;
            $infoProduitsPanier[$i]["infoPhoto"] = $infoPhoto;
            $infoProduitsPanier[$i]["infoRemise"] = $infoRemise;

            if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] > $prod["quantite_par_produit"])    
            {
                $infoProduitsPanier[$i]["quantiteProduit"] = $prod["quantite_par_produit"];
            }
            else
            {
                $infoProduitsPanier[$i]["quantiteProduit"] = $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];
                $_SESSION['panier']['contient'][$i]['quantite_par_produit'] = $infoProduitsPanier[$i]['infoProduit']['quantite_stock'];  

                
                if ($infoProduitsPanier[$i]["infoProduit"]["quantite_stock"] == 0) {

                    $peutValider = false;
                }
            }

            $montantTotalTTC += $infoProduitsPanier[$i]["infoRemise"]["prix_remise"] * $infoProduitsPanier[$i]["quantiteProduit"];
            $nbProduitsTotal += $infoProduitsPanier[$i]["quantiteProduit"];
        }

        //Mise à jour des attributs nb_produit_total & montant_total_ttc du panier
        $_SESSION['panier']['nb_produit_total'] = $nbProduitsTotal;
        $_SESSION['panier']['montant_total_ttc'] = $montantTotalTTC;

        $lienBtnValiderPanier = "/html/fo/connexion.php";
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php include(__DIR__ . "/../../php/structure/head_front.php");?>
    <title>Panier</title>
</head>
<body
    <?php 
        if ($_SESSION['role'] === 'client' && !empty($produitsPanier)){
            echo 'class="pb-28 md:pb-0"';
        }
        else if ($_SESSION['role'] === 'visiteur' && $_SESSION['panier']['nb_produit_total'] > 0) {
            echo 'class="pb-28 md:pb-0"';
        }
    ?>
>
    <?php include(__DIR__ . "/../../php/structure/header_front.php") ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php") ?>

    <div id="popup-overlay" class="right-12 md:right-40">
        <div id="popup-modif-panier" class="popup p-2 border-vertFonce shadow-xl">
            <p>Vos modifications ont bien été enregistrées !</p>
            <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
        </div>

        <div id="popup-erreur-valider-panier" class="popup p-2 border-vertFonce shadow-xl">
            <p>Vous ne pouvez pas valider votre panier car un ou plusieurs produits sont hors-stock.</p>
            <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
        </div>
    </div>

    <div id="overlay-valider" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

    <div id="container-div-valider" 
    class="hidden fixed bg-white top-1/4 left-8 md:top-1/2 md:left-1/2 md:-translate-x-1/2 md:-translate-y-1/2 chart-container justify-center items-center flex-col p-2 m-12 z-50">
        <div id="div-form-valider" class="flex flex-col justify-center items-center relative m-4 w-[50vw] h-[20vh] md:h-[25vh] md:w-[30vw] ">
            <p class="mb-4 text-center">Voulez-vous vraiment vider votre panier ?</p>
            <form class="flex justify-center" method="get" action="./../../php/vider_panier.php">
                <input type="hidden" name="typeVider" value="normal">
                <div>
                    <button id="cancelButton" class="bg-beige rounded-2xl w-20 h-10 mt-2 md:w-40 md:h-14 md:mt-4 mr-2 cursor-pointer border-black border shadow" type="button">
                        Annuler
                    </button>
                    <button class="bg-beige rounded-2xl w-20 h-10 mt-2 md:w-40 md:h-14 md:mt-4 ml-2 cursor-pointer border-black border shadow" type="submit">
                        OK
                    </button>
                </div>
                
            </form>
        </div>
    </div>
    
        <?php
            
            if (empty($infoProduitsPanier)) {
                ?>
                    <main class="min-h-[420px] md:min-h-[620px] md:p-4 flex justify-center">
                        <h2 class="md:text-center self-center">Votre panier est vide</h2>
                <?php
                }
                else
                {
                ?>
                    <main class="min-h-[420px] md:min-h-[620px] md:p-4 md:grid md:grid-cols-2 md:relative">

                        <section id="conteneur-produit" class="flex flex-col">
                            <?php
                                foreach ($infoProduitsPanier as $i => $value) 
                                {
                                    ?>
                                        <!-- Balise représentant une card produit -->
                                        <article id="<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>" class="bg-bleu pt-4 pb-4 md:p-4 m-4 shadow md:grid md:grid-cols-2 produit">

                                            <!-- l'Image -->
                                            <section class="flex justify-center mb-3 md:mb-0 relative image">
                                                <a class="flex justify-center" href="/html/fo/details_produit.php?idProduit=<?php echo $infoProduitsPanier[$i]["infoProduit"]["id_produit"]; ?>">
                                                    <img src="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["url_photo"]; ?>" 
                                                    alt="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["alt"]; ?>" 
                                                    title="<?php echo $infoProduitsPanier[$i]["infoPhoto"]["titre"]; ?>"
                                                    class="border self-center w-3/4 h-auto md:w-full md:h-auto">
                                                </a> 
                                            </section>
                                                
                                            <!-- Le conteneur des éléments liés au produit -->
                                            <section class="text-center gap-4 md:flex md:flex-col md:justify-evenly prod-info stock:<?php echo $infoProduitsPanier[$i]["infoProduit"]["quantite_stock"];?>">

                                                <div class="flex flex-col justify-center items-center mb-3 md:mb-0">
                                                    <h4> <?php echo $infoProduitsPanier[$i]["infoProduit"]["libelle_produit"]; ?></h4>
                                                    <p class="mt-2"> <?php echo affichageNote($infoProduitsPanier[$i]["infoProduit"]["note_moyenne"]); ?></p>
                                                </div>
                                                    
                                                <div class="prix">
                                                    <p class="inline-block">Prix unitaire :</p>
                                                    <p class="<?php echo ($infoProduitsPanier[$i]['infoRemise']['id_remise'] !== NULL)?'line-through':'';?> inline-block"> <?php echo htmlentities(str_replace(".", ",",$infoProduitsPanier[$i]['infoProduit']['prix_ttc'])); ?>€</p> 
                                                    <p class="<?php echo ($infoProduitsPanier[$i]['infoRemise']['id_remise'] !== NULL)?'inline-block':' hidden';?> prix-u pl-3"><?php echo htmlentities(str_replace(".", ",",$infoProduitsPanier[$i]['infoRemise']['prix_remise'])); ?>€ </p>
                                                    <p class="inline-block">(<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                                    
                                                    <p class="mt-0.5 md:mt-0 prix-tot"> 
                                                        <?php
                                                            $prixTot = ($infoProduitsPanier[$i]["infoRemise"]["prix_remise"] * $infoProduitsPanier[$i]["quantiteProduit"]);
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

                                            </section>

                                        </article>
                                    <?php
                                }
                                ?>     
                        </section>   
                    <?php
                
                if ($_SESSION['role'] === 'client') { 
                    ?>
                        <section class="flex bottom-14 border-b md:border-none md:w-full md:grid md:grid-cols-1 lg:grid-cols-3 fixed md:sticky md:bottom-64 pointer-events-none md:h-16 md:top-1/3">
                            <div></div>
                            <aside id="conteneur-info_panier" class="flex w-full flex-wrap justify-evenly md:flex-nowrap bg-beige pb-4 pointer-events-auto md:grid md:grid-rows-4 md:justify-center md:items-center md:rounded-2xl" >
                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 nb-prod-total">
                                    <p class="mr-2">Nombre d'article : </p>
                                    <p class="ml-2"><?php echo $nbProduitsTotal; ?></p>
                                </div>

                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 sous-total">
                                    <p class="mr-2">Sous total :</p>
                                    <p class="ml-2"><?php echo number_format($montantTotalTTC, 2, ',', '') . "€"; ?></p>
                                </div>

                                <div class="flex justify-center">
                                    <button id="viderPanier" class="bg-beige rounded-2xl w-32 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow">
                                        Vider le panier
                                    </button>
                                </div>
                                
                                <?php 
                                    if ($peutValider) 
                                    {
                                        ?>
                                            <form class="flex justify-center valider-panier" method="get" action="<?php echo $lienBtnValiderPanier;?>">
                                                <input type="hidden" name="idPanier" value="<?= htmlspecialchars($idPanier) ?>">
                                                <button class="bg-beige rounded-2xl w-56 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                                    Valider le panier
                                                </button>
                                            </form>
                                        <?php
                                    }
                                    else if (!$peutValider)
                                    {
                                        ?>
                                            <div class="flex justify-center valider-panier-div">
                                                <button id="btnValiderPanier" class="bg-rouge text-gray-300 rounded-2xl w-56 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow">
                                                Valider le panier
                                                </button>
                                            </div>
                                        <?php
                                    }
                                ?>
                                
                            </aside>
                        </section>

                    <?php
                }
                else if ($_SESSION['role'] === 'visiteur') {
                    ?>
                        <section class="flex bottom-14 border-b md:border-none md:w-full md:grid md:grid-cols-1 lg:grid-cols-3 fixed md:sticky md:bottom-64 pointer-events-none md:h-16 md:top-1/3">
                            <div></div>
                            <aside id="conteneur-info_panier" class="flex w-full flex-wrap justify-evenly md:flex-nowrap bg-beige pb-4 pointer-events-auto md:grid md:grid-rows-4 md:justify-center md:items-center md:rounded-2xl" >
                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 nb-prod-total">
                                    <p class="mr-2">Nombre d'article : </p>
                                    <p class="ml-2"><?php echo $_SESSION['panier']['nb_produit_total']; ?></p>
                                </div>

                                <div class="inline-flex mt-2 mb-2 md:mb-0 md:mt-4 sous-total">
                                    <p class="mr-2">Sous total :</p>
                                    <p class="ml-2"><?php echo number_format($_SESSION['panier']['montant_total_ttc'], 2, ',', '') . "€";?></p>
                                </div>

                                <div class="flex justify-center">
                                    <button id="viderPanier" class="bg-beige rounded-2xl w-32 h-10 mt-2 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow">
                                        Vider le panier
                                    </button>
                                </div>
                                
                                <?php 
                                    if ($peutValider) 
                                    {
                                        ?>
                                            <form class="flex justify-center valider-panier" method="get" action="<?php echo $lienBtnValiderPanier;?>">
                                                <input type="hidden" name="idPanier" value="<?= htmlspecialchars($idPanier) ?>">
                                                <button class="bg-beige rounded-2xl w-56 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow" type="submit">
                                                    Valider le panier
                                                </button>
                                            </form>
                                        <?php
                                    }
                                    else if (!$peutValider)
                                    {
                                        ?>
                                            <div class="flex justify-center valider-panier-div">
                                                <button id="btnValiderPanier" class="bg-rouge text-gray-300 rounded-2xl w-56 p-1 h-10 mt-2 md:p-0 md:w-40 md:h-14 md:mt-4 cursor-pointer border-black border shadow">
                                                Valider le panier
                                                </button>
                                            </div>
                                        <?php
                                    }
                                ?>
                                
                                </aside>
                            <div></div>
                        </section>
                    <?php
                }
            }
    ?>
    
                    </main>
    
    <?php include(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>

<script type="module" src="/js/fo/panier.js"></script>

</html>