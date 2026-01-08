<?php 
    session_start();
    include __DIR__ . '/../../php/verif_role_bo.php';
    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once __DIR__ . "/../../php/fonctions.php";
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__."/../../php/structure/head_back.php";?>
<head> 
    <title>Produits promus</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>



<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class=" p-8">
        <!--Début du catalogue-->
        <h2 id="vosProduits">Vos produits promus</h2>

        <?php

            $tabProduit = [];

            try {                
                //récupère toutes les infos des tables produits et photos
                foreach($dbh->query("SELECT *
                                    FROM sae3_skadjam._produit pr
                                    INNER join sae3_skadjam._montre m
                                        ON pr.id_produit=m.id_produit
                                    INNER JOIN sae3_skadjam._photo ph  
                                        ON ph.id_photo = m.id_photo 
                                    INNER JOIN sae3_skadjam._vendeur v
                                        ON pr.id_vendeur = v.id_compte
                                    INNER JOIN sae3_skadjam._promu pm
                                        ON pr.id_produit = pm.id_produit
                                    WHERE v.id_compte = $idCompte
                                        AND pr.est_supprime = false"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                if($tabProduit == null){ ?>
                    <p>Votre catalogue est vide.</p>
                <?php }

                //affiche la photo du produit, son nom, son prix et sa note, son stock ?>
                <div class="grid grid-cols-3">
                    <?php foreach($tabProduit as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];?>
                        <section class="bg-bleu grid grid-cols-[40%_60%] w-80 p-3 m-2">
                            <!--affichage de la photo-->
                            <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>" class="col-span-2 justify-self-center mb-3">
                                <img src="<?php echo $valeurs['url_photo'];?>" 
                                        alt="<?php echo $valeurs['alt'];?>"
                                        title="<?php echo $valeurs['titre'];?>">
                            </a>

                            <!--affichage du nom du produit-->
                            <p class="col-span-2"><?php echo $valeurs['libelle_produit'];?></p> 

                            <!--affichage du prix du produit-->   
                            <div class="flex justify-start items-center col-span-2">
                                <?php $prix = str_replace(".", ",", $valeurs['prix_ttc'])?>
                                <p><?php echo $prix;?> €</p>

                                <!--récupération de la note-->
                                <div class="ml-2 md:ml-10 flex">
                                    <?php $note = $valeurs['note_moyenne'];
                                        affichageNote($note); ?>
                                </div> 
                            </div>   
                             
                            <!--affichage du stock-->
                            <p class="col-span-2">En stock : <?php echo $valeurs['quantite_stock'];?></p>       
                        </section>
                    <?php } ?>
                </div>         
                <?php $dbh = null;
            }catch(PDOException $e){
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
    </main>
    
    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>

</html>
