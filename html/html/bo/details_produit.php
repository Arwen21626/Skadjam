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
    <?php require(__DIR__ . "/../../php/structure/head_front.php") ?>
    <title>Détails</title>
</head>
<body>
    <!--header-->
    <?php 
    include(__DIR__ . '/../../php/structure/header_back.php');
    include(__DIR__ . '/../../php/structure/navbar_back.php');
    ?>

    <main class="p-10 flex flex-col">
        <!--affichage du libelle-->
        <h2><?php echo($produit['id_produit'].' - '.$produit['libelle_produit']); ?></h2>
        <!--récupération de la note-->
        <?php 
            $note = $produit['note_moyenne'];
            affichageNote($note);
        ?>  
        <p>Catégorie : <?php echo htmlentities($produit['libelle_categorie']); ?></p>
        <!--affichage de la photo-->

        <!--carrousel à faire-->
        <div class="flex flex-row justify-around  m-4 p-4">
            <img class=" w-1/4" src="<?php echo htmlentities($produit['url_photo']);?>" 
            alt="<?php echo htmlentities($produit['alt']);?>"
            title="<?php echo htmlentities($produit['titre']);?>">

            <div class="m-4 p-4 space-y-4 content-between">
                <!--affichage du prix-->
                <p> <?php echo htmlentities($produit['prix_ttc']); ?>€ (TTC)</p>
                <!--affichage de la quantite-->
                <?php 
                $stock = $produit['quantite_stock'];
                if($stock != 0){?>
                    <p>En stock : <?php echo htmlentities($stock); ?></p>
                <?php } 
                else{?>
                    <p>Produit indsponible</p>
                <?php }?>

                <div class="flex flex-col space-y-4">
                    <button class=" bg-beige rounded-2xl w-40 h-14" type="button"><a href="../bo/modifier_produit.php?idProduit=<?php echo $idProd?>">Modifier</a></button>
                    <!-- <button class=" bg-beige rounded-2xl w-40 h-14" type="button">Masquer</button> -->
                    <button class=" bg-beige rounded-2xl w-40 h-14" type="button"><a href="../bo/supprimer_produit.php?idProduit=<?php echo $idProd?>">Supprimer</a></button>
                </div>
            </div>
        </div>

        <h3>Description détaillée</h3>
        <p><?php echo htmlentities($produit['description_produit']);?></p>

        <!-- Avis -->
        <h3>Avis</h3>
        <div class="flex flex-row justify-between mt-10">
            <?php 
            // Recupération des avis
            $avis = [];
            foreach($dbh->query("SELECT * FROM sae3_skadjam._avis a 
                                INNER JOIN sae3_skadjam._client c 
                                    ON a.id_compte = c.id_compte 
                                WHERE id_produit = $idProd", PDO::FETCH_ASSOC) as $row){
                $avis[] = $row;
            }
            
            if($avis == null){?>
                <p class=" ml-24">Aucun commentaire associé à ce produit.</p>
            <?php }
            else{?>

            <!-- Commentaire -->
            <section class=" ml-32">
                <?php foreach($avis as $row){?>
                        <section class=" bg-bleu rounded-2xl m-4 p-4 w-4xl">
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

            <!-- Notes -->
            <section class="mr-16 p-5 bg-beige rounded-2xl sticky top-48 h-80 w-45 flex flex-col justify-center">
                <h4>Notes - <?php echo count($avis);?></h4>
                <table>
                    <tbody>
                        <?php for ($i = 0; $i <= 5; $i++){
                            $compteur = 5- $i;?>
                            <tr class="flex justify-around items-center">
                                <td class=" mr-1 mt-2 mb-2" ><?php echo $compteur;?></td>
                                <td class=" mr-4 ml-1 mt-2 mb-2"><img src="../../images/logo/bootstrap_icon/star-fill.svg"></td>
                                <?php 
                                    foreach($dbh->query("SELECT COUNT(nb_etoile) AS nbre_notes 
                                                            FROM sae3_skadjam._avis 
                                                            WHERE id_produit = $idProd 
                                                                AND nb_etoile = $compteur"
                                                        , PDO::FETCH_ASSOC) as $row){;?>
                                    <td class=" mr-1 mt-2 mb-2"><?php echo $row["nbre_notes"];?></td>
                                    <td class=" mr-4 ml-1 mt-2 mb-2"><?php echo ($row["nbre_notes"]<=1)?'note':'notes'?></td>
                                <?php }?>
                            </tr>
                        <?php }?>
                    </tbody>
                </table>
            </section>

            <?php }?>
        </div>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
</body>
</html>
