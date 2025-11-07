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
    <header>

    </header>

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
                    <a href="">
                        <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                alt="<?php echo htmlentities($valeurs['alt']);?>"
                                title="<?php echo htmlentities($valeurs['titre']);?>">
                    </a>
                    <h4><?php echo htmlentities($valeurs['libelle_produit']);?></h4>    
                    <p><?php echo htmlentities($valeurs['prix_ttc']);?></p>
                    <?php $note = $valeurs['note'];
                        if ($note == null){ ?>
                            <p><?php echo htmlentities('non noté'); ?></p>
                        <?php } 
                        else {
                            if(($note == 0) || ($note == 1) || ($note == 2) || ($note == 3) || ($note == 4) || ($note == 5)){
                                $cinqMoinsNote = 5-$note;
                                for($i=0; $i<$note; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php }
                                for($i=0; $i<$cinqMoinsNote; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
                                <?php }
                            }
                            if(($note == 0.5) || ($note == 1.5) || ($note == 2.5) || ($note == 3.5) || ($note == 4.5) || ($note == 5.5)){
                                $partieEntiere = $note-0.5;
                                $cinqMoinsNote = 5-$partieEntiere-1;
                                for($i=0; $i<$partieEntiere; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
                                <?php } ?>
                                <img src="../../images/logo/bootstrap_icon/star-half.svg" alt="demie étoile">
                                <?php for($i=0; $i<$cinqMoinsNote; $i++){?>
                                    <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
                                <?php }
                            }
                        }?>

                    
                    <!--<p>nb étoiles</p>-->
                    
            <?php }

                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>

        <p id="droite">Voir plus ...</p>
    </main>
    
    
    <footer>

    </footer>
</body>

</html>