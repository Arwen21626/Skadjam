<?php 
    include __DIR__ . '/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../../connections_params.php";
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . "/../../php/modification_variable.php";
    const PAGE_SIZE = 24;
    session_start();

    if (!isset($_SESSION['role'])) {
        $_SESSION['role'] = "visiteur";
        $_SESSION['panier'] = ["nb_produit_total" => 0, // Utilisation des noms de colonne utilisées dans la BDD
                               "montant_total_ttc" => 0,
                               "contient" => []]; //format du tableau représentant un produit : ['id' => 25, 'quantite_par_produit' => 2]
    }

    require_once __DIR__ . "/../../php/verif_role_fo.php";
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_front.php";?>
<head> 
    <title>Promotions</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>



<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px] p-8">
        <!--Début du catalogue-->
        <h2 class="m-0">Nos produits les plus vendus</h2>

        <?php
            $pageNumber = 1;

            $tabProduit = [];

            try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT pr.libelle_produit,
                                    pr.id_produit,
                                    ph.url_photo,
                                    ph.alt,
                                    ph.titre,
                                    pr.prix_ttc,
                                    pr.quantite_stock,
                                    pr.note_moyenne,
                                    pr.prix_remise,
                                    r.pourcentage_remise,
                                    ventes.total_vendus
                                FROM (
                                        SELECT id_produit, SUM(quantite) AS total_vendus
                                        FROM sae3_skadjam._details
                                        GROUP BY id_produit
                                    ) ventes
                                JOIN sae3_skadjam._produit pr
                                    ON pr.id_produit = ventes.id_produit
                                INNER JOIN sae3_skadjam._montre m
                                    ON pr.id_produit = m.id_produit
                                INNER JOIN sae3_skadjam._photo ph  
                                    ON ph.id_photo = m.id_photo
                                LEFT JOIN sae3_skadjam._reduit rd
                                    ON rd.id_produit = pr.id_produit
                                LEFT JOIN sae3_skadjam._remise r
                                    ON r.id_remise = rd.id_remise
                                WHERE pr.est_supprime = false AND pr.est_masque = false
                                ORDER BY ventes.total_vendus DESC
                                LIMIT 16;", PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);
                
                //affiche la photo du produit, son nom, son prix et sa note ?>
                <article class="flex flex-row flex-wrap justify-around">
                    <?php foreach($lignes as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];
                        // Le produit est-il en promotion ?
                        $stmt = $dbh->prepare("SELECT *
                                            FROM sae3_skadjam._promu
                                            WHERE id_produit = :id_produit");
                        $stmt->execute([':id_produit' => $idProduit]);
                        $estPromu = ($stmt->fetch() !== false); ?>
                        <section class="bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3">
                            <!--affichage de la photo-->
                            <a href= "<?= 'html/fo/details_produit.php?idProduit='.$idProduit;?>" class="mb-3">
                                <img src="<?= $valeurs['url_photo'];?>" 
                                        alt="<?= $valeurs['alt'];?>"
                                        title="<?= $valeurs['titre'];?>"
                                        class="w-auto h-40 md:h-80 block mx-auto">

                                <!--affichage du nom du produit-->
                                <p><?= $valeurs['libelle_produit'];?></p> 

                                <!--affichage du prix du produit-->   
                                <div class="flex flex-row justify-between items-center">
                                    <p class="inline-block <?= ($valeurs['pourcentage_remise'] !== NULL)?'line-through':'';?>"> <?= htmlentities(str_replace(".", ",",$valeurs['prix_ttc'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                    <p class=" pl-3 <?= ($valeurs['pourcentage_remise'] !== NULL)?'':'hidden';?>"> <?= htmlentities(str_replace(".", ",",$valeurs['prix_remise'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                </div>
                                <!--récupération de la note-->
                                <div class="flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div>
                            </a>
                            <!--affichage de la promotion-->
                                <?php if($estPromu){ 
                                    $stmt = $dbh->prepare("SELECT *
                                                            FROM sae3_skadjam._promu pu
                                                            INNER JOIN sae3_skadjam._promotion pn
                                                                ON pu.id_promotion = pn.id_promotion
                                                            WHERE pu.id_produit = :id_produit");
                                    $stmt->execute([':id_produit' => $idProduit]);
                                    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $debutPromo = formatDate($promotion['date_debut_promotion']);
                                    $finPromo = null;
                                    if($promotion['date_fin_promotion'] !== null){
                                        $finPromo = formatDate($promotion['date_fin_promotion']);
                                    }
                                    $labelPromo = $promotion['label'];
                                    if($debutPromo <= date('Y-m-d') && ($finPromo == null || $finPromo >= date('Y-m-d')) && !empty($labelPromo)){
                                ?>
                                <div class="bg-rouge absolute w-36 md:w-74 underline text-beige pt-2 pb-1.5">
                                    <h4 class="text-center text-beige overline m-0"><?= htmlspecialchars($labelPromo); ?></h4>
                                </div>
                                <?php }} ?>
                            </section>
                    <?php } ?>
                </article>
                <?php $dbh = null;
            }catch(PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }

            ?>
    </main>
    

    <!--footer-->
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>

</body>

</html>