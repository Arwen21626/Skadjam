<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . '/../../php/verification_formulaire.php';
    $idCompte = $_SESSION['idCompte'];

    try {     
        $tabProduit = null;           
        //récupère toutes les infos des tables produits
        foreach($dbh->query("SELECT *
                            FROM sae3_skadjam._produit pr
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._montre m
                                ON m.id_produit = pr.id_produit
                            INNER JOIN sae3_skadjam._photo p
                                ON p.id_photo = m.id_photo
                            INNER JOIN sae3_skadjam._promu pmu
                                ON pr.id_produit = pmu.id_produit
                            INNER JOIN sae3_skadjam._promotion pmn
                                ON pmu.id_promotion = pmn.id_promotion
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        } 

    }catch(PDOException $e){
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . "/../../php/structure/head_back.php";?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produits promus</title>
</head>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[545px]">
        <h2>Vos produits promus</h2>

        <?php if($tabProduit == null){ ?>
            <p class="text-center">Vous n'avez pas de produits en promotion.</p>
            <!---bouton retour--->
            <a href="index_vendeur.php" class="flex justify-center mt-15 mb-15">
                <button class="border-vertFonce border-2 rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button>
            </a>
        <?php }else{?>
            <div class="flex justify-center flex-row-reverse mb-14">

                <div class="flex justify-around flex-col sticky top-1/4 h-40 ml-10">
                    <!---bouton retour--->
                    <a href="index_vendeur.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer">Retour</a>
                    <!---bouton modifier promotion--->
                    <button class="border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer mt-5">
                        <a href="../bo/modifier_promotion.php?idCompte=<?php echo $idCompte ;?>" class="">Modifier</a>
                    </button>
                </div>

                <table class="table-auto w-2/3">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col" class="text-left w-100 pl-3"><h3>Nom du produit</h3></th>
                            <th scope="col"><h3>Début</h3></th>
                            <th scope="col"><h3>Fin</h3></th>
                            <th scope="col"><h3>libellé</h3></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            //pour changer la classe de css une ligne sur 2
                            $impair = 0;
                            $classe;
                            $classe1 = "py-4";
                            $classe2 = "py-4 bg-bleu";
                            foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit']; 
                                $impair ++;
                                if(fmod($impair, 2) == 0){
                                    $classe = $classe1;
                                }
                                else{
                                    $classe = $classe2;
                                }?>
                                <tr class="<?php echo $classe; ?>">
                                    <td class="py-3 w-24 text-center">
                                        <img class="w-16 h-16 object-contain inline-block" 
                                            src="<?php echo $valeurs['url_photo'];?>" 
                                            alt="<?php echo $valeurs['alt'];?>" 
                                            title="<?php echo $valeurs['titre'];?>">
                                    </td>
                                    <th scope="row" class="text-left py-3 pl-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></th>
                                    <td class="text-center py-3"><p><?php echo htmlentities($valeurs['date_debut_promotion']);?></p></td>
                                    <td class="text-center py-3"><p><?php echo isset($valeurs['date_fin_promotion']) ? htmlentities($valeurs['date_fin_promotion']) : "non défini";?></p></td>
                                    <td class="text-center py-3"><p><?php echo !empty($valeurs['label']) ? htmlentities($valeurs['label']) : "non défini"; ?></p></td>
                                    
                                </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_back.php"; ?>
</body>
</html>