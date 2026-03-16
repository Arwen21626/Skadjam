<?php
    session_start();
    include(__DIR__ . '/../../php/verif_role_bo.php');
    include(__DIR__."/../../01_premiere_connexion.php");
    require_once(__DIR__."/../../php/fonctions.php");

    //Récupération des données sur le produit ainsi que la photo
    $idProd = $_GET['idProduit'];
    
    $produit = "vide";
    foreach($dbh->query("SELECT pr.id_produit, pr.libelle_produit, pr.note_moyenne, c.libelle_categorie,
                                ph.url_photo, ph.alt, ph.titre, r.pourcentage_remise, pr.quantite_stock, 
                                pr.description_produit, pr.prix_ttc, pr.prix_remise, pr.seuil_alerte
                        from sae3_skadjam._produit pr
                        inner join sae3_skadjam._montre m
                            on pr.id_produit=m.id_produit
                        inner join sae3_skadjam._photo ph  
                            on ph.id_photo = m.id_photo
                        inner join sae3_skadjam._categorie c
                            on c.id_categorie = pr.id_categorie
                        left join sae3_skadjam._reduit rd
                            on rd.id_produit = pr.id_produit
                        left join sae3_skadjam._remise r
                            on r.id_remise = rd.id_remise
                        where pr.id_produit = $idProd"
                        , PDO::FETCH_ASSOC) as $row){
        $produit = $row;
    }

    if ($produit === "vide")
    {
        header("location:/html/bo/404_vendeur.php");
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require(__DIR__ . "/../../php/structure/head_back.php") ?>
    <title><?php echo $produit['libelle_produit']; ?></title>
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
        <p>Catégorie : <?php echo $produit['libelle_categorie']; ?></p>
        <!--affichage de la photo-->

        <!--carrousel à faire-->
        <article class="flex flex-row justify-around  m-4 p-4">
            <img class=" w-1/4" src="<?php echo $produit['url_photo'];?>" 
            alt="<?php echo $produit['alt'];?>"
            title="<?php echo $produit['titre'];?>">

            <section class="m-4 p-4 space-y-4 content-between">
                <!--affichage du prix-->
                <p class=" <?php echo ($produit['pourcentage_remise'] !== NULL)?'line-through':'';?>"> <?php echo htmlentities(str_replace(".", ",",$produit['prix_ttc'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                <p class=" <?php echo ($produit['pourcentage_remise'] !== NULL)?'':'hidden';?>"> <?php echo htmlentities(str_replace(".", ",",$produit['prix_remise'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                <!--affichage de la quantite-->
                <?php 
                $stock = $produit['quantite_stock'];
                if($stock != 0){?>
                    <p>En stock : <?php echo $stock; ?></p>
                <?php } 
                else{?>
                    <p>Produit indsponible</p>
                <?php }?>
                <p>Seuil d'alerte : <?php if($produit['seuil_alerte'] != 0){echo $produit['seuil_alerte'];} else{ echo 0;}?></p>

                <div class="flex flex-col space-y-4">
                    <a href="../bo/modifier_produit.php?idProduit=<?php echo $idProd?>"><button class=" bg-beige rounded-2xl w-40 h-14 cursor-pointer" type="button">Modifier</button></a>
                    <!-- <button class=" bg-beige rounded-2xl w-40 h-14" type="button">Masquer</button> -->
                    <a href="../bo/supprimer_produit.php?idProduit=<?php echo $idProd?>"><button class=" bg-beige rounded-2xl w-40 h-14 cursor-pointer" type="button">Supprimer</button></a>
                </div>
            </section>
        </article>

        <h3>Description détaillée</h3>
        <p><?php echo $produit['description_produit'];?></p>

        <!-- Avis -->
        <h3>Avis</h3>
        <article class="flex flex-row justify-between mt-10">
            <?php 
            // Recupération des avis
            $avis = [];
            foreach($dbh->query("SELECT a.*, c.pseudo
                                FROM sae3_skadjam._avis a 
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
                <?php foreach($avis as $row){
                        $idAvis = $row['id_avis'];
                        $stmt = $dbh->prepare("SELECT id_avis, raison_sociale, contenu_reponse FROM sae3_skadjam._reponse r
                                                INNER JOIN sae3_skadjam._vendeur v
                                                    ON r.id_compte = v.id_compte 
                                                WHERE id_avis = ?");
                        $stmt->execute([$idAvis]);
                        $reponse = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        $aReponse = (isset($reponse['id_avis'])) ? true : false;
                        ?>
                        <section class=" bg-bleu m-4 p-4 w-4xl <?php echo $aReponse?'mb-0 rounded-t-2xl':'rounded-2xl'?>">
                            
                            <div class="flex justify-between items-center py-2">
                                <!---pseudonyme--->
                                <h4><?php echo $row['pseudo'];?></h4>

                                <div class="flex items-center md:gap-4 gap-2">
                                    <!---note de l'avis--->
                                    <?php echo affichageNote($row['nb_etoile']); ?>

                                    <div class="flex items-center md:gap-1">
                                            <!---pouce haut--->
                                            <span id="like-count-<?= $row['id_avis'] ?>">
                                                <?= $row['nb_pouce_haut'] ?>
                                            </span>
                                            <img class="vote-btn like md:w-6 md:h-6 w-5 h-5" 
                                                    src="../../images/logo/bootstrap_icon/hand-thumbs-up.svg"
                                                    alt="icône pouce vers le haut si vous avez aimé l'avis" 
                                                    title="J'aime cet avis">
                                        </div>
                                    
                                        <!---pouce bas--->
                                        <div class="flex items-center md:gap-1">
                                            <span id="dislike-count-<?= $row['id_avis'] ?>">
                                                <?= $row['nb_pouce_bas'] ?>
                                            </span>
                                            <img class="vote-btn dislike md:w-6 md:h-6 w-5 h-5" 
                                                    src="../../images/logo/bootstrap_icon/hand-thumbs-down.svg"
                                                    alt="icône pouce vers le bas si vous n'avez pas aimé l'avis" 
                                                    title="Je n'aime pas cet avis">
                                        </div>
                                </div>
                                
                                <!---Répondre/modifier sa réponse--->
                                <?php if (!$aReponse){?>
                                    <a class="text-black text-center" href="./ajouter_reponse.php?idProduit=<?php echo $idProd;?>&idAvis=<?php echo $row['id_avis']?>">Répondre</a>
                                <?php }else{?>
                                    <a class="text-black text-center" href="./ajouter_reponse.php?idProduit=<?php echo $idProd;?>&idAvis=<?php echo $row['id_avis']?>">Modifier ma réponse</a>
                                <?php }?>
                            </div>
                            <p><?php echo $row['contenu_commentaire'];?></p>     
                        </section>

                        <!-- Réponse -->
                        <?php 
                        if(isset($reponse["id_avis"])){
                        ?>
                        <section class=" bg-beige m-4 mt-0 p-4 w-4xl rounded-b-2xl">
                            <div class="grid grid-cols-4 md:grid-cols-5 justify-items-end w-auto">
                                <h4 class="mr-4 col-span-2 md:col-span-3 justify-self-start">
                                    <?php echo $reponse['raison_sociale']; ?>
                                </h4>
                            </div>
                            <p><?php echo $reponse['contenu_reponse'];?></p>     
                        </section>
                        <?php }?>
                    <?php }?>
            </section>

            <!-- Notes -->
            <aside class="mr-16 p-5 bg-beige rounded-2xl sticky top-48 h-80 w-45 flex flex-col justify-center">
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
            </aside>

            <?php }?>
        </article>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
</body>
</html>
