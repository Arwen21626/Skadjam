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

// Récupération des futurs achats et du panier du client
include __DIR__. '/../../php/requetesBDD/recup_FA.php';
include __DIR__. '/../../php/requetesBDD/recup_panier.php';
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_front.php";?>
<head> 
    <title>Promotions</title>
</head>



<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px] p-8">
        <!--Début du catalogue-->
        <h2 id="vosProduits">Nos promotions</h2>

        <?php
            //initialisation du numéro de page
            if(isset($_GET['page'])&& $_GET['page']!==""){
                $pageNumber = $_GET['page'];
            }
            else{
                $pageNumber = 1;
            }

            $tabProduit = [];

            try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT *
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    INNER JOIN sae3_skadjam._promu pu
                                        ON pr.id_produit = pu.id_produit
                                    INNER JOIN sae3_skadjam._promotion pn
                                        ON pu.id_promotion = pn.id_promotion
                                    WHERE pr.est_masque = false
                                    AND pr.est_supprime = false"
                                    , PDO::FETCH_ASSOC) as $row){
                    // Formattage des dates
                    $row['date_debut_promotion'] = formatDate($row['date_debut_promotion']);
                    if($row['date_fin_promotion'] !== null){
                        $finPromo = formatDate($row['date_fin_promotion']);
                    }
                    // La promotion est-elle terminée ou commence-t-elle ?
                    if($row['date_debut_promotion'] <= date('Y-m-d') && ($row['date_fin_promotion'] == null || $row['date_fin_promotion'] >= date('Y-m-d'))){
                        $tabProduit[] = $row;
                    }
                }

                if($tabProduit == null){ ?>
                    <p class="text-center mb-9">Nous n'avons pas de produits en promotion pour le moment.</p>
                <?php }
                
                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);
                
                //affiche la photo du produit, son nom, son prix et sa note, son stock ?>
                <div class="flex flex-row flex-wrap justify-around">
                    <?php foreach($lignes as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];
                        // Le produit est-il en promotion ?
                        $stmt = $dbh->prepare("SELECT *
                                            FROM sae3_skadjam._promu
                                            WHERE id_produit = :id_produit");
                        $stmt->execute([':id_produit' => $idProduit]);
                        $estPromu = ($stmt->fetch() !== false); ?>
                        <div id="<?php echo $idProduit; ?>" class="bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3 justify-between">
                            <!--affichage de la photo-->
                            <a href= "<?= './details_produit.php?idProduit='.$idProduit;?>" class="mb-3">
                                <img src="<?= $valeurs['url_photo'];?>" 
                                        alt="<?= $valeurs['alt'];?>"
                                        title="<?= $valeurs['titre'];?>"
                                        class="w-auto h-40 md:h-80 block mx-auto">

                                <!--affichage du nom du produit-->
                                <p><?= $valeurs['libelle_produit'];?></p> 

                                <!--affichage du prix du produit-->   
                                <div class="flex flex-row justify-between items-center">
                                    <p class="inline-block <?= ($prod['pourcentage_remise'] !== NULL)?'line-through':'';?>"> <?= htmlentities(str_replace(".", ",",$prod['prix_ttc'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                    <p class=" pl-3 <?= ($prod['pourcentage_remise'] !== NULL)?'':'hidden';?>"> <?= htmlentities(str_replace(".", ",",$prod['prix_remise'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                </div>
                                <!--récupération de la note-->
                                <div class="flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div>
                            </a>
                            <!-- boutons futurs achats & panier -->
                            <div class="flex justify-end">
                                <!-- Produit dans les futurs achats ? -->
                                <a id="btnFA" href="/php/traitementFAPanier.php?idProduit=<?php echo $idProduit;?>&ajout=fa&vientDe=promo">
                                    <button class="cursor-pointer size-10 bg-no-repeat bg-size-[auto_40px]
                                    <?php 
                                    $bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus.svg)]";
                                    
                                    $trouveFA = array_search($idProduit, $tabFA);
                                    
                                    if ($trouveFA != null) {
                                        $bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus-fill.svg)]";
                                    }
                                    echo $bg;
                                    ?>
                                    ">
                                    </button>
                                </a>
                                <!-- Produit dans le panier ? -->
                                <a id="btnPanier" href="/php/traitementFAPanier.php?idProduit=<?php echo $idProduit;?>&ajout=panier&vientDe=promo">
                                    <button class="cursor-pointer size-10 bg-no-repeat bg-size-[auto_40px]
                                        <?php 
                                        $bg = "bg-[url(/images/logo/bootstrap_icon/cart-vert-fonce.svg)]";
                                        $trouveP = false;

                                        if (isset($_SESSION['panier']['contient'][$idProduit])){
                                            $trouveP = true;
                                        }

                                        if ($trouveP != false) {
                                            $bg = "bg-[url(/images/logo/bootstrap_icon/cart-fill-vert-fonce.svg)]";
                                        }
                                        echo $bg;
                                        ?>
                                    ">
                                    </button>
                                </a>
                            </div>
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
                        </div>
                    <?php } ?>
                </div>         
                <?php $dbh = null;
            }catch(PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center py-3">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./promotion.php?page=". $pageNumber-1;?>">
                <img class="w-7" src="../../images/logo/bootstrap_icon/chevron-left.svg" alt="page précédente">
            </a>
            <?php }?>

            <p>page <?php echo $pageNumber;?></p>
            
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./promotion.php?page=". $pageNumber+1;?>">
                <img class="w-7" src="../../images/logo/bootstrap_icon/chevron-right.svg" alt="page suivante">
            </a>
            <?php }?>
        </div>

        <!---bouton retour--->
        <a href="../../index.php" class="flex justify-center mt-7 mb-7">
            <button class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer">Retour</button>
        </a>
    </main>
    
    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_front.php"; ?>

</body>

</html>
