<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    include __DIR__ . '/../../connexion_recupraptor.php';
    $idCompte = $_SESSION['idCompte'];

    if($idCompte == null){
        header('Location: ./connexion.php');
    }

    try {     

        $tabInfoCommandes = null;           
        //récupère toutes les infos de la table commande
        foreach($dbh->query("SELECT c.id_commande, c.date_commande, c.etat, c.montant_total_ttc
                            FROM sae3_skadjam._commande c
                            WHERE c.id_client = $idCompte
                            ORDER BY c.date_commande DESC, c.id_commande DESC;"
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
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px]">
        <h2 class = "md:pt-15 pt-10">Liste de mes commandes</h2>

        <?php if($tabInfoCommandes == null){ ?>
            <p class="pt-15 text-center">Votre n'avez pas encore effectué de commande.</p>
        <?php }

        else{?>
            <div class="flex justify-center md:mt-15 mt-10">
                <?php //tableau des commandes ?>
                <table class="table-auto md:w-250 w-95">
                    <thead>
                        <tr>
                            <th scope="col" class="md:w-80 pl-3"><h3>N°</h3></th>
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
                                    <th scope="row" class="text-left pl-5 md:text-center py-3 md:pl-3" ><p><?php echo $idCommande; ?></p></th>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['date_commande']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['etat']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo htmlentities($commande['montant_total_ttc']); ?></p></td>
                                    <td><a href="<?php echo htmlentities("commande.php?idCommande=".$idCommande);?>">
                                        <img src="../../images/logo/bootstrap_icon/plus-square.svg" alt="voir plus d'informations" class="w-8 md:w-10 h-auto">
                                    </a></td>
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
            <a href="../../index.php" class="flex justify-center mt-15 mb-15"><button class="border-vertClair border-4 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>