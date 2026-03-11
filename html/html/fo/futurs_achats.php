<?php
include __DIR__ . '/../../01_premiere_connexion.php';

//initialisation du numéro de page
if(isset($_GET['page'])&& $_GET['page']!==""){
    $pageNumber = $_GET['page'];
}else{
    $pageNumber = 1;
}
$tabFA = [];

// Récupération des futurs achats du VISITEUR
if ($_SESSION['role'] == "visiteur") {
    $tabFA = $_SESSION['futurAchat'];
}

// Récupération des futurs achats du client
if ($_SESSION['role'] == "client") {
    $idCompte = $_SESSION['idCompte'];
    foreach ($dbh->query("SELECT id_produit FROM sae3_skadjam._futur_achat WHERE id_compte = $idCompte", PDO::FETCH_ASSOC) as $row) {
        $tabFA[] = $row;
    }

    print_r($tabFA);
}

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
        <?php
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
        <div class="flex flex-row space-x-4 justify-center py-3">
            <?php if($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./index.php?page=". $pageNumber-1 ."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?= "./index.php?page=". $pageNumber+1 ."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
    </main>
    <!--footer-->
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>