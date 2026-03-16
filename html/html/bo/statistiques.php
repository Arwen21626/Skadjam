<?php 
    session_start();
    include __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ . "/../../01_premiere_connexion.php";

    $idVendeur = $_SESSION["idCompte"];

    $dataStats = [];

    $rqt = $dbh->query("SELECT id_commande, date_commande FROM sae3_skadjam._commande", PDO::FETCH_ASSOC);
    $commandes = $rqt->fetchAll();

    if ($commandes) {
        
        foreach ($commandes as $commande) {
            
            $date = $commande["date_commande"];
            $mois = explode("/", $date)[1];
            $annee = trim(explode("/", $date)[2]);
            $idCommande = $commande["id_commande"];

            $rqt = $dbh->query("SELECT id_produit, quantite FROM sae3_skadjam._details WHERE id_commande = $idCommande", PDO::FETCH_ASSOC);
            $prodsInCommande = $rqt->fetchAll();

            foreach ($prodsInCommande as $produit) {
                
                $quantite = $produit["quantite"];
                $idProd = $produit["id_produit"];

                $rqt = $dbh->query("SELECT libelle_produit, id_vendeur, id_categorie, prix_ttc  
                                    FROM sae3_skadjam._produit 
                                    WHERE id_produit = $idProd", PDO::FETCH_ASSOC);
                
                $infosProduits = $rqt->fetch();
                $idVendeurProd = $infosProduits["id_vendeur"];
                $libelleProd = $infosProduits["libelle_produit"];
                $idCategorie = $infosProduits["id_categorie"];
                $prixTTC = $infosProduits["prix_ttc"];
                
                if ($idVendeurProd == $idVendeur){

                    // echo $idProd . " " . $quantite . " " . $mois . " " . $annee . "<br>";

                    if (!isset($dataStats[$annee][$mois])){
                        $dataStats[$annee][$mois]["nb_ventes_totales"] = $quantite;
                        $dataStats[$annee][$mois]["montant_total_ttc"] = number_format(($prixTTC * $quantite), 2, '.', '');
                    }
                    else {
                        $dataStats[$annee][$mois]["nb_ventes_totales"] += $quantite;
                        $dataStats[$annee][$mois]["montant_total_ttc"] += number_format(($prixTTC * $quantite), 2, '.', '');
                    }

                    if (!isset($dataStats[$annee][$mois]["produits"][$idProd])){
                        $dataStats[$annee][$mois]["produits"][$idProd]["libelle_prod"] = $libelleProd;
                        $dataStats[$annee][$mois]["produits"][$idProd]["nb_ventes_totales"] = $quantite;
                        $dataStats[$annee][$mois]["produits"][$idProd]["id_categorie"] = $idCategorie;
                        $dataStats[$annee][$mois]["produits"][$idProd]["montant_total_ttc"] = number_format(($prixTTC * $quantite), 2, '.', '');
                    }
                    else {
                        $dataStats[$annee][$mois]["produits"][$idProd]["nb_ventes_totales"] += $quantite;
                        $dataStats[$annee][$mois]["produits"][$idProd]["montant_total_ttc"] += number_format(($prixTTC * $quantite), 2, '.', '');
                    }
                }
            }
        }
    }
?>

<!-- <pre>
    <?php print_r($dataStats); ?>
</pre> -->

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stats</title>

    <script> 
        const dataJson = <?php echo json_encode($dataStats); ?>;
    </script>

</head>

<?php include __DIR__ . "/../../php/structure/head_back.php"; ?>

<body>
    <?php 
        require_once __DIR__ . "/../../php/structure/header_back.php";
        require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>

    <main class="m-4 pt-8 min-h-[420px] flex flex-col">

        <h2>Mes Statistiques</h2>

        <?php if($dataStats){?> 
            <div class="flex flex-row sticky top-[84px] justify-around mb-16 bg-white pt-4 pb-4 border-b z-10">
                <div class="flex flex-row">
                    <p class="pr-2">Choisissez une année :</p>
                    <select name="" id="select-annee" class="pl-2 cursor-pointer">
                        <?php 
                            $cles = array_keys($dataStats);
                            foreach ($cles as $annee) {
                                ?>

                                <option class="cursor-pointer" value=<?php echo $annee; ?>><?php echo $annee; ?></option>

                                <?php
                            }
                        ?>
                    </select>
                </div>

                <div class="flex flex-row">
                    <p class="pr-2">Format des statistiques de vente :</p>
                    <select name="" id="select-format">
                        <option value="volume">Volume</option>
                        <option value="euro">Montant (€-TTC)</option>
                    </select>
                </div>
            </div>

            <div class="flex flex-row justify-start">
                <div id="graphique-all" class="mb-4 border-r">
                    <div>
                        <div class="mt-2">
                            <h3 class="text-center">Total des ventes pour l'année sélectionnée</h3>
                        </div>
                    </div>


                    <div class="chart-container flex justify-center items-center flex-col p-2">
                        <div class="chart flex justify-center items-center relative m-4 w-[45vw] h-[50vh]">
                            <canvas id="all-chart"></canvas>
                        </div>
                    </div>
                </div>

                <div id="graphique-categorie" class="mb-4 border-l">
                    <div>
                        <div class="mt-2 text-wrap">
                            <h3 class="text-center text-wrap">Total des ventes par catégorie pour l'année sélectionnée</h3>
                        </div>
                    </div>

                    <div class="chart-container flex justify-center items-center flex-col p-2">
                        <div class="chart flex justify-center items-center relative m-4 w-[45vw] h-[45vh]">
                            <canvas id="categorie-chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div id="graphique-produit" class="mt-8">
                <div class="ml-4">
                    <div class="ml-8 mb-8">
                        <h3>Statistiques des produits :</h3>
                    </div>

                    <div class="flex flex-col mt-2">

                        <div id="prod-container" class="flex justify-around flex-row flex-wrap gap-4">
                            <?php 
                                $rqt = $dbh->query("SELECT sae3_skadjam._produit.id_produit, libelle_produit, sae3_skadjam._photo.url_photo, sae3_skadjam._photo.alt, sae3_skadjam._photo.titre FROM sae3_skadjam._produit 
                                                    INNER JOIN sae3_skadjam._montre ON sae3_skadjam._montre.id_produit = sae3_skadjam._produit.id_produit
                                                    INNER JOIN sae3_skadjam._photo ON sae3_skadjam._photo.id_photo = sae3_skadjam._montre.id_photo
                                                    WHERE id_vendeur = $idVendeur", PDO::FETCH_ASSOC);
                                $cles = $rqt->fetchAll();

                                foreach ($cles as $prod) {
                                        ?>
                                            <!-- Div représentant une carte produit -->
                                            <div title="Afficher le graphique" id="<?php echo $prod["id_produit"]; ?>" class="produit bg-bleu flex flex-col w-80 h-auto p-3 m-2 cursor-pointer">
                                                <!--affichage de la photo-->
                                                <img class="w-auto h-40 md:h-80 mx-auto block" 
                                                    src="<?php echo $prod['url_photo'];?>" 
                                                    alt="<?php echo $prod['alt'];?>"
                                                >
                                                        
                                                <!--affichage du nom du produit-->
                                                <p class="text-center mt-8 mb-4 max-w-70"><?php echo $prod['libelle_produit'];?></p>
                                            </div>
                                        <?php
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div id="overlay-chart" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40 cursor-pointer" title="Fermer"></div>

                <div id="container-prod-chart" title="Fermer"
                class="hidden fixed bg-white top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 chart-container justify-center items-center flex-col p-2 m-12 z-50 cursor-pointer">
                    <div id="div-prod-chart" class="chart flex justify-center items-center relative m-4 w-[60vw] h-[50vh]">
                        <canvas id="prod-chart"></canvas>
                    </div>
                </div>
            </div>

        <?php } else { ?>

            <div class="flex justify-center items-center mt-8">
                <h3 class="text-center">Aucune statistique de ventes enregistrée</h3>
            </div>

        <?php } ?>
        
    </main>
    
    <?php 
        require_once __DIR__ . "/../../php/structure/footer_back.php";
    ?>
</body>

<script src="/js/chart.umd.js"></script>
<script type="module" src="/js/bo/stats.js"></script>

</html>