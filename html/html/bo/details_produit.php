<?php

    include(__DIR__.'/../../01_premiere_connexion.php');

    //Récupération des données sur le produit ainsi que la photo
    $idProd = $_GET['idProduit'];
    $idProd = 1;

    foreach($dbh->query("SELECT *
                        from sae3_skadjam._produit pr
                        inner join sae3_skadjam._montre m
                            on pr.id_produit=m.id_produit
                        inner join sae3_skadjam._photo ph  
                            on ph.id_photo = m.id_photo
                        inner join sae3_skadjam._categorie c
                            on c.id_categorie = pr.id_categorie 
                        where pr.id_produit = $idProd"
                        , PDO::FETCH_ASSOC) as $row){
        $produit = $row;
    }

    // function supprimer($id){
    //     $supp = $dbh->query("DELETE FROM sae3_skadjam._produit WHERE id_produit = $id");
    //     $supp -> execute();
    // }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/output.css">
    <title>Détails</title>
</head>
<body>
    <!--header-->
    <?php //include(__DIR__ . '/../../php/structure/header_back.php');?>
    <?php //include(__DIR__ . '/../../php/structure/navbar_back.php');?>

    <main>
        <!--affichage du libelle-->
        <h2><?php echo($produit['libelle_produit']); ?></h2>
        <!--récupération de la note-->
        <?php $note = $produit['note_moyenne'];
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
            } ?>  
        <p>Catégorie : <?php echo htmlentities($produit['libelle_categorie']); ?></p>
        <!--affichage de la photo-->
        <!--carrousel à faire-->
        <img src="<?php echo htmlentities($produit['url_photo']);?>" 
            alt="<?php echo htmlentities($produit['alt']);?>"
            title="<?php echo htmlentities($produit['titre']);?>">
        <!--affichage du prix-->
        <p> <?php echo htmlentities($produit['prix_ttc']); ?></p>
        <!--affichage de la quantite-->
        <?php 
            $stock = $produit['quantite_stock'];
            if($stock != 0){?>
                <p>En stock : <?php echo htmlentities($stock); ?></p>
            <?php } 
            else{?>
                <p>Produit indsponible</p>
            <?php }?>

            <div class="flex flex-col">
                <button class=" bg-beige rounded-2xl w-40 h-14" type="button">Modifier</button>
                <button class=" bg-beige rounded-2xl w-40 h-14" type="button">Masquer</button>
                <!-- <button type="button">Extraire</button> -->
                <button class=" bg-beige rounded-2xl w-40 h-14" type="button">Supprimer</button>
            </div>
        

        <h2>Description détaillée</h2>
        <?php echo htmlentities($produit['description_produit']);?>

        <p>Partie sur les avis à voir plus tard</p>

    </main>
    <?php //include(__DIR__ . '/../../php/structure/footer_back.php');?>
</body>
</html>
