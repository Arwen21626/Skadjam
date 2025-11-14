<?php 
    session_start();
    //à retirer
    $_SESSION['idCompte'] = 1;

    include __DIR__ .'/../../01_premiere_connexion.php';
    const PAGE_SIZE = 15;
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Accueil</title>
</head>



<body>
    <!--header-->
    <?php include("../../php/structure/header_back.php"); ?>
    <?php include("../../php/structure/navbar_back.php"); ?>

    <main>
        <div class="grid grid-cols-2 gap-2">
            <a href="" title="lien vers page promotion">
                <img src="../../images/images_accueil/promotion.webp" alt="promotion" class="w-80 h-auto">
            </a>
            <a href="" title="lien vers page derniers ajouts">
                <img src="../../images/images_accueil/derniers_ajouts.webp" alt="derniers ajouts" class="w-80 h-auto">
            </a>           
            <a href="" title="lien vers page stock">
                <img src="../../images/images_accueil/stock.webp" alt="stock" class="w-80 h-auto">
            </a>
            <a href="" title="lien vers page commandes">
                <img src="../../images/images_accueil/commandes.webp" alt="commandes" class="w-80 h-auto">
            </a>        
        </div>


        <div class="flex flex-row justify-around m-4">
            <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="../bo/creation_produit.php">Ajouter un produit</a></button>
            <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="">Statistiques</a></button>
            <button class="border-2 border-vertFonce rounded-2xl w-40 h-14"><a href="">Avis récents</a></button>
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
                foreach($dbh->query("SELECT *
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    WHERE v.id_compte = $idCompte"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);

                //affiche la photo du produit, son nom, son prix et sa note, son stock
                foreach($tabProduit as $id => $valeurs){
                    $idProduit = $valeurs['id_produit'];?>
                    <section class="bg-bleu grid grid-col-[30%_70%]">
                        <!--affichage de la photo-->
                        <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>" class="col-span-2">
                            <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                    alt="<?php echo htmlentities($valeurs['alt']);?>"
                                    title="<?php echo htmlentities($valeurs['titre']);?>">
                        </a>

                        <!--affichage du nom du produit-->
                        <h4 class="col-span-2"><?php echo htmlentities($valeurs['libelle_produit']);?></h4> 

                        <!--affichage du prix du produit-->   
                        <p class="col-1"><?php echo htmlentities($valeurs['prix_ttc']);?> €</p>

                        <!--récupération de la note-->
                        <div class=" flex justify-start col-2"></div>
                            <?php $note = $valeurs['note_moyenne']; ?>

                            <!--affichage d'une note nulle-->
                            <?php 
                            if ($note == null){ ?>
                                <p><?php echo htmlentities('non noté'); ?></p>
                            <?php } 
                            else {
                                $entierPrec = intval($note);
                                $entierSuiv = $entierPrec+1;
                                $moitie = $entierPrec+0.5;
                                $noteFinale;
                                $nbEtoilesVides;

                                //note arrondie à l'entier précédent
                                if($note < $entierPrec+0.3){
                                    $noteFinale = $entierPrec;
                                }

                                //note arrondie à 0.5
                                else if(($note < $moitie) || ($note < $entierPrec+0.8)){
                                    $noteFinale = $moitie;
                                    $nbEtoilesVides = 5-$entierPrec-1;
                                    //affichage d'une note et demie
                                    //boucle pour étoiles pleines
                                    for($i=0; $i<$entierPrec; $i++){?>
                                        <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine" class="w-3 h-3"  >
                                    <?php } ?>
                                    <!--demie étoile-->
                                    <img src="../../images/logo/bootstrap_icon/star-half.svg" alt="demie étoile" class="w-3 h-3"  >
                                    <!--boucle pour étoiles vides-->
                                    <?php for($i=0; $i<$nbEtoilesVides; $i++){?>
                                        <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide" class="w-3 h-3"  >
                                    <?php }
                                }
                                
                                //note arrondie à l'entier suivant
                                else{
                                    $noteFinale = $entierSuiv;
                                }

                                //affichage d'une note entière :
                                if($noteFinale != $moitie){
                                    $nbEtoilesVides = 5-$noteFinale;
                                    //boucle pour étoiles pleines
                                    for($i=0; $i<$noteFinale; $i++){?>
                                        <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine" class="w-3 h-3"  >
                                    <?php }
                                    //boucle pour étoiles vides
                                    for($i=0; $i<$nbEtoilesVides; $i++){?>
                                        <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide" class="w-3 h-3"  >
                                    <?php }
                                } ?>  
                        </div>     
                        <!--affichage du stock-->
                        <p class="col-span-2">En stock : <?php echo htmlentities($valeurs['quantite_stock']);?></p>
                        <?php }   ?>
                    </section>
                <?php }          
                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->

        <?php if ($pageNumber>1){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber-1)."#vosProduits";?>">page précédente</a>
        <?php }?>
    
        <?php if ($pageNumber<$maxPage){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber+1)."#vosProduits";?>">page suivante</a>
        <?php }?>

    </main>
    
    <!--footer-->
    <?php include("../../php/structure/footer_back.php"); ?>

</body>

</html>
