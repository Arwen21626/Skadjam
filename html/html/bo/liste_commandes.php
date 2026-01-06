<?php 
    session_start();
    include(__DIR__ . '/../../php/verif_role_bo.php');

    include __DIR__ .'/../../01_premiere_connexion.php';
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits et photos
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr 
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        } 
        
        $qteStock = $row['quantite_stock'];

    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . "/../../php/structure/head_back.php"; ?>
<head>
    <title>Commandes</title>
</head>
<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main class="min-h-[600px]">
        <h2>Bientôt disponible ...</h2>
        <div class="flex justify-center mt-15 mb-15">
            <a href="../bo/index_vendeur.php"><button class="cursor-pointer border-2 border-vertFonce rounded-2xl w-50 h-14 px-7">Retour</button></a>
        </div>
    </main>

    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>