<?php
    include(__DIR__.'/../../01_premiere_connexion.php');
    require_once("./../../php/fonctions.php");

    //Récupération des données sur le produit ainsi que la photo
    $idProd = $_GET['idProduit'];

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
    <?php 
    include(__DIR__ . '/../../php/structure/header_back.php');
    include(__DIR__ . '/../../php/structure/navbar_back.php');
    ?>

    <main class="p-10">
        <!--affichage du libelle-->
        <h2><?php echo($produit['libelle_produit']); ?></h2>
        <!--récupération de la note-->
        <?php 
            $note = $produit['note_moyenne'];
            affichageNote($note);
        ?>  
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
            <button class=" bg-beige rounded-2xl w-40 h-14" type="button"><a href="../bo/supprimer_produit.php">Supprimer</a></button>
        </div>
        

        <h3>Description détaillée</h3>
        <p><?php echo htmlentities($produit['description_produit']);?></p>

        <!-- Avis -->
        <h3>Avis</h3>
        <section class=" ml-32">
            <?php 
            foreach($dbh->query("SELECT * FROM sae3_skadjam._avis a 
                                    INNER JOIN sae3_skadjam._client c 
                                        ON a.id_compte = c.id_compte 
                                    WHERE id_produit = $idProd") as $row){?>
            <section class=" bg-bleu m-4 p-4 w-2/3">
                <div class="flex flex-nowrap justify-start items-center w-auto">
                    <h4 class="mr-4">
                        <?php echo $row['pseudo'];?>
                    </h4>
                    <?php echo affichageNote($row['nb_etoile']);?>
                </div>
                <p><?php echo $row['contenu_commentaire'];?></p>     
            </section>          
            <?php }?>
        </section>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
</body>
</html>
