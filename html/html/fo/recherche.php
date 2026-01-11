<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_fo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../../connections_params.php");

    //récupère toutes les infos des tables produits et photos
    $tabProduit = [];
    foreach($dbh->query("SELECT *
                        FROM sae3_skadjam._produit pr
                        INNER JOIN sae3_skadjam._montre m
                            ON pr.id_produit=m.id_produit
                        INNER JOIN sae3_skadjam._photo ph  
                            ON ph.id_photo = m.id_photo 
                        INNER JOIN sae3_skadjam._vendeur v
                            ON pr.id_vendeur = v.id_compte
                        WHERE pr.est_supprime = false AND pr.est_masque = false"
                        , PDO::FETCH_ASSOC) as $row){
        $tabProduit[] = $row;
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css" >
    <title>Recherche</title>
    <?php include __DIR__ . "/../../php/structure/head_front.php"; ?>
    <script src="../../js/fo/recherche.js"></script>
    <script src="../../js/fo/julien.js"></script>
</head>

<body>
    <script>
        const tabProd = <?php echo json_encode($tabProduit);?>;
        const maxPages = (tabProd.length)/numberOfItems
        // let lignes = calculNbPages(tabProd)[0]
    </script>

    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="md:min-h-[800px] min-h-[600px] mt-10">
        <!-- Barre de recherche -->
        <aside class="sidebar scroller bg-beige p-4 -translate-x-full active:translate-0">
            <!-- Filtres -->
            <section>
                <h3>Filtres</h3>
                <!-- Categorie -->
                 <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par catégorie</summary>
                        <!-- Alimentaire -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire" value="aimentaire">
                            <label for="alimentaire">Alimentaire</label>
                        </div>
                        

                        <!-- Vetements -->
                        <div>
                            <input type="checkbox" name="vetement" id="vetement" value="vetement">
                            <label for="vetement">Vetements</label>
                        </div>

                        <!-- Artisanat -->
                        <div>
                            <input type="checkbox" name="artisanat" id="artisanat" value="artisanat">
                            <label for="artisanat">Artisanat</label>
                        </div>

                        <!-- Goodies -->
                        <div>
                            <input type="checkbox" name="goodies" id="goodies" value="goodies">
                            <label for="goodies">Goodies</label>
                        </div>

                        <!-- Soin -->
                        <div>
                            <input type="checkbox" name="soin" id="soin" value="soin">
                            <label for="soin">Soin</label>
                        </div>
                    </details>
                 </article>
                
                <!-- Notes -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par note</summary>
                        <!-- non noté -->
                        <div>
                            <input type="checkbox" name="zeroE" id="zeroE" value="zeroE">
                            <label for="zeroE">Non noté</label>
                        </div>

                        <!-- 1 étoile -->
                        <div>
                            <input type="checkbox" name="uneE" id="uneE" value="uneE">
                            <label for="uneE">1</label>
                        </div>

                        <!-- 2 étoiles -->
                        <div>
                            <input type="checkbox" name="deuxE" id="deuxE" value="deuxE">
                            <label for="deuxE">2</label>
                        </div>

                        <!-- 3 étoiles -->
                        <div>
                            <input type="checkbox" name="troisE" id="troisE" value="troisE">
                            <label for="troisE">3</label>
                        </div>

                        <!-- 4 étoiles -->
                        <div>
                            <input type="checkbox" name="quatreE" id="quatreE" value="quatreE">
                            <label for="quatreE">4</label>
                        </div>

                        <!-- 5 étoiles -->
                        <div>
                            <input type="checkbox" name="cinqE" id="cinqE" value="cinqE">
                            <label for="cinqE">5</label>
                        </div>
                    </details>
                </article>
                
                <!-- Tranche de prix -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par tranche de prix</summary>
                        <div>
                            <input type="checkbox" name="prix1" id="prix1" value="prix1">
                            <label for="prix1">2,99€ - 8,39€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix2" id="prix2" value="prix2">
                            <label for="prix2">8,40€ - 13,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix3" id="prix3" value="prix3">
                            <label for="prix3">13,20€ - 19,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix4" id="prix4" value="prix4">
                            <label for="prix4">19,20€ - 31,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix5" id="prix5" value="prix5">
                            <label for="prix5">31,20€ - 71,99€ </label>
                        </div>
                    </details>
                </article>
            </section>

            <section>
                <h3>Tris</h3>
                <!-- prix -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par prix</summary>
                        <div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTriCroissant" value="croissant">
                                <label for="prixTriCroissant">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTriDecroissant" value="decroissant">
                                <label for="prixTriDecroissant">Décroissant</label>
                            </div>
                        </div>
                    </details>
                    
                </article>

                <!-- ordre alpha -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par ordre alphabétique</summary>
                        <div>
                            <div>
                                <input type="radio" name="alphaTri" id="alphaTriAZ" value="az">
                                <label for="alphaTriAZ">A-Z</label>
                            </div>
                            <div>
                                <input type="radio" name="alphaTri" id="alphaTriZA" value="za">
                                <label for="alphaTriZA">Z-A</label>
                            </div>
                        </div>
                    </details>
                    
                </article>

                <!-- note -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par note</summary>
                        <div>
                            <div>
                                <input type="radio" name="noteTri" id="noteTri51" value="51">
                                <label for="noteTri51">5-1</label>
                            </div>
                            <div>
                                <input type="radio" name="noteTri" id="noteTri15" value="15">
                                <label for="noteTri15">1-5</label>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </aside>

        <div id="prod" class="grid grid-cols-2 justify-items-center md:grid-cols-3">
            <script>
                afficherListe()
            </script>
        </div>

        <?php $dbh = null;?>
        
        <!--fin du catalogue-->
        <div class="flex flex-row justify-around">
            <button id="premierePage">Premiere page</button>
            <button id="pagePrec">Page prec</button>
            <button id="pageSuiv">Page suiv</button>
            <button id="dernierePage">Derniere page</button>
        </div>
        

        <script>
            ajoutEventListener()
        </script>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>