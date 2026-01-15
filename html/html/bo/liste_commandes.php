<?php
    session_start();
    
    echo $_SESSION['role'];
    echo'ici';
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabInfoCommandes = null;           
        //récupère toutes les infos de la table commande
        foreach($dbh->query("SELECT DISTINCT
                                c.id_commande,
                                c.date_commande,
                                c.etat,
                                c.montant_total_ttc
                            FROM sae3_skadjam._commande c
                            INNER JOIN sae3_skadjam._details d
                                ON d.id_commande = c.id_commande
                            INNER JOIN sae3_skadjam._produit p
                                ON p.id_produit = d.id_produit
                            WHERE p.id_vendeur = $idCompte;"
                            , PDO::FETCH_ASSOC) as $row){
            $tabInfoCommandes[] = $row;
        } 
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste de mes commandes</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_back.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[600px]">
        <h2 class = "pt-15">Liste de mes commandes</h2>

        <?php if($tabInfoCommandes == null){ ?>
            <p class="pt-15 text-center">Personne n'a encore effectué de commande chez vous.</p>
        <?php }

        else{?>
            <div class="flex justify-center mt-15">
                <?php //tableau des commandes ?>
                <table class="table-auto w-250">
                    <thead>
                        <tr>
                            <th scope="col" class="w-80 pl-3"><h3>N° de commande</h3></th>
                            <th scope="col"><h3>Date</h3></th>
                            <th scope="col"><h3>Etat</h3></th>
                            <th scope="col"><h3>Total</h3></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            //pour changer la classe de css une ligne sur 2
                            $impair = 0;
                            foreach($tabInfoCommandes as $id => $commande){
                                $idCommande = $commande['id_commande']; 
                                $impair ++;
                                if(fmod($impair, 2) == 0){
                                    $classe = "py-4";
                                }
                                else{
                                    $classe = "py-4 bg-bleu";
                                }?>
                                <tr class="<?php echo $classe; ?>">
                                    <th scope="row" class="text-center py-3 pl-3" ><p><?php echo $idCommande; ?></p></th>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['date_commande']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['etat']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['montant_total_ttc']); ?></p></td>
                                    <td><a href="<?php echo htmlentities("commande.php?idCommande=".$idCommande);?>">
                                        <img src="../../images/logo/bootstrap_icon/plus-square.svg" alt="voir plus d'informations" class="w-10 h-auto">
                                    </a></td>
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <a href="../../index_vendeur.php" class="flex justify-center mt-15 mb-15"><button class="border-vertFonce border-2 rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_back.php"); ?>
</body>
</html>