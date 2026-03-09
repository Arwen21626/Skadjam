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

                $rqt = $dbh->query("SELECT libelle_produit, id_vendeur, id_categorie  
                                    FROM sae3_skadjam._produit 
                                    WHERE id_produit = $idProd", PDO::FETCH_ASSOC);
                
                $infosProduits = $rqt->fetch();
                $idVendeurProd = $infosProduits["id_vendeur"];
                $libelleProd = $infosProduits["libelle_produit"];
                $idCategorie = $infosProduits["id_categorie"];
                
                if ($idVendeurProd == $idVendeur){

                    // echo $idProd . " " . $quantite . " " . $mois . " " . $annee . "<br>";

                    if (!isset($dataStats[$annee][$mois])){
                        $dataStats[$annee][$mois]["nb_ventes_totales"] = $quantite;
                    }
                    else {
                        $dataStats[$annee][$mois]["nb_ventes_totales"] += $quantite;
                    }

                    if (!isset($dataStats[$annee][$mois]["produits"][$idProd])){
                        $dataStats[$annee][$mois]["produits"][$idProd]["libelle_prod"] = $libelleProd;
                        $dataStats[$annee][$mois]["produits"][$idProd]["nb_ventes_totales"] = $quantite;
                        $dataStats[$annee][$mois]["produits"][$idProd]["id_categorie"] = $idCategorie;
                    }
                    else {
                        $dataStats[$annee][$mois]["produits"][$idProd]["nb_ventes_totales"] += $quantite;
                    }
                }
            }
        }
    }
?>

<pre>
    <!-- <?php print_r($dataStats); ?> -->
</pre>

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
            <div class="flex flex-row mb-2">
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

            <div id="graphique-produit" class="mt-4">
                <div class="ml-4">
                    <div class="mb-2">
                        <h3>Total des ventes du produit sélectionné :</h3>
                    </div>

                    <div class="flex flex-row mt-2">
                        <p class="pr-2">Choisissez un produit :</p>
                        <select name="" id="select-produit" class="pl-2 cursor-pointer">
                            <?php 
                                $cles = [];

                                foreach ($dataStats as $annee) { // Parcours toutes les années
                                    foreach ($annee as $mois) { // Parcours tous les mois
                                        $clesTemp = array_keys($mois["produits"]);

                                        foreach ($clesTemp as $key) { // Parcours tous les id des produits vendus lors d'un mois d'une année
                                            $cles[$key] = $mois["produits"][$key]["libelle_prod"];
                                        }
                                    }
                                }

                                foreach ($cles as $idProd => $libelle) {
                                    ?>
                                        <option class="cursor-pointer" value=<?php echo $idProd; ?>><?php echo $libelle; ?></option>
                                    <?php
                                }
                            ?>
                        </select>
                    </div>
                </div>


                <div class="chart-container flex justify-center items-center flex-col p-2">
                    <div class="chart flex justify-center items-center relative m-4 w-[60vw] h-[50vh]">
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