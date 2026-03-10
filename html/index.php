<?php
    session_start();

    include __DIR__ . '/01_premiere_connexion.php';

    const PAGE_SIZE = 24;

    require_once __DIR__ . "/php/fonctions.php";
    require_once __DIR__ . "/php/modification_variable.php";
    
    require_once __DIR__ . "/php/verif_role_fo.php";
    
    if (!isset($_SESSION['role'])) {
        $_SESSION['role'] = "visiteur";
        $_SESSION['panier'] = ["nb_produit_total" => 0, // Utilisation des noms de colonne utilisées dans la BDD
                               "montant_total_ttc" => 0,
                               "contient" => []]; //format du tableau représentant un produit : ['id' => 25, 'quantite_par_produit' => 2]
    }
    

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
                                    WHERE pr.est_supprime = false AND pr.est_masque = false"
                        , PDO::FETCH_ASSOC) as $row){
        $tabProduit[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Accueil</title>

        <script>
            const tabProd = <?php echo json_encode($tabProduit);?>;    
        </script>
        
        <script src="js/affichageListeProduits.js"></script>
        <script src="js/tris.js"></script>
        <script src="js/affichageNote.js"></script> 
    </head>    
<?php include __DIR__ . "/php/structure/head_front.php"; ?>
<body>
    <!--header-->
    
    <?php include __DIR__ . "/php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/php/structure/navbar_front.php"; ?>

    <main class="mt-10">
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="html/fo/promotion.php" title="lien vers page promotion" alt="promotion">
                <img src="images/images_accueil/promotion.webp" title="lien vers page promotion" alt="Produits en promotion" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="html/fo/nouveaux_produits.php" title="lien vers page nouveaux produits" alt="nouveaux produits">
                <img src="images/images_accueil/nouveaux_produits.webp" title="lien vers page nouveaux produits" alt="Produits ajoutés ces 30 derniers jours" class="w-90 md:w-150 h-auto justify-self-start">
            </a>           
            <a href="html/fo/les_plus_vendus.php" title="lien vers page les plus vendus" alt="les 16 produits les plus vendus sur le site">
                <img src="images/images_accueil/les_plus_vendus.webp" title="lien vers page les plus vendus" alt="Produits les plus vendus (Pas encore disponible)" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="html/fo/liste_commandes.php" title="lien vers page commandes" alt="commandes">
                <img src="images/images_accueil/commandes.webp" title="lien vers page commandes" alt="Mes commandes en cours" class="w-90 md:w-150 h-auto justify-self-start">
            </a>        
        </div>

        <!--Début du catalogue-->
        <h2 id="nosProduits">Nos produits</h2>

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
                                    WHERE pr.est_supprime = false AND pr.est_masque = false"
                                    , PDO::FETCH_ASSOC) as $row){
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
        
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./index.php?page=". $pageNumber-1 ."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./index.php?page=". $pageNumber+1 ."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
    </main>
    

    <!--footer-->
    <?php require __DIR__ . "/php/structure/footer_front.php"; ?>

</body>

</html>