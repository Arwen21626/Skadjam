<?php 
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_bo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../../connections_params.php");
    $idCompte = $_SESSION['idCompte'];
    $tabProduit = [];
    foreach($dbh->query("SELECT *
        FROM sae3_skadjam._produit pr
        INNER join sae3_skadjam._montre m
            ON pr.id_produit=m.id_produit
        INNER JOIN sae3_skadjam._photo ph  
            ON ph.id_photo = m.id_photo
        INNER JOIN sae3_skadjam._vendeur v
            ON pr.id_vendeur = v.id_compte
        WHERE v.id_compte = $idCompte
            AND pr.est_supprime = false"
        , PDO::FETCH_ASSOC) as $row){

        $tabProduit[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__."/../../php/structure/head_back.php");?>
<head> 
    <title>Accueil</title>
    <script>
        const tabProd = <?php echo json_encode($tabProduit);?>;
    </script>
    <script src="../../js/index.js"></script>
    <script src="../../js/affichageListeProduits.js"></script>
    <script src="../../js/bo/affichageProduit.js"></script>
    <script src="../../js/paginationIndex.js"></script>
    <script src="../../js/tris.js"></script>
    <script src="../../js/filtres.js"></script>
    <script src="../../js/affichageNote.js"></script>
</head>


<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="p-8">
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="../bo/promotion_vendeur.php" title="lien vers page promotion" alt="promotion">
                <img src="../../images/images_accueil/promotion.webp" title="lien vers page promotion" alt="promotion" class="w-150 h-auto justify-self-end">
            </a>
            <a href="#vosProduits" id="nouveauxProduits" title="lien vers page derniers ajouts" alt="derniers ajouts">
                <img src="../../images/images_accueil/derniers_ajouts.webp" title="lien vers page derniers ajouts" alt="derniers ajouts" class="w-150 h-auto justify-self-start">
            </a>           
            <a href="../bo/stock.php" title="lien vers page stock" alt="stock">
                <img src="../../images/images_accueil/stock.webp" title="lien vers page stock" alt="stock" class="w-150 h-auto justify-self-end">
            </a>
            <a href="../bo/liste_commandes.php" title="lien vers page commandes" alt="commandes">
                <img src="../../images/images_accueil/commandes.webp" title="lien vers page commandes" alt="commandes" class="w-150 h-auto justify-self-start">
            </a>        
        </div>

        <div class="mt-15 flex flex-row justify-around">
            <a href="creation_produit.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Créer un produit</button></a>
            <a href="details_remises.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Consulter les remises</button></a>
            <a href="statistiques.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Mes statistiques</button></a>
            <a href="vider_catalogue.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Vider le catalogue</button></a>
        </div>

        <!--Début du catalogue-->
        <h2 id="vosProduits">Vos produits</h2>

        <?php if($tabProduit == null){ ?>
                    <p>Votre catalogue est vide.</p>
        <?php }
        else{ ?>
        <section id="listeProduit" class="flex flex-col items-center">
            <article id="prod" class="flex flex-row flex-wrap justify-around w-auto">
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        afficherListe(tabProd)
                    });
                </script>
            </article>
            <!--fin du catalogue-->
            <article id="changePage" class="flex flex-row justify-around w-96">
                <button id="pagePrec" class="md:order-2">|<</button>
                <button id="pageSuiv" class="md:order-4">>|</button>
                <p id="pageInfo" class="md:order-3"></p>
                <button id="premierePage" class="md:order-1"><<</button>
                <button id="dernierePage" class="md:order-5">>></button>
            </article>
        </section>
        <?php } ?>

        <?php $dbh = null;?>

        <script>
            ajoutEventListener()
        </script>
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
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>
</html>
