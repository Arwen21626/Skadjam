<?php
session_start();
include __DIR__ . '/../../php/verif_role_bo.php';
include __DIR__ . '/../../01_premiere_connexion.php';
require_once __DIR__ . '/../../php/verification_formulaire.php';
require_once __DIR__ . '/../../php/modification_variable.php';



//Récupération id vendeur
$idVendeur = $_SESSION['idCompte'];

//Tableau pour les catégories de la base
$tab_categories = [];

//Tableau pour les tva
$tab_tva = [];

//Tableau pour les unites
$tab_unite = ["Piece", "Litre","cl","g","kg","S","M","L","XL","XXL","m","cm"];

//Initialisation des variables d'erreurs
$erreurIdCategorie = false;
$erreurNom = false;
$erreurPrixHT = false;
$erreurQteStock = false;
$erreurDescription = false;
$erreurUnite = false;
$erreurQteUnite = false;

//Requete récupération categories
foreach($dbh->query('SELECT * from sae3_skadjam._categorie', PDO::FETCH_ASSOC) as $row) {
    $tab_categories[] = $row;
}

//Requete récupération TVA
foreach($dbh->query('SELECT * from sae3_skadjam._tva', PDO::FETCH_ASSOC) as $row) {
    $tab_tva[] = $row;
}

