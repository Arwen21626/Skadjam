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

// Récupération des futurs achats et du panier du client
include __DIR__. '/../../php/requetesBDD/recup_FA.php';
include __DIR__. '/../../php/requetesBDD/recup_panier.php';

// Variable pour savoir s'il faut afficher une popup
$addPanier = (isset($_GET['addPanier']) && $_GET['addPanier'] === "1");
$removePanier = (isset($_GET['removePanier']) && $_GET['removePanier'] === "1");
$addFA = (isset($_GET['addFA']) && $_GET['addFA'] === "1");
$removeFA = (isset($_GET['removeFA']) && $_GET['removeFA'] === "1");

// Récupération des infos produits en fontion du tabFA
$tabProd = [];
if ($_SESSION['role'] == "client") {
    foreach($dbh->query("SELECT pr.libelle_produit, pr.id_produit, pr.prix_ttc, 
                        pr.quantite_stock, pr.note_moyenne, pr.prix_remise, 
                        r.pourcentage_remise, pu.id_promotion,
                        pm.label, pm.date_debut_promotion, pm.date_fin_promotion,
                        ph.url_photo, ph.alt, ph.titre
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
                                LEFT JOIN sae3_skadjam._reduit rd
                                    ON rd.id_produit = pr.id_produit
                                LEFT JOIN sae3_skadjam._remise r
                                    ON r.id_remise = rd.id_remise
                                INNER JOIN sae3_skadjam._futur_achat fa
                                    ON pr.id_produit = fa.id_produit
                                WHERE pr.est_supprime = false AND pr.est_masque = false"
                    , PDO::FETCH_ASSOC) as $row){
        $tabProd[] = $row;
    }
}
else {
    foreach ($tabFA as $id) {
        $stmt = $dbh->query("SELECT pr.libelle_produit, pr.id_produit, pr.prix_ttc, 
                                pr.quantite_stock, pr.note_moyenne, pr.prix_remise, 
                                r.pourcentage_remise, pu.id_promotion,
                                pm.label, pm.date_debut_promotion, pm.date_fin_promotion,
                                ph.url_photo, ph.alt, ph.titre
                                FROM sae3_skadjam._produit pr
                                INNER JOIN sae3_skadjam._montre m
                                    ON pr.id_produit=m.id_produit
                                INNER JOIN sae3_skadjam._photo ph  
                                    ON ph.id_photo = m.id_photo 
                                INNER JOIN sae3_skadjam._vendeur v
                                    ON pr.id_vendeur = v.id_compte
                                LEFT JOIN sae3_skadjam._reduit rd
                                    ON rd.id_produit = pr.id_produit
                                LEFT JOIN sae3_skadjam._remise r
                                    ON r.id_remise = rd.id_remise
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
    
    <main class="min-h-[400px] md:min-h-[615px]">
        <h2>Vos futurs achats</h2>
        <?php if($tabFA == null){ ?>
                    <h4 class="text-center">Vos futurs achats sont vides.</h4>
        <?php }
        else{ ?>
        <!-- Liste des cartes produits -->
        <div class="flex flex-row flex-wrap justify-around">
            <?php foreach($lignes as $id => $valeurs){
                $idProduit = $valeurs['id_produit'];
                // Le produit est-il en promotion ?
                $stmt = $dbh->prepare("SELECT *
                                    FROM sae3_skadjam._promu
                                    WHERE id_produit = :id_produit");
                $stmt->execute([':id_produit' => $idProduit]);
                $estPromu = ($stmt->fetch() !== false); ?>
                <!-- Carte produit -->
                <div id="<?php echo $idProduit; ?>" class="carteProduit bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3 justify-between">
                    <p class="hidden"><?php echo $valeurs['quantite_stock']; ?></p>
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
                    <!-- boutons futurs achats & panier -->
                    <div class="flex justify-end">
                        <!-- Produit dans les futurs achats ? -->
                        <a id="btnFA" href="/php/traitementFAPanier.php?idProduit=<?php echo $idProduit;?>&ajout=fa&vientDe=fa">
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
                        <button class="btnPanier cursor-pointer size-10 bg-no-repeat bg-size-[auto_40px]
                            <?php 
                            $bg = "bg-[url(/images/logo/bootstrap_icon/cart-vert-fonce.svg)]";
                            $trouveP = false;
                            if ($_SESSION['role'] != 'client') {
                                if (isset($_SESSION['panier']['contient'][$idProduit])){
                                    $trouveP = true;
                                }
                            }  
                            else{
                                foreach ($tabPanier as $id) {
                                    if($id['id_produit'] == $idProduit){
                                        $trouveP = true;
                                    }
                                }
                            }

                            if ($trouveP != false) {
                                $bg = "bg-[url(/images/logo/bootstrap_icon/cart-fill-vert-fonce.svg)]";
                            }
                            echo $bg;
                            ?>
                        ">
                        </button>
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
        <?php } ?>
        
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center py-3">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./fo/futus_achats?page=". $pageNumber-1 ."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./fo/futus_achats?page=". $pageNumber+1 ."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>


        <!-- Box question nb prod a mettre au panier -->
        <div id="fondNbAddPanier" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

        <div id="contNbAddPanier" class="hidden fixed bg-white top-1/2 left-1/5 justify-center items-center flex-col p-4 z-50 w-3/5 md:top-2/5 md:w-2/5 md:left-3/10 h-45">
            <form action="/php/traitementFAPanier.php" method="get" id="formNbAddPanier" class="flex flex-col items-center w-full h-full space-y-4">
                <label id="validAjout" class="hidden" for="nbAddPanier">Combien voulez-vous en ajouter au panier ?</label>
                <p id="valideRetrait" class="hidden">Etes-vous sur de vouloir retirer ce produit de votre panier ?</p>
                <input placeholder="5" class="hidden pl-3 border-4 border-beige rounded-2xl w-20 m-2 placeholder-gray-500" type="number" name="nbAddPanier" id="nbAddPanier" min="1">
                <input type="hidden" name="ajout" value="panier">
                <input type="hidden" name="vientDe" value="fa">
                <div class="flex flex-row justify-around w-full">
                    <p id="btnRetour" class="flex items-center justify-center border-2 border-vertClair rounded-2xl w-25 h-12 cursor-pointer">Retour</p>
                    <input class="border-2 border-vertClair rounded-2xl w-25 h-12 cursor-pointer" type="submit" value="Valider">
                </div>
            </form>
        </div>

        <!-- Liste des popup de la page -->
        <div id="popup-overlay" class="right-12 md:right-40">
            <?php if($addPanier){ ?>
            <!---popup ajout d'un produit dans le panier--->
            <div id="popup-ajouter-panier" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été ajouté à votre panier !</p>
                <div class="flex justify-around mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                    <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
                </div>
            </div>
            <?php } ?>

            <?php if($removePanier){ ?>
            <!---popup retrait d'un produit du panier--->
            <div id="popup-retirer-panier" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été retiré de votre panier !</p>
                <div class="flex justify-around mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                    <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
                </div>
            </div>
            <?php } ?>

            <?php if($addFA){ ?>
            <!---popup ajout d'un produit aux futurs achats--->
            <div id="popup-ajouter-fa" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été ajouté à vos futurs achats !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
            <?php } ?>

            <?php if($removeFA){ ?>
            <!---popup retrait d'un produit aux futurs achats--->
            <div id="popup-retirer-fa" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été retiré de vos futurs achats !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
            <?php } ?>
        </div>
    </main>
    <!--footer-->
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
    <script type="module" src="/js/fo/popupFAPanier.js"></script>
</body>
</html>