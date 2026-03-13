<?php
    session_start();
    require_once(__DIR__ . '/../../php/verif_role_bo.php');
    require_once(__DIR__ . '/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../../connections_params.php");
    require_once __DIR__ . "/../../php/fonctions.php";
    require_once __DIR__ . "/../../php/modification_variable.php";
    $idCompte = $_SESSION['idCompte'];

    try{
        //récupère toutes les informations sur les produits
        $tabProduit = null;
        $sql="SELECT pr.libelle_produit, pr.id_produit, url_photo, alt, titre, prix_ttc, quantite_stock, 
                    note_moyenne, prix_remise, pourcentage_remise
                FROM sae3_skadjam._produit pr
                INNER join sae3_skadjam._montre m
                    ON pr.id_produit=m.id_produit
                INNER JOIN sae3_skadjam._photo ph  
                    ON ph.id_photo = m.id_photo
                INNER JOIN sae3_skadjam._vendeur v
                    ON pr.id_vendeur = v.id_compte
                LEFT JOIN sae3_skadjam._reduit rd
                    ON rd.id_produit = pr.id_produit
                LEFT JOIN sae3_skadjam._remise r
                    ON r.id_remise = rd.id_remise
                WHERE v.id_compte = :idCompte
                    AND pr.est_supprime = false";

        $stmt = $dbh->prepare($sql);
        $stmt->bindValue(':idCompte', $idCompte, PDO::PARAM_INT);
        $stmt->execute();
        $tabProduit = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }
    
?>
<!DOCTYPE html>
<html lang="fr">
<?php require_once __DIR__ . "/../../php/structure/head_back.php" ?>
<head>
    <title>Créer un catalogue</title>
</head>
<body>
    <?php 
        require_once __DIR__ . "/../../php/structure/header_back.php";
        require_once __DIR__ . "/../../php/structure/navbar_back.php";
    ?>
    <main class="p-8">
        <!--Début du catalogue-->
        <h2 id="vosProduits">Vos produits</h2>

        <?php if($tabProduit == null){ ?>
            <p>Votre catalogue est vide.</p>
        <?php }
            $lignes = $tabProduit; 

            //affiche la photo du produit, son nom, son prix et sa note, son stock ?>
            <form id="formCatalogue" class="flex justify-center gap-10" method="post" action="./confirmer_catalogue.php">

                <div class="flex flex-row flex-wrap">
                    <?php foreach($lignes as $id => $valeurs){
                        $idProduit = $valeurs['id_produit'];
                        // Le produit est-il en promotion ?
                        $stmt = $dbh->prepare("SELECT *
                                            FROM sae3_skadjam._promu
                                            WHERE id_produit = :id_produit");
                        $stmt->execute([':id_produit' => $idProduit]);
                        $estPromu = (!empty($stmt->fetch())); ?>
                        <section class="bg-bleu flex flex-col w-40 h-auto p-2 m-2 md:w-80 md:p-3">
                            <!--affichage de la photo-->
                            <div class=" mb-3">
                                <img class="w-auto h-40 md:h-80 mx-auto block" 
                                    src="<?php echo $valeurs['url_photo'];?>" 
                                    alt="<?php echo $valeurs['alt'];?>"
                                    title="<?php echo $valeurs['titre'];?>">
                                    
                            <!--affichage du nom du produit-->
                            <p class=" max-w-70"><?php echo $valeurs['libelle_produit'];?></p> 

                            <!--affichage du prix du produit-->   
                            <div class="flex flex-row justify-between items-center">
                                <p class="<?php echo ($valeurs['pourcentage_remise'] !== NULL)?'line-through':'';?>"> <?php echo htmlentities(str_replace(".", ",",$valeurs['prix_ttc'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>
                                <p class="<?php echo ($valeurs['pourcentage_remise'] !== NULL)?'':'hidden';?>"> <?php echo htmlentities(str_replace(".", ",",$valeurs['prix_remise'])); ?>€ (<abbr title="Toutes Taxes Comprises">TTC</abbr>)</p>

                            </div>
                            <!--récupération de la note-->
                            <div class="flex">
                                <?php $note = $valeurs['note_moyenne'];
                                    affichageNote($note); ?>
                            </div>
                            
                            <!--affichage du stock-->
                            <p>En stock : <?php echo $valeurs['quantite_stock'];?></p>
                            </div>  

                            <!-- Ajouter au catalogue -->
                            <div class="flex justify-end">
                                <!-- appearance-none size-10 bg-no-repeat bg-size-[auto_40px] bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus.svg)] checked:bg-[url(/images/logo/bootstrap_icon/bookmark-fa-plus-fill.svg)] -->
                                <!-- <input type="hidden" name="idProduit" value="<?php //echo $idProduit; ?>"> -->
                                <input type="checkbox" name="<?php echo $idProduit; ?>" id="<?php echo $idProduit; ?>" class="cursor-pointer size-7" alt="Ajouter au catalogue" title="Ajouter au catalogue">
                            </div>
                            <!--affichage de la promotion-->
                            <?php if($estPromu){ 
                                    $stmt = $dbh->prepare("SELECT *
                                                            FROM sae3_skadjam._promu pu
                                                            INNER JOIN sae3_skadjam._promotion pn
                                                                ON pu.id_promotion = pn.id_promotion
                                                            WHERE pu.id_produit = :id_produit");
                                    $stmt->execute([':id_produit' => $idProduit]);
                                    $promotion = $stmt->fetch(PDO::FETCH_ASSOC);
                                    $debutPromo = formatDate($promotion['date_debut_promotion']);
                                    $finPromo = null;
                                    if($promotion['date_fin_promotion'] !== null){
                                        $finPromo = formatDate($promotion['date_fin_promotion']);
                                    }
                                    $labelPromo = $promotion['label'];
                                    if($debutPromo <= date('Y-m-d') && ($finPromo === null || $finPromo >= date('Y-m-d')) && !empty($labelPromo)){?>
                                        <!-- Affichage de la bannière -->
                                        <div class="bg-rouge absolute w-36 md:w-74 underline text-beige pt-2 pb-1.5">
                                            <h4 class="text-center text-beige overline m-0"><?php echo htmlspecialchars($labelPromo); ?></h4>
                                        </div>
                                    <?php }
                            } ?> 
                        </section>
                    <?php } ?>
                </div> 
                
                <div class="flex flex-col sticky right-[6em] top-32 gap-4 h-fit">
                    <a href="./index_vendeur.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-60 h-14 cursor-pointer">Annuler</a>
                    <button type="button" id="btnSelectAll" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-60 h-14 cursor-pointer">Tout sélectionner</button>
                    <input type="submit" value="Confirmer" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-60 h-14 cursor-pointer">
                    <p id="erreurProduit" class="text-rouge hidden">
                        Veuillez sélectionner au moins un produit.
                    </p>
                </div>
            </form>         
            <?php $dbh = null;?>
    </main>
    <?php
        require_once __DIR__ . "/../../php/structure/footer_back.php";
    ?>
    <script>
        document.getElementById("formCatalogue").addEventListener("submit", function(e) {

            const checked = document.querySelectorAll('input[type="checkbox"]:checked').length;

            if (checked === 0) {
                e.preventDefault();
                document.getElementById("erreurProduit").classList.remove("hidden");
            }

        });

        const boutonSelectAll = document.getElementById("btnSelectAll");
        let toutSelectionne = false;

        boutonSelectAll.addEventListener("click", function(){

            const checkboxes = document.querySelectorAll('input[type="checkbox"]');

            toutSelectionne = !toutSelectionne;

            checkboxes.forEach(function(box){
                box.checked = toutSelectionne;
            });

            boutonSelectAll.textContent = toutSelectionne ? "Tout Désélectionner" : "Tout sélectionner";
        });
    </script>
</body>
</html>