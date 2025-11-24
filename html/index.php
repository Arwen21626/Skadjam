<?php
    include(__DIR__ . '/01_premiere_connexion.php');
    const PAGE_SIZE = 15;
    require_once(__DIR__ . "/../connections_params.php");
    require_once(__DIR__ . "/php/fonctions.php");
    session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css">
    <?php (include __DIR__ . "/php/structure/head_front.php"); ?>

    <title>Accueil</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>

<body>
    <!--header-->
    
    <?php (include __DIR__ . "/php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/php/structure/navbar_front.php"); ?>

    <main>
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="#nosProduits" title="lien vers page promotion">
                <img src="images/images_accueil/promotion.webp" alt="promotion" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="#nosProduits" title="lien vers page nouveaux produits">
                <img src="images/images_accueil/nouveaux_produits.webp" alt="nouveaux produits" class="w-90 md:w-150 h-auto justify-self-start">
            </a>           
            <a href="#nosProduits" title="lien vers page les plus vendus">
                <img src="images/images_accueil/les_plus_vendus.webp" alt="les plus vendus" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="html/fo/commandes.php" title="lien vers page commandes">
                <img src="images/images_accueil/commandes.webp" alt="commandes" class="w-90 md:w-150 h-auto justify-self-start">
            </a>        
        </div>


        <!--Début du catalogue-->
        <h2 id="nosProduits">Nos produits</h2>

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
                                    INNER JOIN sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);

                //affiche la photo du produit, son nom, son prix et sa note ?>
                <div class="grid grid-cols-2 justify-items-center md:grid-cols-3">
                    <?php foreach($lignes as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];?>
                        <section class="bg-bleu grid grid-cols-[40%_60%] w-40 md:w-80 h-auto p-2 md:p-3 m-2">
                            <!--affichage de la photo-->
                            <a href= "<?php echo "html/fo/details_produit.php?idProduit=".$idProduit;?>" class="col-span-2 justify-self-center mb-3">
                                <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                        alt="<?php echo htmlentities($valeurs['alt']);?>"
                                        title="<?php echo htmlentities($valeurs['titre']);?>">
                            </a>

                            <!--affichage du nom du produit-->
                            <p class="col-span-2"><?php echo htmlentities($valeurs['libelle_produit']);?></p> 

                            <!--affichage du prix du produit-->   
                            <div class="flex justify-start items-center col-span-2">
                                <p><?php echo htmlentities($valeurs['prix_ttc']);?> €</p>

                                <!--récupération de la note-->
                                <div class="w-2/4 ml-2 md:ml-10 flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div> 
                            </div>
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
            <a class= "lienPage" href="<?php echo "./index.php?page=".($pageNumber-1)."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if ($pageNumber<$maxPage){?>
            <a class= "lienPage" href="<?php echo "./index.php?page=".($pageNumber+1)."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>

    </main>
    
    <!--footer-->
    <?php require (__DIR__ . "/php/structure/footer_front.php"); ?>

</body>

</html>