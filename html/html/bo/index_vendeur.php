<?php 
    session_start();

    include __DIR__ .'/../../01_premiere_connexion.php';
    require_once(__DIR__ . "/../../php/fonctions.php");
    const PAGE_SIZE = 15;
    $idCompte = $_SESSION['idCompte'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <link rel="stylesheet" type="text/css" href="../../css/bo/general_back.css" >
    <title>Accueil</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>



<body>
    <!--header-->
    <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
    <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

    <main>
        <div class="grid grid-cols-2 gap-4 justify-items-center">
            <a href="#vosProduits" title="lien vers page promotion">
                <img src="../../images/images_accueil/promotion.webp" alt="promotion" class="w-150 h-auto justify-self-end">
            </a>
            <a href="#vosProduits" title="lien vers page derniers ajouts">
                <img src="../../images/images_accueil/derniers_ajouts.webp" alt="derniers ajouts" class="w-150 h-auto justify-self-start">
            </a>           
            <a href="#vosProduits" title="lien vers page stock">
                <img src="../../images/images_accueil/stock.webp" alt="stock" class="w-150 h-auto justify-self-end">
            </a>
            <a href="" title="lien vers page commandes">
                <img src="../../images/images_accueil/commandes.webp" alt="commandes" class="w-150 h-auto justify-self-start">
            </a>        
        </div>


        <div class="mt-15 flex flex-row justify-around">
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7"><a href="../bo/creation_produit.php">Ajouter un produit</a></button>
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7"><a href="../bo/vider_catalogue.php">Vider le catalogue</a></button>
        </div>

        


        <!--Début du catalogue-->
        <h2 id="vosProduits">Vos produits</h2>

        <?php
            //initialisation du numéro de page
            if(isset($_GET['page'])&& $_GET['page']!==""){
                $pageNumber = $_GET['page'];
            }
            else{
                $pageNumber = 1;
            }

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
                                    WHERE v.id_compte = $idCompte"
                                    , PDO::FETCH_ASSOC) as $row){
                    $tabProduit[] = $row;
                }

                if($tabProduit == null){ ?>
                    <p>Votre catalogue est vide.</p>
                <?php }

                $maxPage = sizeof($tabProduit)/PAGE_SIZE;
                //découpe le catalogue en page de 15 produits
                $lignes = array_slice($tabProduit, $pageNumber*PAGE_SIZE-PAGE_SIZE, PAGE_SIZE); 

                //affiche la photo du produit, son nom, son prix et sa note, son stock ?>
                <div class="grid grid-cols-3">
                    <?php foreach($tabProduit as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];?>
                        <section class="bg-bleu grid grid-cols-[40%_60%] w-80 p-3 m-2">
                            <!--affichage de la photo-->
                            <a href= "<?php echo "details_produit.php?idProduit=".$idProduit;?>" class="col-span-2 justify-self-center mb-3">
                                <img src="<?php echo htmlentities($valeurs['url_photo']);?>" 
                                        alt="<?php echo htmlentities($valeurs['alt']);?>"
                                        title="<?php echo htmlentities($valeurs['titre']);?>">
                            </a>

                            <!--affichage du nom du produit-->
                            <p class="col-span-2"><?php echo htmlentities($valeurs['libelle_produit']);?></p> 

                            <!--affichage du prix du produit-->   
                            <p class="col-span-1 col-start-1"><?php echo htmlentities($valeurs['prix_ttc']);?> €</p>

                            <!--récupération de la note-->
                            <div class=" flex col-span-1 col-start-2">
                                <?php $note = $valeurs['note_moyenne'];
                                    affichageNote($note); ?>
                            </div>    
                             
                            <!--affichage du stock-->
                            <p class="col-span-2">En stock : <?php echo htmlentities($valeurs['quantite_stock']);?></p>       
                        </section>
                    <?php } ?>
                </div>         
                <?php $dbh = null;
            } 

            catch (PDOException $e) {
                print "Erreur !: " . $e->getMessage() . "<br/>";
                die();
            }
        ?>
        <!--fin du catalogue-->

        <?php if ($pageNumber>1){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber-1)."#vosProduits";?>">Page précédente</a>
        <?php }?>
    
        <?php if ($pageNumber<$maxPage){?>
        <a class= "lienPage" href="<?php echo "./index_vendeur.php?page=".($pageNumber+1)."#vosProduits";?>">Page suivante</a>
        <?php }?>

    </main>
    
    <!--footer-->
    <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>

</body>

</html>
