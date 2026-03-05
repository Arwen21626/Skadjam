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

                $rqt = $dbh->query("SELECT id_vendeur FROM sae3_skadjam._produit WHERE id_produit = $idProd", PDO::FETCH_ASSOC);
                $idVendeurProd = $rqt->fetch()["id_vendeur"];

                if ($idVendeurProd == $idVendeur){

                    echo $idProd . " " . $quantite . " " . $mois . " " . $annee . "<br>";

                    if (!isset($dataStats[$annee][$mois])){
                        $dataStats[$annee][$mois]["nb_ventes_totales"] = $quantite;
                    }
                    else {
                        $dataStats[$annee][$mois]["nb_ventes_totales"] += $quantite;
                    }
                    // $dataStats[$date]["nb_ventes_totales"] += $quantite;
                }
            }
        }
    }
?>

<pre>
    <?php print_r($dataStats); ?>
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

    <main class="m-4">

        <h2>Mes Statistiques</h2>

        <div class="charts-containers flex justify-center items-center flex-col p-2">
            <div class="chart-container flex justify-center items-center relative m-4 w-[60vw] h-[50vh]">
                <canvas class="" id="testChart"></canvas>
            </div>
        </div>
    </main>
    
    <?php 
        require_once __DIR__ . "/../../php/structure/footer_back.php";
    ?>
</body>

<script src="/js/chart.umd.js"></script>
<script type="module" src="/js/bo/stats.js"></script>

</html>