<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_fo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
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
<body>
    <!--header-->
    <?php (include __DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <main class="md:min-h-[800px] min-h-[600px]">
        <!-- Barre de recherche -->
        <aside class="sidebar">
            <!-- Filtres -->
            <section>
                <h3>Filtres</h3>
                <!-- Categorie -->
                 <article>
                    <h4>Par catégorie</h4>
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
                 </article>
                

                <!-- Notes -->
                <article>
                    <h4>Par note</h4>

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
                </article>
                
                <!-- Tranche de prix -->
                <article>
                    <h4>Par tranche de prix</h4>

                </article>

            </section>

            <section>
                <h3>Tris</h3>
                <!-- prix -->
                <article>
                    <h4>Par prix</h4>
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
                </article>

                <!-- ordre alpha -->
                <article>
                    <h4>Par ordre alphabétique</h4>
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
                </article>
                <!-- note -->
                <article>
                    <h4>Par note</h4>
                    <div>
                        <div>
                            <input type="radio" name="noteTri" id="noteTri">
                            <label for="noteTri">1-5</label>
                        </div>
                        <div>
                            <input type="radio" name="noteTri" id="noteTri">
                            <label for="noteTri">5-1</label>
                        </div>
                    </div>
                </article>
                
            </section>
            

            
        </aside>


        
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
    <script></script>
</body>
</html>