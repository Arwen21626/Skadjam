<?php
session_start();

include __DIR__ . '/../../01_premiere_connexion.php';
include __DIR__ . "/../../php/fonctions.php";
include __DIR__. '/../../php/modification_variable.php';
require_once __DIR__ . "/../../php/verif_role_fo.php";

const PAGE_SIZE = 24;

//initialisation du numéro de page
if(isset($_GET['page'])&& $_GET['page']!==""){
    $pageNumber = $_GET['page'];
}else{
    $pageNumber = 1;
}


// Récupération des futurs achats du VISITEUR
if ($_SESSION['role'] == "visiteur") {
    $tabFA = $_SESSION['futurAchat'];
}

// Récupération des futurs achats du client
include __DIR__. '/../../php/requetesBDD/recup_FA.php';

// Récupération des infos produits en fontion du tabFA
$tabProd = [];
if ($_SESSION['role'] == "client") {
    foreach($dbh->query("SELECT pr.id_produit, pr.date_creation, libelle_produit, description_produit, prix_ttc, prix_remise, quantite_stock, id_categorie, pr.id_vendeur, note_moyenne, ph.id_photo, url_photo, alt, titre, id_compte, pu.id_promotion, label
                            FROM sae3_skadjam._produit pr
                                INNER JOIN sae3_skadjam._montre m
                                    ON pr.id_produit=m.id_produit
                                INNER JOIN sae3_skadjam._photo ph  
                                    ON ph.id_photo = m.id_photo 
                                INNER JOIN sae3_skadjam._vendeur v
                                    ON pr.id_vendeur = v.id_compte
                                LEFT JOIN sae3_skadjam._promu pu
                                    ON pu.id_produit = pr.id_produit
                                LEFT JOIN sae3_skadjam._promotion pm
                                    ON pu.id_promotion = pm.id_promotion
                                INNER JOIN sae3_skadjam._futur_achat fa
                                    ON pr.id_produit = fa.id_produit
                                WHERE pr.est_supprime = false AND pr.est_masque = false"
                    , PDO::FETCH_ASSOC) as $row){
        $tabProd[] = $row;
    }
}
else {
    foreach ($tabFA as $id) {
        $stmt = $dbh->query("SELECT pr.id_produit, pr.date_creation, libelle_produit, description_produit, prix_ttc, prix_remise, quantite_stock, id_categorie, pr.id_vendeur, note_moyenne, ph.id_photo, url_photo, alt, titre, id_compte, pu.id_promotion, label
                            FROM sae3_skadjam._produit pr
                                INNER JOIN sae3_skadjam._montre m
                                    ON pr.id_produit=m.id_produit
                                INNER JOIN sae3_skadjam._photo ph  
                                    ON ph.id_photo = m.id_photo 
                                INNER JOIN sae3_skadjam._vendeur v
                                    ON pr.id_vendeur = v.id_compte
                                LEFT JOIN sae3_skadjam._promu pu
                                    ON pu.id_produit = pr.id_produit
                                LEFT JOIN sae3_skadjam._promotion pm
                                    ON pu.id_promotion = pm.id_promotion
                                WHERE pr.est_supprime = false AND pr.est_masque = false AND pr.id_produit = $id"
                            );
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $tabProd[] = $row;
    }
}


//découpe le catalogue en page
$maxPage = sizeof($tabProd)/PAGE_SIZE;
$lignes = array_slice($tabProd, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Futurs achats</title>
</head>
<?php include __DIR__ . "/../../php/structure/head_front.php"; ?>

<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>
    
    <main>
        <h2>Vos futurs achats</h2>
        <!-- Liste des produits -->
        <div class="flex flex-row flex-wrap justify-around">
            <?php foreach($lignes as $id => $valeurs){
                $idProduit = $valeurs['id_produit'];
                // Le produit est-il en promotion ?
                $stmt = $dbh->prepare("SELECT *
                                    FROM sae3_skadjam._promu
                                    WHERE id_produit = :id_produit");
                $stmt->execute([':id_produit' => $idProduit]);
                $estPromu = ($stmt->fetch() !== false); ?>
                <!-- Affichage d'une carte produit -->
                <div id="<?php echo $idProduit; ?>" class="bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3 justify-between">
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
                    <div class="flex justify-end">
                        <a id="btnFA" href="/php/traitementFAPanier.php?idProduit=<?php echo $idProduit;?>&ajout=fa&vientDe=fa">
                            <button class="cursor-pointer size-10 bg-no-repeat bg-size-[auto_40px]
                            <?php 
                            $bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus.svg)]";
                            $trouve = array_search($idProduit, $_SESSION['futurAchat']);
                            if ($trouve != null) {
                                $bg = "bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus-fill.svg)]";
                            }
                                echo $bg;
                            ?>
                            ">
                            </button>
                        </a>
                        <a id="btnPanier" href=""><button class="cursor-pointer size-10 bg-no-repeat bg-size-[auto_40px] bg-[url(/images/logo/bootstrap_icon/cart-vert-fonce.svg)]"></button></a>
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
        
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center py-3">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./fo/futus_achats?page=". $pageNumber-1 ."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./fo/futus_achats?page=". $pageNumber+1 ."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
    </main>
    <!--footer-->
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>