if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description']) && isset($_POST['unite'])) {
    //Récupération des champs pour l'insertion
    $idCategorie = $_POST['categorie'];
    $nom = $_POST['nom'];
    $prixHT = str_replace(",", ".", $_POST['prix']);
    $qteStock = $_POST['qteStock'];
    $description = $_POST['description'];
    $unite = $_POST['unite'];
    $qteUnite = $_POST['qteUnite'];
    // Champs spécifiques à la promotion
    $dateDebutPromotion = isset($_POST['dateDebutPromotion']) ? htmlentities($_POST['dateDebutPromotion']) : date('Y-m-d');
    $dateFinPromotion = htmlentities($_POST['dateFinPromotion']);
    $dateFinPromotion = trim($dateFinPromotion);
    $dateFinPromotion = ($dateFinPromotion === '') ? null : $dateFinPromotion;
    $labelPromo = isset($_POST['labelPromo']) ? $_POST['labelPromo'] : null;

    if(isset($_POST['mettreEnLigne'])){
        $enLigne = $_POST['mettreEnLigne'];
    }
    

    //Récupération du nom de la catégorie pour la gestion de la tva
    foreach ($tab_categories as $c) {
        if ($c['id_categorie'] == $idCategorie) {
            $nomCategorie = $c['libelle_categorie'];
        }
    }
    
    if(isset($_POST['mettreEnLigne'])){
        if($_POST['mettreEnLigne'] == 'on') {
            //S'il n'est pas coché il faut mettre est_masque dans la BDD à true en chaine pour eviter les problèmes
            $enLigne = 'false';
        }else{
            $enLigne = 'true';
        }
    }
    else{
        $enLigne = 'true';
    }

    //Gestion de la photo
    $typePhoto = $_FILES['photo']['type'];
    $ext = explode('/',$typePhoto)[1];
    $nom_serv_photo = $_FILES['photo']['tmp_name'];

    //Déplacement et renommage du fichier photo
    $nom_explode = explode(' ',$nom)[0];
    $currentTime = time();
    $destination = __DIR__ . '/../../images/photo_importees';
    $nom_photo_finale = $nom_explode.$currentTime.'.'.$ext;
    move_uploaded_file($nom_serv_photo,$destination.'/'.$nom_photo_finale);

    if($idCategorie == 0){
        $erreurIdCategorie = true;
    }

    if($unite == 0){
        $erreurUnite = true;
    }
    
    if (verifPrix($prixHT) && verifQteStock($qteStock) && $idCategorie != 0 && $unite != 0){
        try{
            if ($nomCategorie == 'Alimentaire') {
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'reduit') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }
            else{
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'normal') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }

            
            //Calcul prixTTC
            $prixTTC = $prixHT*(1+$pourcentageTVA);

            //Insertion du produit
            $insertionProduit = $dbh -> prepare("WITH id AS (
                INSERT INTO sae3_skadjam._produit 
                (libelle_produit, description_produit, prix_ht, prix_ttc, est_masque, quantite_stock, quantite_unite, unite, id_categorie, id_vendeur, id_tva)
                VALUES 
                (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                RETURNING id_produit)
                SELECT * FROM id;
                ");
            $insertionProduit->execute([$nom,$description,$prixHT,$prixTTC,$enLigne,$qteStock,$qteUnite,$unite,$idCategorie,$idVendeur,$tva]);
            
            foreach ($insertionProduit as $t) {
                $idProd = $t['id_produit'];
            }

            //Insertion de la photo dans la table photo
            $insertionPhoto = $dbh -> prepare("WITH id AS (
                INSERT INTO sae3_skadjam._photo 
                (url_photo, alt, titre)
                VALUES 
                (?, ?, ?)
                RETURNING id_photo)
                SELECT * FROM id;
                ");

            $insertionPhoto->execute(['/images/photo_importees/'.$nom_photo_finale,$nom,$nom]);

            foreach ($insertionPhoto as $t) {
                $idPhoto = $t['id_photo'];
            }

            // Gestion de la promotion
            $caseCochee = isset($_POST['mettreEnPromotion']);

            // La case "Mettre en promotion" est cochée
            if ($caseCochee) {
                $dbh->beginTransaction();
                // Création de la promotion
                try {
                    if(verifDate($dateDebutPromotion) && $dateDebutPromotion >= date('Y-m-d')){
                        // Une date de fin à été ajoutée
                        if(verifDate($dateFinPromotion) && $dateFinPromotion >= $dateDebutPromotion){
                            $stmtPromo = $dbh->prepare("INSERT INTO sae3_skadjam._promotion
                                                        (
                                                            date_debut_promotion,
                                                            date_fin_promotion,
                                                            heure_debut,
                                                            id_vendeur,
                                                            id_photo
                                                        ) VALUES (
                                                            :date_debut,
                                                            :date_fin,
                                                            '00:00',
                                                            :id_vendeur,
                                                            :id_photo
                                                        )");
                            $stmtPromo->execute([
                                ':date_debut' => formatDate($dateDebutPromotion),
                                ':date_fin'   => formatDate($dateFinPromotion),
                                ':id_vendeur' => $idVendeur,
                                ':id_photo'   => $idPhoto
                            ]);
                        // Si aucune date de fin n'a été ajoutée
                        }else if($dateFinPromotion === null){
                            $stmtPromo = $dbh->prepare("INSERT INTO sae3_skadjam._promotion
                                                        (
                                                            date_debut_promotion,
                                                            date_fin_promotion,
                                                            heure_debut,
                                                            id_vendeur,
                                                            id_photo
                                                        ) VALUES (
                                                            :date_debut,
                                                            :date_fin,
                                                            '00:00',
                                                            :id_vendeur,
                                                            :id_photo
                                                        )");
                            $stmtPromo->execute([
                                ':date_debut' => formatDate($dateDebutPromotion),
                                ':date_fin'   => null,
                                ':id_vendeur' => $idVendeur,
                                ':id_photo'   => $idPhoto
                            ]);
                        }else{
                            echo "La date de fin de promotion est invalide.";
                        }
                    }else{
                        echo "La date de début de promotion est invalide.";
                    }
                    
                    $idPromotion = $dbh->lastInsertId();

                    if(strlen($labelPromo) < 20){
                        $stmtLibelle = $dbh->prepare("UPDATE sae3_skadjam._promotion
                                                    SET label = :label
                                                    WHERE id_promotion = :id_promotion");
                        $stmtLibelle->execute([
                            ':label' => $labelPromo,
                            ':id_promotion' => $idPromotion
                        ]);
                    }else{
                        echo "Le libellé de la promotion est invalide.";
                    }

                    $stmtPromu = $dbh->prepare("INSERT INTO sae3_skadjam._promu
                                                (
                                                    id_promotion,
                                                    id_produit
                                                ) VALUES (
                                                    :id_promotion,
                                                    :id_produit
                                                )");
                    $stmtPromu->execute([
                        ':id_promotion' => $idPromotion,
                        ':id_produit'   => $idProd
                    ]);
                    $dbh->commit();
                } catch (Exception $e) {
                    $dbh->rollBack();
                    throw $e;
                }
            }

            $insertionMontre = $dbh -> prepare("INSERT INTO sae3_skadjam._montre VALUES (?,?);");
            $insertionMontre->execute([$idPhoto,$idProd]);

            header("Location: ./details_produit.php?idProduit=".$idProd);

        }
        catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }
    // else{
    //     echo ("Le prix ou la quantité saisi est incorrect ou la catégorie n'a pas été entré.");
    // }
}
?>

<!DOCTYPE html>
<html lang="fr">
    <?php include(__DIR__."/../../php/structure/head_back.php");?>
    <head>
        <title>Création d'un produit</title>
        <style>
            button a:hover{
                color : black;
            }
        </style>
    </head>
    <body>
        <?php include(__DIR__ . '/../../php/structure/header_back.php');?>
        <?php include(__DIR__ . '/../../php/structure/navbar_back.php');?>
        <main class="flex flex-col items-center">
            <h2>Création d'un produit</h2>
            <form class="grid grid-cols-[40%_60%] w-4/5 self-center" action="creation_produit.php" method="post" enctype="multipart/form-data">

                <!-- Image -->
                <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                    <input type="file" id="photo" name="photo" class="hidden" required>
                    <!-- label qui agit comme bouton -->
                    <label for="photo" class="bg-beige w-60 h-60 rounded-2xl image-produit cursor-pointer" style="background-image: url('../../images/logo/bootstrap_icon/image.svg'); background-repeat: no-repeat; background-position: center; background-size: 60%;"></label>
                    <label class="cursor-pointer" for="photo">Ajouter une image*</label>
                </div>

                <!-- Nom produit -->
                <div class="col-start-2 row-start-1 flex flex-col w-200 m-2 p-2">
                    <label for="nom">Nom produit *:</label>
                    <input placeholder="Confiture fraises des bois 200g" class=" border-4 border-beige rounded-2xl placeholder-gray-500" type="text" name="nom" id="nom" required>
                </div>

                <div class="col-start-2 row-start-2 flex flex-row justify-between w-200 m-2 p-2">
                    <!-- Prix ht -->
                    <div class="flex flex-col">
                        <label for="prix">Prix *(hors taxe):</label>
                        <input placeholder="3.99" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="prix" id="prix" min="0.0" step="0.01" required>
                    </div>
                    <!-- Quantite en stock -->
                    <div class="flex flex-col">
                        <label for="qteStock">Quantité en stock* :</label>
                        <input placeholder="50" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="qteStock" id="qteStock" min="0" required>
                    </div>
                </div>
                    
                <div class="col-start-2 row-start-3 col-span-2 flex flex-row justify-between w-200 m-2 p-2">
                    <!-- Catégorie -->
                    <div class="flex flex-col">
                        <label for="categorie">Catégorie* :</label>
                        <select class=" border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="categorie" id="categorie" required>
                            <option value="0">Choisir</option>
                            <?php foreach ($tab_categories as $categorie) {?>
                                <option value="<?php echo htmlentities($categorie['id_categorie'])?>"><?php echo htmlentities($categorie['libelle_categorie'])?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- Unité -->
                    <div class="flex flex-col">
                        <label for="unite">Unité* :</label>
                        <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="unite" id="unite" required>
                        <option value="0">Choisir</option>
                        <?php foreach ($tab_unite as $unite) {?>
                            <option value="<?php echo htmlentities($unite)?>"><?php echo htmlentities($unite)?></option>
                        <?php } ?>
                    </select>
                    </div>
                    <!-- Quantité par unité -->
                    <div class="flex flex-col">
                        <label for="qteUnite">Quantité par unité :</label>
                        <input placeholder="200" class="border-4 border-beige rounded-2xl w-75 placeholder-gray-500" type="number" name="qteUnite" id="qteUnite" min="0" required>
                    </div>
                </div>

                
                <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                    <!-- Mettre en ligne -->
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                        <input class="appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige cursor-pointer" type="checkbox" name="mettreEnLigne" id="mettreEnLigne">
                    </div>
                
                    <!-- Mettre en promotion -->
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnPromotion">Mettre en promotion<?php if ($nbPromos >= 2 && !$caseCochee) { echo " (Limite atteinte)"; } ?></label>
                        <input id="promoCheck" type="checkbox" name="mettreEnPromotion" class="<?php echo ($nbPromos >= 2 && !$caseCochee) ? 'cursor-not-allowed' : 'cursor-pointer'; ?> appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige checked:border-vertFonce" <?php echo ($nbPromos >= 2 && !$caseCochee) ? 'disabled' : ''; ?>>
                    </div>
                </div>
                <!-- Inputs liés aux promotions -->
                <div id="promoInputs" class="col-start-1 row-start-5 col-span-2 flex flex-col">
                    <div class="flex flex-row justify-around m-2 p-2">
                        <div>
                            <div class="flex flex-row mr-4 ml-4">
                                <label class="mr-4" for="dateDebutPromotion">Début de promotion* :</label>
                                <input class="border-4 border-beige rounded-2xl w-45" type="date" name="dateDebutPromotion" id="dateDebutPromotion" value="<?php echo $dateDebutPromotion !== null ? $dateDebutPromotion : date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div>
                            <div class="flex flex-row mr-4 ml-4">
                                <label class="mr-4" for="dateFinPromotion">Fin de promotion :</label>
                                <input class="border-4 border-beige rounded-2xl w-45" type="date" name="dateFinPromotion" id="dateFinPromotion" value="<?php echo $dateFinPromotion; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row justify-around m-2 p-2">
                        <div class="flex flex-row mr-4 ml-4">
                            <label class="mr-4" for="labelPromo">Libellé de la promotion :</label>
                            <input class="border-4 border-beige rounded-2xl w-45" type="text" name="labelPromo" id="labelPromo" value="<?php echo $labelPromo;?>">
                        </div>
                    </div>
                </div>
                
                <!-- Description -->
                <div class="col-start-1 col-span-2 row-start-6 flex flex-col m-2 p-2 ">
                    <label for="description">Description *:</label>
                    <textarea placeholder="Pot de confiture de fraises des bois" class="border-4 border-beige rounded-2xl w-3/4 self-center placeholder-gray-500" name="description" id="description" cols="100" rows="10" required></textarea>
                </div>
                
                <!-- Validation -->
                <div class="col-start-1 col-span-2 row-start-7 flex flex-row justify-around m-4">
                    <button class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer"><a href="../bo/index_vendeur.php">Retour</a></button>                    
                    <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer" type="submit" value="Valider">
                </div>
            </form>
        </main>
        <?php include(__DIR__ . '/../../php/structure/footer_back.php');?>
        <script src="../../js/bo/changement_image_produits.js"></script>
    </body>
    <script>
        // Fonction pour afficher/cacher les inputs de promotion
        function togglePromotionInputs(){
            var promoCheck = document.getElementById('promoCheck');
            var promoInputs = document.getElementById('promoInputs');
            if(promoCheck.checked && !promoCheck.disabled){
                promoInputs.style.display='flex';
                promoInputs.style.visibility = 'visible';
                promoInputs.style.height = 'auto';
            }else{
                promoInputs.style.visibility = 'hidden';
                promoInputs.style.height = '0';
            }
        }
        // Quand "Mettre en promotion" est coché, afficher les inputs de promotion
        document.addEventListener('DOMContentLoaded', function() {
            var promoCheck = document.getElementById('promoCheck');
            promoCheck.addEventListener('change', togglePromotionInputs);
            togglePromotionInputs();
        });
    </script>
</html>