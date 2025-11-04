<?php include('PAS_DE_COMMIT.php');?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../css/index.css" >
    <title>Accueil</title>
</head>



<body>
    <header>

    </header>

    <main>
        <section class="carreImages">

            <a href="" title="lien vers page promotion" id="promotion">
                <img src="../images/imagesAccueil/promotion.webp" alt="promotion">
            </a>
            <a href="" title="lien vers page nouveaux produits" id="nouveauxProduits">
                <img src="../images/imagesAccueil/nouveauxProduits.webp" alt="nouveaux produits">
            </a>           
            <a href="" title="lien vers page les plus vendus" id="lesPlusVendus">
                <img src="../images/imagesAccueil/lesPlusVendus.webp" alt="les plus vendus">
            </a>
            <a href="" title="lien vers page commandes" id="commandes">
                <img src="../images/imagesAccueil/commandes.webp" alt="commandes">
            </a>        
        </section>


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