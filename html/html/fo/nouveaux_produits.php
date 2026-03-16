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
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_front.php";?>
<head> 
    <title>Nouveaux produits</title>
</head>



<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px] p-8">
        <!--Début du catalogue-->
        <h2 class="m-0">Nos nouveaux produits</h2>
        <h3 class="text-center m-0 relative bottom-5">Produits ajoutés il y a moins de 30 jours</h3>

        <?php
            //initialisation du numéro de page
            if(isset($_GET['page'])&& $_GET['page']!==""){
                $pageNumber = $_GET['page'];
            }else{
                $pageNumber = 1;
            }

            $tabProduit = [];

            try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT pr.libelle_produit, pr.id_produit, url_photo, alt, titre, prix_ttc, quantite_stock, note_moyenne, prix_remise, pourcentage_remise
                                    FROM sae3_skadjam._produit pr
                                    INNER JOIN sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    left join sae3_skadjam._reduit rd
                                        on rd.id_produit = pr.id_produit
                                    left join sae3_skadjam._remise r
                                        on r.id_remise = rd.id_remise
                                    WHERE pr.est_supprime = false AND pr.est_masque = false AND pr.date_creation >= CURRENT_DATE - INTERVAL '30 days'"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE); 
                ?>
                
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
                            <a href= "<?= './details_produit.php?idProduit='.$idProduit;?>" class="mb-3">
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
                                <a id="btnFA" href="/php/traitementFAPanier.php?idProduit=<?php echo $idProduit;?>&ajout=fa&vientDe=nP">
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
                <?php $dbh = null;
            }catch(PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }

            ?>
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center py-3">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./nouveaux_produits.php?page=". $pageNumber-1;?>">
                <img class="w-7" src="../../images/logo/bootstrap_icon/chevron-left.svg" alt="page précédente">
            </a>
            <?php }?>

            <p>page <?php echo $pageNumber;?></p>
            
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./nouveaux_produits.php?page=". $pageNumber+1;?>">
                <img class="w-7" src="../../images/logo/bootstrap_icon/chevron-right.svg" alt="page suivante">
            </a>
            <?php }?>
        </div>

        <!---bouton retour--->
        <a href="../../index.php" class="flex justify-center mt-7 mb-7">
            <button class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-60 w-35 md:h-14 h-10 p-2 m-1 cursor-pointer">Retour</button>
        </a>

        <!-- Box question nb prod a mettre au panier -->
        <div id="fondNbAddPanier" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

        <div id="contNbAddPanier" class="hidden fixed bg-white top-1/2 left-1/5 justify-center items-center flex-col p-4 z-50 w-3/5 md:top-2/5 md:w-2/5 md:left-3/10 h-45">
            <form action="/php/traitementFAPanier.php" method="get" id="formNbAddPanier" class="flex flex-col items-center w-full h-full space-y-4">
                <label id="validAjout" class="hidden" for="nbAddPanier">Combien voulez-vous en ajouter au panier ?</label>
                <p id="valideRetrait" class="hidden">Etes-vous sur de vouloir retirer ce produit de votre panier ?</p>
                <input placeholder="5" class="hidden pl-3 border-4 border-beige rounded-2xl w-20 m-2 placeholder-gray-500" type="number" name="nbAddPanier" id="nbAddPanier" min="0">
                <input type="hidden" name="ajout" value="panier">
                <input type="hidden" name="vientDe" value="nP">
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