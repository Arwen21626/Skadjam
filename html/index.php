<?php
    session_start();

    include __DIR__ . '/01_premiere_connexion.php';
    require_once __DIR__ . "/../connections_params.php";
    
    //récupère toutes les infos des tables produits et photos
    $tabProduit = [];

    foreach($dbh->query("SELECT pr.id_produit, pr.date_creation, libelle_produit, description_produit, prix_ttc, prix_remise, quantite_stock, id_categorie, pr.id_vendeur, note_moyenne, ph.id_photo, url_photo, alt, titre, id_compte, pu.id_promotion, label
                                    FROM sae3_skadjam._produit pr
                                    INNER JOIN sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    LEFT JOIN sae3_skadjam._promu pu
                                        ON pu.id_produit = pr.id_produit
                                    LEFT JOIN sae3_skadjam._promotion pm
                                        ON pu.id_promotion = pm.id_promotion
                                    WHERE pr.est_supprime = false AND pr.est_masque = false"
                        , PDO::FETCH_ASSOC) as $row){
        $tabProduit[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="fr">
    <head>
        <title>Accueil</title>

        <script>
            const tabProd = <?php echo json_encode($tabProduit);?>;
            const tabVendeur = <?php echo json_encode($tabVendeur);?>;        
        </script>
        
        <script src="js/index.js"></script>
        <script src="js/affichageListeProduits.js"></script>
        <script src="js/fo/affichageProduitIndex.js"></script>
        <script src="js/paginationIndex.js"></script>
        <script src="js/tris.js"></script>
        <script src="js/affichageNote.js"></script> 
    </head>    
<?php include __DIR__ . "/php/structure/head_front.php"; ?>
<body>
    <!--header-->
    
    <?php include __DIR__ . "/php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/php/structure/navbar_front.php"; ?>

    <main class="mt-10">
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="html/fo/promotion.php" title="lien vers page promotion" alt="promotion">
                <img src="images/images_accueil/promotion.webp" title="lien vers page promotion" alt="promotion" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="#nosProduits" id="nouveauxProduits" title="lien vers page nouveaux produits" alt="nouveaux produits">
                <img src="images/images_accueil/nouveaux_produits.webp" title="lien vers page nouveaux produits" alt="nouveaux produits" class="w-90 md:w-150 h-auto justify-self-start">
            </a>           
            <a href="#nosProduits" id="plusVendus" title="lien vers page les plus vendus" alt="les plus vendus">
                <img src="images/images_accueil/les_plus_vendus.webp" title="lien vers page les plus vendus" alt="les plus vendus" class="w-90 md:w-150 h-auto justify-self-end">
            </a>
            <a href="html/fo/liste_commandes.php" title="lien vers page commandes" alt="commandes">
                <img src="images/images_accueil/commandes.webp" title="lien vers page commandes" alt="commandes" class="w-90 md:w-150 h-auto justify-self-start">
            </a>        
        </div>

        <!--Début du catalogue-->
        <h2 id="nosProduits">Nos produits</h2>

        <section id="listeProduit" class="flex flex-col items-center">
            <article id="prod" class="flex flex-row flex-wrap justify-around w-auto">
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        afficherListe(tabProd)
                    });
                </script>
            </article>

            <!--fin du catalogue-->

            <!-- Pagination en fonction du nb de produits ou affichage s'il n'y en a aucun -->
            <script>affichagePagination(tabProd)</script>
        </section>

        <?php $dbh = null;?>

        <script>
            ajoutEventListener()
        </script>
        <script src="js/pointeur.js"></script>
    </main>
    
    <script>
        const nouveauxProduits = document.getElementById("nouveauxProduits");
        const plusVendus = document.getElementById("plusVendus");

        nouveauxProduits.addEventListener("click", function() {
            localStorage.setItem("triCatalogue", "nouveaux");
            triNouveauxProduits(tabProd);
        });

        plusVendus.addEventListener("click", function() {
            // triPlusVendus(tabProd);
        });

        function triNouveauxProduits(tableau){
            tableau.sort((a, b) => {
                return new Date(b.date_creation) - new Date(a.date_creation);
            });
            afficherListe(tableau);
        }

        document.addEventListener("DOMContentLoaded", () => {

            const tri = localStorage.getItem("triCatalogue");

            if(tri === "nouveaux"){
                triNouveauxProduits(tabProd);
            }else{
                afficherListe(tabProd);
            }

        });

        function appliquerTri(tableau){

            const tri = localStorage.getItem("triCatalogue");

            if(tri === "nouveaux"){
                tableau.sort((a, b) => new Date(b.date_creation) - new Date(a.date_creation));
            }

            return tableau;
        }
    </script>

    <!--footer-->
    <?php require __DIR__ . "/php/structure/footer_front.php"; ?>

</body>

</html>