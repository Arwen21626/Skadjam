<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_fo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    const PAGE_SIZE = 15;
    require_once(__DIR__ . "/../../../connections_params.php");
    require_once(__DIR__ . "/../../php/fonctions.php");

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

    //initialisation du numéro de page
    if(isset($_GET['page'])&& $_GET['page']!==""){
        $pageNumber = $_GET['page'];
    }
    else{
        $pageNumber = 1;
    }

    $maxPage = sizeof($tabProduit)/PAGE_SIZE;

    //découpe le catalogue en page de 15 produits
    $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css" >
    <title>Recherche</title>
</head>
<script>
    const tabProd = <?php echo json_encode($tabProduit);?>
</script>
<body>
    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="md:min-h-[800px] min-h-[600px]">
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
                            <input type="checkbox" name="alimentaire" id="alimentaire">
                            <label for="alimentaire">Vetements</label>
                        </div>

                        <!-- Artisanat -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire">
                            <label for="alimentaire">Artisanat</label>
                        </div>

                        <!-- Goodies -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire">
                            <label for="alimentaire">Goodies</label>
                        </div>

                        <!-- Soin -->
                        <div>
                            <input type="checkbox" name="alimentaire" id="alimentaire">
                            <label for="alimentaire">Soin</label>
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
                            <label for="alimentaire">1</label>
                        </div>

                        <!-- 2 étoiles -->
                        <div>
                            <input type="checkbox" name="deuxE" id="deuxE">
                            <label for="alimentaire">2</label>
                        </div>

                        <!-- 3 étoiles -->
                        <div>
                            <input type="checkbox" name="troisE" id="troisE">
                            <label for="alimentaire">3</label>
                        </div>

                        <!-- 4 étoiles -->
                        <div>
                            <input type="checkbox" name="quatreE" id="quatreE">
                            <label for="alimentaire">4</label>
                        </div>

                        <!-- 5 étoiles -->
                        <div>
                            <input type="checkbox" name="cinqE" id="cinqE">
                            <label for="alimentaire">5</label>
                        </div>
                    </details>
                </article>
                
                <!-- Tranche de prix -->
                <article>
                    <details>
                        <summary class="cursor-pointer mt-1 mb-1">Par tranche de prix</summary>
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
                                <input type="radio" name="prixTri" id="prixTri">
                                <label for="prixTri">Croissant</label>
                            </div>
                            <div>
                                <input type="radio" name="prixTri" id="prixTri">
                                <label for="prixTri">Décroissant</label>
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
                                <input type="radio" name="alphaTri" id="alphaTri">
                                <label for="alphaTri">A-Z</label>
                            </div>
                            <div>
                                <input type="radio" name="alphaTri" id="alphaTri">
                                <label for="alphaTri">Z-A</label>
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
                                <input type="radio" name="noteTri" id="noteTri">
                                <label for="noteTri">5-1</label>
                            </div>
                            <div>
                                <input type="radio" name="noteTri" id="noteTri">
                                <label for="noteTri">1-5</label>
                            </div>
                        </div>
                    </details>
                </article>
            </section>
        </aside>

        <script>
            // Boucle pour afficher tous les produits
            tabProd.forEach(prod => {   
                idProduit = prod['id_produit']
                let parent = document.getElementsByTagName("main")[0]

                // Pour avoir seulement le main et pas le tableau renvoyé

                // Section   
                let produit = document.createElement("section")
                parent.appendChild(produit)

                parent = produit
                

                //Lien
                let lien = document.createElement("a")
                lien.href = "details_produit.php?idProduit="+idProduit
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

                // Prix et note
                let contient = document.createElement("div")
                parent.appendChild(contient)

                parent = contient

                // Prix
                let prix = document.createElement("p")
                prix.textContent = prod['prix_ttc']+" €"
                parent.appendChild(prix)

                // Note
                let contientNote = document.createElement("div")
                parent.appendChild(contientNote)

                parent = contientNote
                // console.log(parent)
            });


        </script>

        <!-- str_replace-->
        <div class="w-2/4 ml-2 md:ml-10 flex">
            <?php 
                $note = $valeurs['note_moyenne'];
                affichageNote($note); 
            ?>
        </div>                    

                            
        <?php $dbh = null;?>
        <!--fin du catalogue-->
        <div class="flex flex-row space-x-4 justify-center">
            <?php if ($pageNumber>1){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "recherche.php?page=".($pageNumber-1)."#nosProduits";?>">Page précédente</a>
            <?php }?>
        
            <?php if ($pageNumber<$maxPage){?>
            <a class= "lienPage hover:text-rouge" href="<?php echo "recherche.php?page=".($pageNumber+1)."#nosProduits";?>">Page suivante</a>
            <?php }?>
        </div>
        

        
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
    <script></script>
</body>
</html>