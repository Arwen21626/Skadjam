<?php 
    include __DIR__ . '/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../../connections_params.php";
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . "/../../php/modification_variable.php";
    const PAGE_SIZE = 15;
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

    <main class=" p-8">
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
                                    WHERE pr.est_masque = false"
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
                <div class="grid grid-cols-3">
                    <?php foreach($tabProduit as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];
                        // Le produit est-il en promotion ?
                        $stmt = $dbh->prepare("SELECT *
                                                FROM sae3_skadjam._promu
                                                WHERE id_produit = :id_produit");
                        $stmt->execute([':id_produit' => $idProduit]);
                        $estPromu = ($stmt->fetch() !== false); ?>
                        <section class="bg-bleu grid grid-cols-[40%_60%] w-40 md:w-80 h-auto p-2 md:p-3 m-2">
                            <!--affichage de la photo-->
                            <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>" class="col-span-2 justify-self-center mb-3">
                                <img src="<?php echo $valeurs['url_photo'];?>" 
                                        alt="<?php echo $valeurs['alt'];?>"
                                        title="<?php echo $valeurs['titre'];?>">
                            </a>

                            <!--affichage de la promotion-->
                            <?php if($estPromu){ ?>
                                <div class="bg-rouge absolute col-span-2 w-36 md:w-74 underline text-beige pt-2 pb-1.5">
                                    <h4 class="text-center text-beige overline m-0"><strong><?php echo htmlspecialchars($valeurs['label']); ?></strong></h4>
                                </div>
                            <?php } ?>

                            <!--affichage du nom du produit-->
                            <p class="col-span-2"><?php echo $valeurs['libelle_produit'];?></p> 

                            <!--affichage du prix du produit-->   
                            <div class="flex justify-start items-center col-span-2">
                                <?php $prix = str_replace(".", ",", $valeurs['prix_ttc'])?>
                                <p><?php echo $prix;?> €</p>

                                <!--récupération de la note-->
                                <div class="ml-2 md:ml-10 flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div> 
                            </div>    
                        </section>
                    <?php } ?>
                </div>         
                <?php $dbh = null;
            }catch(PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center">
            <?php if ($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "./index.php?page=".($pageNumber-1)."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if ($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "./index.php?page=".($pageNumber+1)."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
    </main>
    
    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_front.php"; ?>

</body>

</html>
