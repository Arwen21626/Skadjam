<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_fo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    const PAGE_SIZE = 15;
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
</head>

<body>
    <script>
        const tabProd = <?php echo json_encode($tabProduit);?>
        //initialisation du numéro de page
        const PAGE_SIZE = 15;
        var $_GET = [];
        // Récupération dans l'url
        var parts = window.location.search.substr(1).split("&");
        for (var i = 0; i < parts.length; i++) {
            var temp = parts[i].split("=");
        }

        if(temp == ""){
            pageNumber = 1 
        }else{
            pageNumber = temp[1]
        }

        //console.log($_GET['id']); // Affiche la valeur du paramètre 'id'   

        let maxPage = (tabProd.length)/PAGE_SIZE
        console.log(maxPage)

        let lignes = tabProd.slice(pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE)
        console.log(lignes)
    </script>

    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="md:min-h-[800px] min-h-[600px] mt-10">
        <!-- Barre de recherche -->
        <aside class="sidebar w-60 p-5 bg-beige">
            <!-- Filtres -->
            <section>
                <h3>Filtres</h3>
                <!-- Categorie -->
                 <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par catégorie</summary>
                        <!-- Alimentaire -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire">
                            <label for="alimentaire">Alimentaire</label>
                        </div>
                        

                        <!-- Vetements -->
                        <div>
                            <input type="checkbox" name="Vetement" id="Vetement">
                            <label for="Vetement">Vetements</label>
                        </div>

                        <!-- Artisanat -->
                        <div>
                            <input type="checkbox" name="Artisanat" id="Artisanat">
                            <label for="Artisanat">Artisanat</label>
                        </div>

                        <!-- Goodies -->
                        <div>
                            <input type="checkbox" name="Goodies" id="Goodies">
                            <label for="Goodies">Goodies</label>
                        </div>

                        <!-- Soin -->
                        <div>
                            <input type="checkbox" name="Soin" id="Soin">
                            <label for="Soin">Soin</label>
                        </div>
                    </details>
                 </article>
                
                <!-- Notes -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par note</summary>
                        <!-- 1 étoile -->
                        <div>
                            <input type="checkbox" name="unE" id="unE">
                            <label for="unE">1</label>
                        </div>

                        <!-- 2 étoiles -->
                        <div>
                            <input type="checkbox" name="deuxE" id="deuxE">
                            <label for="deuxE">2</label>
                        </div>

                        <!-- 3 étoiles -->
                        <div>
                            <input type="checkbox" name="troisE" id="troisE">
                            <label for="troisE">3</label>
                        </div>

                        <!-- 4 étoiles -->
                        <div>
                            <input type="checkbox" name="quatreE" id="quatreE">
                            <label for="quatreE">4</label>
                        </div>

                        <!-- 5 étoiles -->
                        <div>
                            <input type="checkbox" name="cinqE" id="cinqE">
                            <label for="cinqE">5</label>
                        </div>
                    </details>
                </article>
                
                <!-- Tranche de prix -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par tranche de prix</summary>
                        <div>
                            <input type="checkbox" name="prix1" id="prix1">
                            <label for="prix1">2,99€ - 8,39€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix2" id="prix2">
                            <label for="prix2">8,40€ - 13,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix3" id="prix3">
                            <label for="prix3">13,20€ - 19,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix4" id="prix4">
                            <label for="prix4">19,20€ - 31,19€ </label>
                        </div>
                        <div>
                            <input type="checkbox" name="prix5" id="prix5">
                            <label for="prix5">31,19€ - 71,99€ </label>
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
                                <input type="radio" name="prixTri" id="prixTriCroissant">
                                <label for="prixTriCroissant">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTriDecroissant">
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
                                <input type="radio" name="alphaTri" id="alphaTriAZ">
                                <label for="alphaTriAZ">A-Z</label>
                            </div>
                            <div>
                                <input type="radio" name="alphaTri" id="alphaTriZA">
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
                                <input type="radio" name="noteTri" id="noteTri51">
                                <label for="noteTri51">5-1</label>
                            </div>
                            <div>
                                <input type="radio" name="noteTri" id="noteTri15">
                                <label for="noteTri15">1-5</label>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </aside>

        <div id="prod" class="grid grid-cols-2 justify-items-center md:grid-cols-3">
            <script>
                // Boucle pour afficher tous les produits
                tabProd.forEach(prod => {
                    idProduit = prod['id_produit']
                    let parent = document.getElementById("prod")

                    // Section   
                    let produit = document.createElement("section")
                    parent.appendChild(produit)
                    produit.classList.add("bg-bleu", "grid", "grid-cols-[40%_60%]", "w-40", "md:w-80", "h-auto", "p-2", "md:p-3", "m-2")
                    parent = produit

                    //Lien
                    let lien = document.createElement("a")
                    lien.href = "details_produit.php?idProduit="+idProduit
                    lien.classList.add("col-span-2", "justify-self-center", "mb-3");
                    parent.appendChild(lien)

                    parent = lien

                    // Image
                    let image = document.createElement("img")
                    image.src = prod['url_photo']
                    image.alt = prod['alt']
                    image.title = prod['title']
                    parent.appendChild(image)

                    // Nom produit
                    let nom = document.createElement("p")
                    nom.textContent = prod['libelle_produit']
                    parent.appendChild(nom)
                    nom.classList.add("col-span-2")

                    // Prix et note
                    let contient = document.createElement("div")
                    parent.appendChild(contient)
                    contient.classList.add("flex", "justify-start", "items-center", "col-span-2")

                    parent = contient

                    // Prix
                    let prix = document.createElement("p")
                    prix.textContent = prod['prix_ttc']+" €"
                    parent.appendChild(prix)

                    // Note
                    let contientNote = document.createElement("div")
                    parent.appendChild(contientNote)
                    contientNote.classList.add("w-2/4", "ml-2", "md:ml-10", "flex")

                    parent = contientNote
                    // console.log(parent)

                    let note = prod['note_moyenne']
                    affichageNote(note, parent)
                    
                    //setTimeout(function(){console.log('Code waits for 1  second')}, 1000);
                
                });
            </script>
        </div>
        <!-- str_replace-->                    
        <?php $dbh = null;?>
        
        <!--fin du catalogue-->
        <script>
            parent = document.getElementsByTagName("main")[0]
            // Pour avoir seulement le main et pas le tableau renvoyé
            let pageChangement = document.createElement("div")
            pageChangement.classList.add("flex", "flex-row", "space-x-4", "justify-center")
            parent.appendChild(pageChangement)

            parent = pageChangement

            if(pageNumber > 1){
                let pagePrec = document.createElement("a")
                pagePrec.href = "recherche.php?page="+(pageNumber-1)+"#nosProduits"

                pagePrec.textContent = "Page précédente"
                pagePrec.classList.add("lienPage","hover:text-rouge")

                parent.appendChild(pagePrec)
            }

            if (pageNumber < maxPage){
                let pageSuiv = document.createElement("a")
                pageSuiv.href = "recherche.php?page="+(pageNumber+1)+"#nosProduits"

                pageSuiv.textContent = "Page suivante"
                pageSuiv.classList.add("lienPage","hover:text-rouge")

                parent.appendChild(pageSuiv)
            }
        </script>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>