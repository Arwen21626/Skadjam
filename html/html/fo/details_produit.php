
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require("html/php/structure/head_front.php") ?>
    <link rel="stylesheet" href="/html/css/output.css">
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
                    <button></button>
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

            </div>
        </section>
    </main>

    <?php require("html/php/structure/footer_front.php") ?>
</body>
</html>