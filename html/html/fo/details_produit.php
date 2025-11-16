<?php 
    include("html/01_premiere_connexion.php");
    
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
    print_r($produit);
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require("html/php/structure/head_front.php") ?>
    <title> <?php echo $produit["libelle_produit"] ?></title>
</head>
<body>
    <?php require("html/php/structure/header_front.php"); ?>
    <?php require("html/php/structure/navbar_front.php"); ?>

    <main class="p-4">
        <!-- Section Description -->
        <section class="flex flex-col">
            <article class="p-2"> <!-- Titrage -->
                <h3> <?php echo $libelleProd; ?></h3>
                <p class="ml-4">Catégorie : <?php echo $libelleCat; ?></p>
            </article>
            
            <article>
                <img src="/html/images/photos_vrac_converties/cidre_breton_brut.webp" alt="PLACE-HOLDER" title="PLACE-HOLDER"
                class="size-1/2 border
                       md:size-1/3">
                <div class="p-2 flex flex-col items-start">
                    <div class="flex ">
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
                    
                    <p class="text-center">Vendu par <?php echo $nomVendeur ?></p>
                    <button>Ajouter au panier</button>
                </div>
            </article>
        </section>

        <!-- Section Description détaillée -->
        <section>
            <h3>Description détaillée</h3>
            <p>
                <?php echo $produitDesc; ?>
            </p>
        </section>

        <!-- Section avis -->
        <section>
            <h3>Avis "NB"</h3>
            <div id="avis_container">   <!-- Div dynamique qui contiendra tout les avis du produit -->
                <div class="avis">
                    <h4>"Pseudonyme"</h4>
                    <p>
                        Lorem ipsum dolor sit amet, consectetur adipisicing elit. 
                        Dolore, eum aut blanditiis iusto officiis est voluptates omnis laudantium possimus officia quia delectus voluptas deleniti similique debitis,
                        cum accusamus voluptate necessitatibus?
                    </p>
                </div>
            </div>
            <div id="notes_container">
                <h3>Notes</h3>
                <div>
                    <h4>"NB" notes</h4>
                    <div>
                        <p>5* - "NB" notes</p>
                        <p>4* - "NB" notes</p>
                        <p>3* - "NB" notes</p>
                        <p>2* - "NB" notes</p>
                        <p>1* - "NB" notes</p>
                    </div>
                    <button>Écrire un commentaire</button>
                </div>
            </div>
        </section>
    </main>

    <?php require("html/php/structure/footer_front.php") ?>
</body>
</html>