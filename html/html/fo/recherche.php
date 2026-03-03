<?php
    session_start();
    // Chemins quand on est dans le dossier html
    require_once __DIR__ . '/../../php/verif_role_fo.php';
    require_once __DIR__ . '/../../01_premiere_connexion.php';
    include (__DIR__."/../../php/recupCoord.php");

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


    
    <main class="md:min-h-[900px] min-h-[600px]" id="produits">
        <!-- Barre de recherche -->
        <input type="text" id="recherche" maxlength="100" class="border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-267 w-95 p-2 md:ml-7 ml-6 mt-4 mb-4" placeholder="Rechercher un produit...">
        
        <div class="flex justify-between">
            <button id="filtresTris" class="md:hidden underline  m-2">Filtres & tris</button>
            <button id="carte" class="md:hidden underline m-2">Accéder à la carte</button>
        </div>
        <!-- Aside -->
        <aside class="sidebar hidden overflow-auto bg-beige/90 w-110 h-[871px] fixed top-0 bottom-10 md:bg-beige p-4 md:sticky md:block md:w-79 md:h-225 md:top-16 md:float-left z-10">
            
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
                            $raisonSociale = $v['raison_sociale'];
                            $idVendeur = $v['id_compte'];
                            ?>
                            <div>
                                <input class="vendeur h-5 w-5" type="checkbox" name="<?php echo $raisonSociale; ?>" id="<?php echo $idVendeur; ?>" value="<?php echo $idVendeur; ?>" class="triFiltre h-5 w-5">
                                <label for="<?php echo $raisonSociale; ?>" class="labelDetails"><?php echo htmlspecialchars($raisonSociale, ENT_QUOTES, 'UTF-8'); ?></label>
                            </div>
                        <?php } ?>
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

        <div id="map" class="w-[300px] h-[200px] solid border-vertFonce md:w-1/3 md:h-80 md:fixed md:bottom-0 md:right-0"></div>
        <script>
            ajoutEventListener()

            var nub;
            var map = L.map('map').setView([48, -3], 7);

            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; OpenStreetMap'
            }).addTo(map);

            var markers = L.markerClusterGroup({
                iconCreateFunction: function(cluster) {
                    var count = cluster.getChildCount();
                    var color = count < 5 ? '#86D0CC' : count < 10 ? '#588A87' : '#365452';
                    var textColor = count < 5 ? '#000000' : count < 10 ? '#FFFFFF' : '#FFFFFF';
                    return L.divIcon({
                        html: '<div style="background:' + color + '; display:flex; align-items:center; justify-content:center; border-radius: 20px; width: 40px; height: 40px; border:solid #365452 0.5px; color: ' + textColor + '"><b>' + count + '</b></div>',
                        className: 'custom-cluster',
                        iconSize: L.point(40, 40),  
                    });
                }
            });
            
            var pointer = L.icon({
                iconUrl: '../../images/logo/pointeurVertFonce.png',
                iconSize: [45, 70],
            });

            coord.forEach(function(element) {
                markers.addLayer(
                    L.marker([element.latitude, element.longitude], { icon: pointer, id_compte: element.id_compte }).bindPopup(element.raison_sociale),
                )
            })

            map.addLayer(markers)
            
            markers.on("click", function(e) {
                checkedVendeurs.splice(checkedVendeurs.indexOf(e.layer.options.id_compte), 1)
                console.log("ID du point :", e.layer.options.id_compte);
                
                // filtre vendeur à mettre ici
                checkedVendeurs.push(e.layer.options.id_compte)
                console.log(checkedVendeurs)
                tab = filtre()
                mettreAJourListe(tab)
            })
        </script>
        
    </main>

    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_front.php"; ?>
    
</body>
</html>