<?php include('../../PAS_DE_COMMIT.php');?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/index.css" >
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css">
    <title>Accueil</title>
</head>

<body>
    <?php include "../../php/structure/header_front.php"; ?>

    <main>
        <section class="carreImages">

            <a href="" title="lien vers page promotion" id="image1">
                <img src="../../images/images_accueil/promotion.webp" alt="promotion">
            </a>
            <a href="" title="lien vers page nouveaux produits" id="image2">
                <img src="../../images/images_accueil/nouveaux_produits.webp" alt="nouveaux produits">
            </a>           
            <a href="" title="lien vers page les plus vendus" id="image3">
                <img src="../../images/images_accueil/les_plus_vendus.webp" alt="les plus vendus">
            </a>
            <a href="" title="lien vers page commandes" id="image4">
                <img src="../../images/images_accueil/commandes.webp" alt="commandes">
            </a>        
        </section>


        <!--Début du catalogue-->

        <?php
            $tabProduit = [];

            try {
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

                //gère les erreurs
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //faire des tableaux associatifs au lieu de numérique
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT *
                                    from sae3_skadjam._produit pr
                                    inner join sae3_skadjam._montre m
                                        on pr.id_produit=m.id_produit
                                    inner join sae3_skadjam._photo ph  
                                        on ph.id_photo = m.id_photo ", PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                //affiche la photo du produit, son nom, son prix et sa note
                foreach($tabProduit as $id => $valeurs){?>
                    <!--affichage de la photo-->
                    <a href="">
                        <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                alt="<?php echo htmlentities($valeurs['alt']);?>"
                                title="<?php echo htmlentities($valeurs['titre']);?>">
                    </a>

                    <!--affichage du nom du produit-->
                    <h4><?php echo htmlentities($valeurs['libelle_produit']);?></h4> 

                    <!--affichage du prix du produit-->   
                    <p><?php echo htmlentities($valeurs['prix_ttc']);?></p>

                    <!--affichage de la note-->
                    <?php $note = $valeurs['note'];

                        //note nulle
                        if ($note == null){ ?>
                            <p><?php echo htmlentities('non noté'); ?></p>
                        <?php } 
                        else {

                            //note entière 
                            if(($note == 0) || ($note == 1) || ($note == 2) || ($note == 3) || ($note == 4) || ($note == 5)){
                                $cinqMoinsNote = 5-$note;
                                //boucle pour étoiles pleines
                                for($i=0; $i<$note; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php }
                                //boucle pour étoiles vides
                                for($i=0; $i<$cinqMoinsNote; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
                                <?php }
                            }

                            //note à virgule
                            else{
                                $entierPrec = intval($note);
                                $entierSuiv = $entierPrec+1;
                                $moitie = $entierPrec+0.5;
                                $noteFinale;
                                if($note < $entierPrec+0.3){
                                    $noteFinale = $entierPrec;
                                }
                                else if(($note < $moitie) || ($note < $entierPrec+0.8)){
                                    $noteFinale = $moitie;
                                }
                                else{
                                    $noteFinale = $entierSuiv;
                                }
                                $cinqMoinsNote = 5-$partieEntiere-1;
                                //boucle pour étoiles pleines
                                for($i=0; $i<$partieEntiere; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php } ?>
                                <!--demie étoile-->
                                <img src="../../images/logo/bootstrap_icon/star-half.svg" alt="demie étoile">
                                <!--boucle pour étoiles vides-->
                                <?php for($i=0; $i<$cinqMoinsNote; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
                                <?php }
                            }
                        }?>                
            <?php }

                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->

        <p id="droite">Voir plus ...</p>

    </main>
    
    <?php include "../../php/structure/footer_back.php"; ?>

</body>

</html>