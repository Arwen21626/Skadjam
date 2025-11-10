<?php include('../../connections_params.php');
include('../../01_premiere_connexion.php');
const PAGE_SIZE = 15;?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css//bo/index_vendeur.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Accueil</title>
</head>



<body>
    <!--header-->
    <?php include "../../php/structure/header_front.php"; ?>

    <main>
        <section class="carreImages">

            <a href="" title="lien vers page promotion" id="image1">
                <img src="../../images/images_accueil/promotion.webp" alt="promotion">
            </a>
            <a href="" title="lien vers page derniers ajouts" id="image2">
                <img src="../../images/images_accueil/derniers_ajouts.webp" alt="derniers ajouts">
            </a>           
            <a href="" title="lien vers page stock" id="image3">
                <img src="../../images/images_accueil/stock.webp" alt="stock">
            </a>
            <a href="" title="lien vers page commandes" id="image4">
                <img src="../../images/images_accueil/commandes.webp" alt="commandes">
            </a>        
        </section>

        <section id ="ligneBoutons">
            <a href="" class="bouton">Ajouter un produit</a>
            <a href="" class="bouton">Statistiques</a>
            <a href="" class="bouton">Avis récents</a>
        </section>

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
                                    from sae3_skadjam._produit pr
                                    inner join sae3_skadjam._montre m
                                        on pr.id_produit=m.id_produit
                                    inner join sae3_skadjam._photo ph  
                                        on ph.id_photo = m.id_photo ", PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);

                //affiche la photo du produit, son nom, son prix et sa note
                foreach($tabProduit as $id => $valeurs){
                    $idProduit = $valeurs['id_produit'];?>
                    <!--affichage de la photo-->
                    <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>">
                        <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                alt="<?php echo htmlentities($valeurs['alt']);?>"
                                title="<?php echo htmlentities($valeurs['titre']);?>">
                    </a>

                    <!--affichage du nom du produit-->
                    <h4><?php echo htmlentities($valeurs['libelle_produit']);?></h4> 

                    <!--affichage du prix du produit-->   
                    <p><?php echo htmlentities($valeurs['prix_ttc']);?></p>

                    <!--récupération de la note-->
                    <?php $note = $valeurs['note_moyenne'];

                        //affichage d'une note nulle
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
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php } ?>
                                <!--demie étoile-->
                                <img src="../../images/logo/bootstrap_icon/star-half.svg" alt="demie étoile">
                                <!--boucle pour étoiles vides-->
                                <?php for($i=0; $i<$nbEtoilesVides; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
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
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php }
                                //boucle pour étoiles vides
                                for($i=0; $i<$nbEtoilesVides; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
                                <?php }
                            }
                        }  
                }          
                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->

        <?php if ($pageNumber>1){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber-1)."#nosProduits";?>">page précédente</a>
        <?php }?>
    
        <?php if ($pageNumber<$maxPage){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber+1)."#nosProduits";?>">page suivante</a>
        <?php }?>

    </main>
    
    <!--footer-->
    <?php include "../../php/structure/footer_front.php"; ?>

</body>

</html>
