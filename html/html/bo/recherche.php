<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_bo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../../connections_params.php");

    //récupère toutes les infos des tables produits et photos
    $idVendeur = $_SESSION['idCompte'];
    $tabProduit = [];
    foreach($dbh->query("SELECT *
        FROM sae3_skadjam._produit pr
        INNER join sae3_skadjam._montre m
            ON pr.id_produit=m.id_produit
        INNER JOIN sae3_skadjam._photo ph  
            ON ph.id_photo = m.id_photo
        INNER JOIN sae3_skadjam._vendeur v
            ON pr.id_vendeur = v.id_compte
        LEFT JOIN sae3_skadjam._promu pu
            ON pu.id_produit = pr.id_produit
        LEFT JOIN sae3_skadjam._promotion pm
            ON pu.id_promotion = pm.id_promotion
        WHERE v.id_compte = $idVendeur
            AND pr.est_supprime = false"
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
    <title>Recherche</title>
    <?php include __DIR__ . "/../../php/structure/head_back.php"; ?>
    <script>
        const tabProd = <?php echo json_encode($tabProduit);?>;
    </script>
    <script src="../../js/recherche.js"></script>
    <script src="../../js/affichageListeProduits.js"></script>
    <script src="../../js/bo/affichageProduit.js"></script>
    <script src="../../js/pagination.js"></script>
    <script src="../../js/tris.js"></script>
    <script src="../../js/filtres.js"></script>
    <script src="../../js/affichageNote.js"></script>
</head>

<body>
    
    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    
    <main class="min-h-[800px]">
        <!-- Barre de recherche -->
        <div class="bg-white sticky z-1 p-2 top-20 flex-row justify-center inline-block">
            <input type="text" id="recherche" maxlength="100" class="border-4 border-vertFonce rounded-xl placeholder-gray-500 w-267 p-2 ml-7 mt-4 mb-4" placeholder="Rechercher un produit...">
        </div>
        <button id="filtresTris" class="md:hidden underline  m-2">Filtres & tris</button>
        <!-- Aside -->
        <aside class="sidebar overflow-auto float-left bg-vertFonce text-bleu p-4 sticky w-79 h-200 top-20">
            <!-- Filtres -->
            <section>
                <h3>Filtres</h3>
                <!-- Categorie -->
                    <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par catégorie</summary>
                        <!-- Alimentaire -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire" value="alimentaire" class="triFiltre h-5 w-5">
                            <label for="alimentaire" class="labelDetails">Alimentaire</label>
                        </div>
                        

                        <!-- Vetements -->
                        <div>
                            <input type="checkbox" name="vetement" id="vetement" value="vetement" class="triFiltre h-5 w-5">
                            <label for="vetement" class="labelDetails">Vetements</label>
                        </div>

                        <!-- Artisanat -->
                        <div>
                            <input type="checkbox" name="artisanat" id="artisanat" value="artisanat" class="triFiltre h-5 w-5">
                            <label for="artisanat" class="labelDetails">Artisanat</label>
                        </div>

                        <!-- Goodies -->
                        <div>
                            <input type="checkbox" name="goodies" id="goodies" value="goodies" class="triFiltre h-5 w-5">
                            <label for="goodies" class="labelDetails">Goodies</label>
                        </div>

                        <!-- Soin -->
                        <div>
                            <input type="checkbox" name="soin" id="soin" value="soin" class="triFiltre h-5 w-5">
                            <label for="soin" class="labelDetails">Soin</label>
                        </div>
                    </details>
                    </article>
                
                <!-- Notes -->
                <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par note</summary>
                        <!-- non noté -->
                        <div>
                            <input type="checkbox" name="nonNote" id="nonNote" value="nonNote" class="triFiltre h-5 w-5">
                            <label for="nonNote" class="labelDetails">Non noté</label>
                        </div>

                        <!-- 1 étoile -->
                        <div>
                            <input type="checkbox" name="uneE" id="uneE" value="uneE" class="triFiltre h-5 w-5">
                            <label for="uneE" class="labelDetails">1</label>
                        </div>

                        <!-- 2 étoiles -->
                        <div>
                            <input type="checkbox" name="deuxE" id="deuxE" value="deuxE" class="triFiltre h-5 w-5">
                            <label for="deuxE" class="labelDetails">2</label>
                        </div>

                        <!-- 3 étoiles -->
                        <div>
                            <input type="checkbox" name="troisE" id="troisE" value="troisE" class="triFiltre h-5 w-5">
                            <label for="troisE" class="labelDetails">3</label>
                        </div>

                        <!-- 4 étoiles -->
                        <div>
                            <input type="checkbox" name="quatreE" id="quatreE" value="quatreE" class="triFiltre h-5 w-5">
                            <label for="quatreE" class="labelDetails">4</label>
                        </div>

                        <!-- 5 étoiles -->
                        <div>
                            <input type="checkbox" name="cinqE" id="cinqE" value="cinqE" class="triFiltre h-5 w-5">
                            <label for="cinqE" class="labelDetails">5</label>
                        </div>
                    </details>
                </article>
                
                <!-- Tranche de prix -->
                <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par tranche de prix</summary>
                        <div>
                            <input type="checkbox" name="prix1" id="prix1" value="prix1" class="triFiltre h-5 w-5">
                            <label for="prix1" class="labelDetails">0€ - 8,39€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix2" id="prix2" value="prix2" class="triFiltre h-5 w-5">
                            <label for="prix2" class="labelDetails">8,40€ - 13,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix3" id="prix3" value="prix3" class="triFiltre h-5 w-5">
                            <label for="prix3" class="labelDetails">13,20€ - 19,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix4" id="prix4" value="prix4" class="triFiltre h-5 w-5">
                            <label for="prix4" class="labelDetails">19,20€ - 31,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix5" id="prix5" value="prix5" class="triFiltre h-5 w-5">
                            <label for="prix5" class="labelDetails">31,20€ - 71,99€ </label>
                        </div>
                    </details>
                </article>
            </section>

            <section>
                <h3>Tris</h3>
                <!-- Stock -->
                <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par stock</summary>
                        <div>
                            <div>
                                <input type="radio" name="tri" id="stockTriCroissant" value="croissant" class="triFiltre h-5 w-5">
                                <label for="stockTriCroissant" class="labelDetails">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="tri" id="stockTriDecroissant" value="decroissant" class="triFiltre h-5 w-5">
                                <label for="stockTriDecroissant" class="labelDetails">Décroissant</label>
                            </div>
                        </div>
                    </details>
                    
                </article>
                <!-- Prix -->
                <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par prix</summary>
                        <div>
                            <div>
                                <input type="radio" name="tri" id="prixTriCroissant" value="croissant" class="triFiltre h-5 w-5">
                                <label for="prixTriCroissant" class="labelDetails">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="tri" id="prixTriDecroissant" value="decroissant" class="triFiltre h-5 w-5">
                                <label for="prixTriDecroissant" class="labelDetails">Décroissant</label>
                            </div>
                        </div>
                    </details>
                    
                </article>

                <!-- ordre alpha -->
                <article>
                    <details >
                        <summary class="cursor-pointer mt-1 mb-1">Par ordre alphabétique</summary>
                        <div>
                            <div>
                                <input type="radio" name="tri" id="alphaTriAZ" value="az" class="triFiltre h-5 w-5">
                                <label for="alphaTriAZ" class="labelDetails">A-Z</label>
                            </div>
                            <div>
                                <input type="radio" name="tri" id="alphaTriZA" value="za" class="triFiltre h-5 w-5">
                                <label for="alphaTriZA" class="labelDetails">Z-A</label>
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
                                <input type="radio" name="tri" id="noteTri51" value="51" class="triFiltre h-5 w-5">
                                <label for="noteTri51" class="labelDetails">5-1</label>
                            </div>
                            <div>
                                <input type="radio" name="tri" id="noteTri15" value="15" class="triFiltre h-5 w-5">
                                <label for="noteTri15" class="labelDetails">1-5</label>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </aside>
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

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>