<?php 
    include(__DIR__ . "/../../01_premiere_connexion.php");
    include(__DIR__ . "/../../php/fonctions.php");

    /* Temporaire pour le dev, à changer au moment de la finalisation */
    if (isset($_GET["idProduit"])) {
        $idProd = $_GET["idProduit"]; // Récupère l'id du produit qu'on affiche
    }
    else { 
        $idProd = 3;
    }
    /* Temporaire pour le dev, à changer au moment de la finalisation */

    
    

    // Requête pour récupérer les infos du produit
    foreach($dbh->query("SELECT *, est_masque::char AS est_masque_char
                         FROM sae3_skadjam._produit pr
                         WHERE pr.id_produit = $idProd"
                        , PDO::FETCH_ASSOC) as $row){
        $produit = $row;
    }

    // Requête pour récupérer les infos de la catégorie du produit
    foreach ($dbh->query("SELECT libelle_categorie
                                      FROM sae3_skadjam._categorie ca
                                      WHERE ca.id_categorie =" . $produit["id_categorie"], 
                                      PDO::FETCH_ASSOC) as $row) {
        $categorie = $row;
    };

    // Requête pour récupérer les infos du vendeur du produit
    foreach ($dbh->query("  SELECT *
                            FROM sae3_skadjam._vendeur ven
                            WHERE ven.id_compte =" . $produit["id_vendeur"], 
                                      PDO::FETCH_ASSOC) as $row) {
        $vendeur = $row;
    };

    // Requête pour récupérer l'url de la photo
    foreach ($dbh->query("  SELECT url_photo, alt, titre
                            FROM sae3_skadjam._montre
                            INNER JOIN sae3_skadjam._photo
                            ON _photo.id_photo = _montre.id_photo
                            WHERE _montre.id_produit =" . $idProd, 
                                      PDO::FETCH_ASSOC) as $row) {
        $infoPhoto = $row;
    };
    
    // Définition des variables PHP pour récupérer chaque donnée nécessaire
    $libelleProd = $produit["libelle_produit"]; // Nom du produit
    $libelleCat = $categorie["libelle_categorie"]; //Libellé de la catégorie
    $prixTTC = $produit["prix_ttc"]; // Prix du produit
    $produitStock = $produit["quantite_stock"]; // Récupère le stock du produit pour savoir si il est disponible ou non
    $nomVendeur = $vendeur["raison_sociale"];
    $produitDesc = $produit["description_produit"];

    // Affichage test
    //  print_r($infoPhoto);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require(__DIR__ . "/../../php/structure/head_front.php") ?>
    <title> <?php echo $produit["libelle_produit"] ?></title>
</head>
<body>
    <?php require(__DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php require(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="p-4 md:pl-8 pr-8">
        <!-- Section Description -->
        <section class="flex flex-col ">
            <article class="p-2 md:pb-8"> <!-- Titrage -->
                <h3> <?php echo $libelleProd; ?></h3>
                <p class="ml-4">Catégorie : <?php echo $libelleCat; ?></p>
            </article>
            
            <article class="md:flex md:flex-row md:justify-around">
                <img src="<?php echo $infoPhoto["url_photo"]; ?>"  alt=<?php echo $infoPhoto["alt"] ?> title=<?php echo $infoPhoto["titre"] ?>
                class="size-1/2 border
                       md:size-1/4">

                <div class="p-2 flex flex-col items-start md:items-center">
                    <div class="flex md:flex-col md:mb-4">
                        <h3 class="text-center pr-2 self-center"> <?php echo $prixTTC ?>€</h3>
                        <p class="text-center pl-2 mt-1 self-center">
                            <?php 
                                if ($produitStock > 0) { // Le stock est supérieur à 0, le produit est disponible
                                    echo "Disponible"; 
                                }
                                else { // Pour 
                                    echo "Indisponible";
                                }
                            ?>
                        </p>
                    </div>
                    
                    <div class="flex md:flex-col">
                        <p class="text-center pr-1 md:p-0">Vendu par</p>
                        <p class="text-center font-medium pl-1 md:p-0"><?php echo $nomVendeur ?></p>
                    </div>
                    
                    
                    <button class="-indent-96 overflow-hidden whitespace-nowrap
                    size-12 bg-no-repeat bg-size-[auto_48px]
                    bg-[url(/images/logo/bootstrap_icon/plus.svg)]
                    md:size-auto md:bg-none md:indent-0 md:overflow-visible md:whitespace-normal
                    md:bg-beige md:shadow md:rounded-2xl md:w-40 md:h-14 md:mt-4">
                        Ajouter au panier
                    </button>
                </div>
            </article>
        </section>

        <!-- Section Description détaillée -->
        <section class="md:mt-4">
            <h3>Description détaillée</h3>
            <p class="pt-2 pb-4">
                <?php echo $produitDesc; ?>
            </p>
        </section>

        <!-- Section avis -->
        <section>
            <div class="flex justify-between items-center">
                <h3>Avis</h3>
                <button class="size-auto bg-none indent-0 overflow-visible whitespace-normal
                                bg-beige shadow rounded-2xl w-40 h-14 mt-4">
                    <a href="ajouter_avis.php?idProduit=<?php echo $idProd;?>">Ajouter un avis</a>
                </button>
            </div>
            <section class=" m-2 md:ml-32">
                <?php 
                    $avis = [];
                    foreach($dbh->query("SELECT * FROM sae3_skadjam._avis a 
                                        INNER JOIN sae3_skadjam._client c 
                                            ON a.id_compte = c.id_compte 
                                        WHERE id_produit = $idProd", PDO::FETCH_ASSOC) as $row){
                        $avis[] = $row;
                    }
                    
                    if($avis == null){?>
                        <p>Aucun commentaire associé à ce produit.</p>
                    <?php }
                    else{
                        foreach($avis as $row){?>
                        
                        <section class=" bg-bleu rounded-2xl m-4 p-4 md:w-2/3">
                            <div class="flex flex-nowrap justify-start items-center w-auto">
                                <h4 class="mr-4">
                                    <?php echo $row['pseudo'];?>
                                </h4>
                                <?php echo affichageNote($row['nb_etoile']);?>
                            </div>
                            <p><?php echo $row['contenu_commentaire'];?></p>     
                        </section>
                    <?php }
                    }?>
                
            </section>
        </section>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>
</html>