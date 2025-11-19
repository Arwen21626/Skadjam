<?php 
    session_start();
    $_SESSION['idCompte'] = 1;
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once(__DIR__ . "/../../php/fonctions.php");
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main>
        <?php try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT *
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    WHERE v.id_compte = $idCompte"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                } 
                
                if($tabProduit == null){ ?>
                    <p>Votre catalogue de produit est vide, vous n'avez donc pas de stock.</p>
                <?php } 
                
                else{?>
                    <table>
                        
                    </table>


                        
                <?php }
                $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>