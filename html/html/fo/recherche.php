<?php
    session_start();
    // Chemins quand on est dans le dossier html
    require_once __DIR__ . '/../../php/verif_role_fo.php';
    require_once __DIR__ . '/../../01_premiere_connexion.php';
    include (__DIR__."/../../php/recup_coord.php");
    include __DIR__. '/../../php/requetesBDD/recup_FA.php';
    include __DIR__. '/../../php/requetesBDD/recup_panier.php';
    include __DIR__. '/../../php/maj_cookie.php';

    //récupère toutes les infos des tables produits et photos
    $tabProduit = [];
    $tabVendeur = [];

    foreach($dbh->query("SELECT pr.id_produit, libelle_produit, description_produit, prix_ttc, prix_remise, quantite_stock, id_categorie, pr.id_vendeur, note_moyenne, ph.id_photo, url_photo, alt, titre, id_compte, pu.id_promotion, label
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

    foreach ($dbh->query("SELECT id_compte, raison_sociale FROM sae3_skadjam._vendeur", PDO::FETCH_ASSOC) as $vendeur) {
        $tabVendeur[] = $vendeur;
    }
    // Récupération du role pour le panier sur la page recherche
    $role = '';
    $role = $_SESSION['role'];

    // Variable pour savoir s'il faut afficher une popup
    $addPanier = (isset($_GET['addPanier']) && $_GET['addPanier'] === "1");
    $removePanier = (isset($_GET['removePanier']) && $_GET['removePanier'] === "1");
    $addFA = (isset($_GET['addFA']) && $_GET['addFA'] === "1");
    $removeFA = (isset($_GET['removeFA']) && $_GET['removeFA'] === "1");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />
    
    <title>Recherche</title>
    <?php include __DIR__ . "/../../php/structure/head_front.php"; ?>
    
    <script>
        const tabProd = <?php echo json_encode($tabProduit);?>;
        const tabVendeur = <?php echo json_encode($tabVendeur);?>;
        let role = <?php echo json_encode($role);?>;
    </script>
    
    <script src="../../js/recherche.js"></script>
    <script src="../../js/affichageListeProduits.js"></script>
    <script src="../../js/fo/affichageProduit.js"></script>
    <script src="../../js/pagination.js"></script>
    <script src="../../js/tris.js"></script>
    <script src="../../js/filtres.js"></script>
    <script src="../../js/affichageNote.js"></script> 
   
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

</head>


<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <!-- Box question nb prod a mettre au panier -->
        <div id="fondNbAddPanier" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-40"></div>

        <div id="contNbAddPanier" class="hidden fixed bg-white top-1/2 left-1/5 justify-center items-center flex-col p-4 z-50 w-3/5 md:top-2/5 md:w-2/5 md:left-3/10 h-45">
            <form action="/php/traitementFAPanier.php" method="get" id="formNbAddPanier" class="flex flex-col items-center w-full h-full space-y-4">
                <label id="validAjout" class="hidden" for="nbAddPanier">Combien voulez-vous en ajouter au panier ?</label>
                <p id="valideRetrait" class="hidden">Etes-vous sur de vouloir retirer ce produit de votre panier ?</p>
                <input placeholder="5" class="hidden pl-3 border-2 border-vertClair rounded-2xl w-20 m-2 placeholder-gray-500" type="number" name="nbAddPanier" id="nbAddPanier" min="1">
                <input type="hidden" name="ajout" value="panier">
                <input type="hidden" name="vientDe" value="recherche">
                <div class="flex flex-row justify-around w-full">
                    <p id="btnRetour" class="flex items-center justify-center border-2 border-vertClair rounded-2xl w-25 h-12 cursor-pointer">Retour</p>
                    <input class="border-2 border-vertClair rounded-2xl w-25 h-12 cursor-pointer" type="submit" value="Valider">
                </div>
            </form>
        </div>
    <script>
        //Récup de la fenetre a affiché
        let contNbAddPanier = document.getElementById("contNbAddPanier")
        let fondNbAddPanier = document.getElementById("fondNbAddPanier")
        let formNbAddPanier = document.getElementById("formNbAddPanier")
        let input = document.createElement("input")

        input.type = "hidden"
        let btnOK = ''

        // Affichage dans la fenetre
        let p = document.getElementById("valideRetrait")
        let label = document.getElementById("validAjout")
        let inputNb = document.getElementById("nbAddPanier")
    </script>

    <main class="md:min-h-[900px] min-h-[600px]" id="produits">

        <!-- Aside -->
        <aside class="z-8 sidebar hidden overflow-auto bg-beige/90 w-110 min-h-[500px] fixed top-0 bottom-10 md:bg-beige p-4 md:sticky md:block md:w-79 md:h-230 md:top-14 md:float-left ">
            
            <!-- Filtres -->
            <section>
                <div class="flex flex-row justify-between">
                    <h3>Filtres</h3>
                    <p id="nbProd" class="flex self-center md:hidden">Nbre produit(s): <?php echo count($tabProduit);?></p>
                    <img id="fermerSidebar" src="../../images/logo/bootstrap_icon/x-large.svg" alt="Fermer" class="flex self-center w-8 md:hidden">
                </div>
                <!-- Vendeurs -->
                <div>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par vendeur</summary>
                        <?php 
                        foreach ($tabVendeur as $v) {
                            if($v['raison_sociale'] != 'Anonyme'){
                                $raisonSociale = $v['raison_sociale'];
                                $idVendeur = $v['id_compte'];
                                ?>
                                <div>
                                    <input class="vendeur h-5 w-5" type="checkbox" name="<?php echo $idVendeur; ?>" id="<?php echo $idVendeur; ?>" value="<?php echo $idVendeur; ?>" class="triFiltre h-5 w-5">
                                    <label for="<?php echo $idVendeur; ?>" class="labelDetails"><?php echo $raisonSociale; ?></label>
                                </div><?php 
                            }
                        } 
                        ?>
                    </details>
                </div>
                <!-- Categorie -->
                    <article>
                    <details>
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
                <!-- prix -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par prix</summary>
                        <div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTriCroissant" value="croissant" class="triFiltre h-5 w-5">
                                <label for="prixTriCroissant" class="labelDetails">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTriDecroissant" value="decroissant" class="triFiltre h-5 w-5">
                                <label for="prixTriDecroissant" class="labelDetails">Décroissant</label>
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
                                <input type="radio" name="alphaTri" id="alphaTriAZ" value="az" class="triFiltre h-5 w-5">
                                <label for="alphaTriAZ" class="labelDetails">A-Z</label>
                            </div>
                            <div>
                                <input type="radio" name="alphaTri" id="alphaTriZA" value="za" class="triFiltre h-5 w-5">
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
                                <input type="radio" name="noteTri" id="noteTri51" value="51" class="triFiltre h-5 w-5">
                                <label for="noteTri51" class="labelDetails">5-1</label>
                            </div>
                            <div>
                                <input type="radio" name="noteTri" id="noteTri15" value="15" class="triFiltre h-5 w-5">
                                <label for="noteTri15" class="labelDetails">1-5</label>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </aside>

        <!-- Barre de recherche -->
        <div class="bg-white sticky z-2 p-2 top-0 md:top-14 md:flex md:flex-row md:justify-center">
            <input type="text" id="recherche" maxlength="100" class="border-4 bg-white border-vertClair rounded-xl placeholder-gray-500 md:w-267 w-95 p-2" placeholder="Rechercher un produit...">
        </div>

        <div class="sticky top-15 bg-white border-b-2 border-b-vertFonce z-1 md:border-0 flex justify-between md:hidden">
            <button id="filtresTris" class="underline  m-2">Filtres & tris</button>
            <button id="mouvMapTel" class="underline m-2">Ouvrir la carte</button>
        </div>


        <div id="listeProduit" class="flex flex-col items-center">
            <div id="prod" class="flex flex-row flex-wrap justify-around w-auto">
                <script>
                    document.addEventListener("DOMContentLoaded", () => {
                        afficherListe(tabProd, tabFA, tabPanier, role)
                    });
                </script>
            </div>

            <!-- Pagination en fonction du nb de produits ou affichage s'il n'y en a aucun -->
            <script>affichagePagination(tabProd)</script>
        </div>

        <?php $dbh = null;?>

        <div id="contMap" class="hidden z-40 p-2 w-9/10 h-54 fixed right-5 bottom-22 md:flex md:flex-row md:w-1/3 md:h-85 md:bottom-5">
            <p id="mouvMap" class=" cursor-pointer hidden md:block text-bleu text-center rounded-l-md bg-vertFonce/90 w-6 h-6">></p>
            <div id="map" class="w-full h-50 border-vertFonce border-2 rounded-2xl md:rounded-l-none md:rounded-b-2xl md:rounded-tr-2xl  bg-vertFonce/90 md:h-80 md:p-2 "></div>
        
        <script>
            ajoutEventListener()
            var map =  L.map('map').setView([48, -3], 7)
        </script>

        <!-- Liste des popup de la page -->
        <div id="popup-overlay" class="right-12 md:right-40">
            <?php if($addPanier){ ?>
            <!---popup ajout d'un produit dans le panier--->
            <div id="popup-ajouter-panier" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été ajouté à votre panier !</p>
                <div class="flex justify-around mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                    <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
                </div>
            </div>
            <?php } ?>

            <?php if($removePanier){ ?>
            <!---popup retrait d'un produit du panier--->
            <div id="popup-retirer-panier" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été retiré de votre panier !</p>
                <div class="flex justify-around mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                    <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
                </div>
            </div>
            <?php } ?>

            <?php if($addFA){ ?>
            <!---popup ajout d'un produit aux futurs achats--->
            <div id="popup-ajouter-fa" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été ajouté à vos futurs achats !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
            <?php } ?>

            <?php if($removeFA){ ?>
            <!---popup retrait d'un produit aux futurs achats--->
            <div id="popup-retirer-fa" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été retiré de vos futurs achats !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
            <?php } ?>

        </div>
        <script src="../../js/pointeur.js"></script>
        <script src="../../js/fo/map.js"></script>
        <script type="module" src="../../js/fo/popupRecherchePanier.js"></script>
    </main>

    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>