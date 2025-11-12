<?php 
    include("html/01_premiere_connexion.php"); 
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require("html/php/structure/head_front.php") ?>
    <title>"Nom du produit"</title>
</head>
<body>
    <?php require("html/php/structure/header_front.php"); ?>
    <?php require("html/php/structure/navbar_front.php"); ?>

    <main>
        <!-- Section Description -->
        <section>
            <article> <!-- Titrage -->
                <h3>"Nom du produit"</h3>
                <p>Catégorie : "Catégorie du produit"</p>
            </article>
            
            <article>
                <img src="#jsp" alt="jsp">
                <div>
                    <h4>"Prix"€</h4>
                    <p>"Disponibilité"</p>
                    <p>Vendu par "Nom du vendeur"</p>
                    <button>Ajouter au panier</button>
                </div>
            </article>
        </section>

        <!-- Section Description détaillée -->
        <section>
            <h3>Description détaillée</h3>
            <p>
                Lorem ipsum dolor, sit amet consectetur adipisicing elit.
                Omnis hic quidem earum ad animi quo, illo, quas pariatur maiores nam natus. 
                Odit vitae blanditiis unde labore nemo. Provident, numquam harum!
            </p>
            <p>
                Lorem ipsum dolor, sit amet consectetur adipisicing elit. 
                Incidunt ad ratione reprehenderit, excepturi hic labore autem magnam doloremque harum at quia repellat earum ab voluptatem sed cupiditate iusto officiis nesciunt.
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