<?php include('../PAS_DE_COMMIT.php');?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/fo/index.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Accueil</title>
</head>



<body>
    <header>

    </header>

    <main>
        <section class="carreImages">

            <a href="" title="lien vers page promotion" id="image1">
                <img src="../../images/imagesAccueil/promotion.webp" alt="promotion">
            </a>
            <a href="" title="lien vers page derniers ajouts" id="image2">
                <img src="../../images/imagesAccueil/derniersAjouts.webp" alt="derniers ajouts">
            </a>           
            <a href="" title="lien vers page stock" id="image3">
                <img src="../../images/imagesAccueil/stock.webp" alt="stock">
            </a>
            <a href="" title="lien vers page commandes" id="image4">
                <img src="../../images/imagesAccueil/commandes.webp" alt="commandes">
            </a>        
        </section>

        <section id ="ligneBoutons">
            <a href="" class="bouton">Ajouter un produit</a>
            <a href="" class="bouton">Statistiques</a>
            <a href="" class="bouton">Avis récents</a>
        </section>

        <h2>Vos produits</h2>


        <!--Début du catalogue-->

        <?php
            $tabProduit = [];

            try {
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

                //gère les erreurs
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                //faire des tableaux associatifs au lieu de numérique
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                foreach($dbh->query("SELECT * from sae3_skadjam._produit", PDO::FETCH_ASSOC) as $row) {
                    $tabProduit[] = $row;
                    print_r($row);
                }


                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <img src="images/imagesProduits/" alt="">
        <h4></h4>
        <p></p>
        <!--nb étoiles-->

        <p>Voir plus ...</p>
    </main>
    
    
    <footer>

    </footer>
</body>

</html>
