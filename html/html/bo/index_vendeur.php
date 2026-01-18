<?php 
    session_start();
    include __DIR__ . '/../../php/verif_role_bo.php';
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . "/../../php/modification_variable.php";
    const PAGE_SIZE = 24;
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__."/../../php/structure/head_back.php");?>
<head> 
    <title>Accueil</title>
</head>


<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="p-8">
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="../bo/promotion_vendeur.php" title="lien vers page promotion">
                <img src="../../images/images_accueil/promotion.webp" alt="promotion" class="w-150 h-auto justify-self-end">
            </a>
            <a href="#vosProduits" title="lien vers page derniers ajouts">
                <img src="../../images/images_accueil/derniers_ajouts.webp" alt="derniers ajouts" class="w-150 h-auto justify-self-start">
            </a>           
            <a href="../bo/stock.php" title="lien vers page stock">
                <img src="../../images/images_accueil/stock.webp" alt="stock" class="w-150 h-auto justify-self-end">
            </a>
            <a href="../bo/liste_commandes.php" title="lien vers page commandes">
                <img src="../../images/images_accueil/commandes.webp" alt="commandes" class="w-150 h-auto justify-self-start">
            </a>        
        </div>

        <div class="mt-15 flex flex-row justify-around">
            <a href="../bo/creation_produit.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Créer un produit</button></a>
            <a href="../bo/details_remises.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Consulter les remises</button></a>
            <a href="../bo/vider_catalogue.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Vider le catalogue</button></a>
        </div>

        <!--Début du catalogue-->
        <h2 id="vosProduits">Vos produits</h2>

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
                foreach($dbh->query("SELECT pr.libelle_produit, pr.id_produit, url_photo, alt, titre, prix_ttc, quantite_stock, 
                                        note_moyenne, prix_remise, pourcentage_remise
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    left join sae3_skadjam._reduit rd
                                        on rd.id_produit = pr.id_produit
                                    left join sae3_skadjam._remise r
                                        on r.id_remise = rd.id_remise
                                    WHERE v.id_compte = $idCompte
                                        AND pr.est_supprime = false"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                if($tabProduit == null){ ?>
                    <p>Votre catalogue est vide.</p>
                <?php }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE); 

                //affiche la photo du produit, son nom, son prix et sa note, son stock ?>
                <div class="flex flex-row flex-wrap justify-around">
                    <?php 
                        foreach($tabProduit as $id => $valeurs){
                            $idProduit = $valeurs['id_produit'];
                            // Le produit est-il en promotion ?
                            $stmt = $dbh->prepare("SELECT *
                                                FROM sae3_skadjam._promu
                                                WHERE id_produit = :id_produit");
                            $stmt->execute([':id_produit' => $idProduit]);
                            $estPromu = (!empty($stmt->fetch())); ?>
                            <section class="bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3">
                                <!--affichage de la photo-->
                                <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>" class="mb-3">
                                    <img class="w-auto h-40 md:h-80 justify-self-center" 
                                        src="<?php echo $valeurs['url_photo'];?>" 
                                        alt="<?php echo $valeurs['alt'];?>"
                                        title="<?php echo $valeurs['titre'];?>">
                                        
                                <!--affichage du nom du produit-->
                                <p><?php echo $valeurs['libelle_produit'];?></p> 

                                <!--affichage du prix du produit-->   
                                <div class="flex flex-row justify-between items-center">
                                    <p class="<?php echo ($valeurs['pourcentage_remise'] !== NULL)?'line-through':'';?>"> <?php echo htmlentities(str_replace(".", ",",$valeurs['prix_ttc'])); ?>€ (TTC)</p>
                                    <p class="<?php echo ($valeurs['pourcentage_remise'] !== NULL)?'':'hidden';?>"> <?php echo htmlentities(str_replace(".", ",",$valeurs['prix_remise'])); ?>€ (TTC)</p>

                                </div>
                                <!--récupération de la note-->
                                <div class="flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div>
                                
                                <!--affichage du stock-->
                                <p>En stock : <?php echo $valeurs['quantite_stock'];?></p>
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
                                        if($debutPromo <= date('Y-m-d') && ($finPromo === null || $finPromo >= date('Y-m-d')) && !empty($labelPromo)){
                                    ?>
                                    <!-- Affichage de la bannière -->
                                    <div class="bg-rouge absolute w-36 md:w-74 underline text-beige pt-2 pb-1.5">
                                        <h4 class="text-center text-beige overline m-0"><strong><?php echo htmlspecialchars($labelPromo); ?></strong></h4>
                                    </div>
                                <?php }} ?> 
                            </section>
                    <?php } ?>
                </div>         
                <?php $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center">
            <?php if ($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "./index_vendeur.php?page=".($pageNumber-1)."#vosProduits";?>">Page précédente</a>
            
            <?php }?>
        
            <?php if ($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "./index_vendeur.php?page=".($pageNumber+1)."#vosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
    </main>
    
    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>
</html>
