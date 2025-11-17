<?php 
    include(__DIR__ . "/../../01_premiere_connexion.php");
    
    /* Temporaire pour le dev, à changer au moment de la finalisation */
    if (isset($_GET["idProduit"])) {
        $idProd = $_GET["idProduit"]; // Récupère l'id du produit qu'on affiche
    }
    else { 
        $idProd = 1;
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
    
    // Définition des variables PHP pour récupérer chaque donnée nécessaire
    $libelleProd = $produit["libelle_produit"]; // Nom du produit
    $libelleCat = $categorie["libelle_categorie"]; //Libellé de la catégorie
    $prixTTC = $produit["prix_ttc"]; // Prix du produit
    $produitStock = $produit["quantite_stock"]; // Récupère le stock du produit pour savoir si il est disponible ou non
    $nomVendeur = $vendeur["raison_sociale"];
    $produitDesc = $produit["description_produit"];

    // Affichage test
    // print_r($produit);
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
                <img src="../../images/photos_vrac_converties/cidre_breton_brut.webp" alt="PLACE-HOLDER" title="PLACE-HOLDER"
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
                    bg-[url(/html/images/logo/bootstrap_icon/plus.svg)]
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
            <h3>Avis "NB"</h3>
            <div id="avis_container" class="mt-2 mb-2">   <!-- Div dynamique qui contiendra tout les avis du produit -->
                <!-- Div représentant un avis et sa potentielle réponse -->
                <div class="avis-reponse shadow">
                    <!-- Div représentant l'avis -->
                    <div class="bg-bleu p-2">
                        <h4 class="mb-0.5">"Pseudonyme"</h4>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                            Dolore, eum aut blanditiis iusto officiis est voluptates omnis laudantium possimus officia quia delectus voluptas deleniti similique debitis,
                            cum accusamus voluptate necessitatibus?
                        </p>
                    </div>
                    
                    <!-- Div représentant la réponse -->
                     <div class="bg-beige p-2">
                        <h4 class="mb-0.5"> <?php echo $nomVendeur ?> </h4>
                        <p>
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                            Dolore, eum aut blanditiis iusto officiis est voluptates omnis laudantium possimus officia quia delectus voluptas deleniti similique debitis,
                            cum accusamus voluptate necessitatibus?
                        </p>
                     </div>
                </div>
            </div>

            <div id="notes_container">
                <h3>Notes</h3>
                <div class="flex justify-between">
                    <div class="flex flex-col">
                        <h4>"NB" notes</h4>
                        <p>5* - "NB" notes</p>
                        <p>4* - "NB" notes</p>
                        <p>3* - "NB" notes</p>
                        <p>2* - "NB" notes</p>
                        <p>1* - "NB" notes</p>
                    </div>
                    <button class="-indent-96 overflow-hidden whitespace-nowrap
                    size-12 bg-no-repeat bg-size-[auto_48px]
                    bg-[url(/images/logo/bootstrap_icon/chat-left-dots.svg)]
                    md:size-auto md:bg-none md:indent-0 md:overflow-visible md:whitespace-normal
                    md:bg-beige md:shadow md:rounded-2xl md:w-40 md:h-14 md:mt-4 md:p-0.5">
                        Écrire un commentaire
                    </button>
                </div>
            </div>
        </section>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>
</html>