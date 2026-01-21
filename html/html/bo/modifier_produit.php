<?php
session_start();
include __DIR__ . '/../../php/verif_role_bo.php';
include  __DIR__ . '/../../01_premiere_connexion.php';
require_once __DIR__ . '/../../php/verification_formulaire.php';
require_once __DIR__ . '/../../php/modification_variable.php';

$idProduit = $_GET['idProduit'];
$idCompte = $_SESSION['idCompte'];

//Tableau pour les catégories de la base
$tab_categories = [];

//Tableau pour les tva
$tab_tva = [];

//Tableau pour les unites
$tab_unite = ["Piece", "Litre","cl","g","kg","S","M","L","XL","XXL","m","cm"];

//Requete récupération categories
foreach($dbh->query('SELECT * from sae3_skadjam._categorie', PDO::FETCH_ASSOC) as $row) {
    $tab_categories[] = $row;
}

//Requete récupération TVA
foreach($dbh->query('SELECT * from sae3_skadjam._tva', PDO::FETCH_ASSOC) as $row) {
    $tab_tva[] = $row;
}

//Recuperation de toutes les informations du produit
foreach($dbh->query("SELECT *,est_masque::CHAR as est_masque_php 
                            FROM sae3_skadjam._produit pr
                            INNER JOIN sae3_skadjam._categorie c
                                ON pr.id_categorie = c.id_categorie
                            INNER JOIN sae3_skadjam._montre m
                                ON pr.id_produit = m.id_produit
                            INNER JOIN sae3_skadjam._photo ph
                                ON m.id_photo = ph.id_photo
                            LEFT JOIN sae3_skadjam._reduit rd
                                ON rd.id_produit = pr.id_produit
                            LEFT JOIN sae3_skadjam._remise r
                                ON r.id_remise = rd.id_remise
                            WHERE pr.id_produit = $idProduit") as $produit){

    //Récupération attribut de produit
    $nom = $produit['libelle_produit'];
    $description = $produit['description_produit'];
    $prixHT = $produit['prix_ht'];
    $remise = $produit['pourcentage_remise'];
    $enLigne = $produit['est_masque_php']; 
    $qteStock = $produit['quantite_stock'];
    $qteUnite = $produit['quantite_unite'];
    $unite = $produit['unite'];
    $nomCategorie = $produit['libelle_categorie'];
    $idCategorie = $produit['id_categorie'];

    // Si dans la BDD est_masque est a false, il faut mettre enLigne à true ou l'inverse
    if($enLigne == 'f'){
        $enLigne = 'true';
    }else{
        $enLigne = 'false';
    }

    $stmtPromo = $dbh->query("SELECT *
                                    FROM sae3_skadjam._promu pmu
                                    INNER JOIN sae3_skadjam._promotion pmn
                                        ON pmu.id_promotion = pmn.id_promotion
                                    WHERE pmu.id_produit = $idProduit");
    $promotion = $stmtPromo->fetch(PDO::FETCH_ASSOC);

    // id_promotion != null veut dire que le produit est en promotion
    if( isset($promotion['id_promotion']) && $promotion['id_promotion'] != null){
        $caseCochee = true;
        // Récupération des infos de promotion
        $dateDebutPromotion = formatDate($promotion['date_debut_promotion']);
        $dateFinPromotion = $promotion['date_fin_promotion'] !== null ? formatDate($promotion['date_fin_promotion']) : null;
        $labelPromo = $promotion['label'];
    }else{
        $caseCochee = false;
    }

    // Suppression des promotions expirées
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $dbh->prepare("DELETE FROM sae3_skadjam._promu
                        WHERE id_promotion IN (
                            SELECT id_promotion
                            FROM sae3_skadjam._promotion
                            WHERE date_fin_promotion < :current_date
                        )")->execute([':current_date' => date('Y-m-d')]);

        $dbh->prepare("DELETE FROM sae3_skadjam._promotion
                        WHERE date_fin_promotion < :current_date")->execute([':current_date' => date('Y-m-d')]);
    }

    //Récupération attribut de photo
    $idPhoto = $produit['id_photo'];
    $urlPhoto = $produit['url_photo'];
    $altPhoto = $produit['alt'];
    $titrePhoto = $produit['titre'];
}

//Traitement du formulaire
if (isset($_POST['categorie']) && isset($_POST['nom']) && isset($_POST['prix']) && isset($_POST['qteStock']) && isset($_POST['description']) && isset($_POST['unite'])) {
    
    //Gestion de la photo
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK){
        $typePhoto = $_FILES['photo']['type'];
        $ext = explode('/',$typePhoto)[1];
        $nom_serv_photo = $_FILES['photo']['tmp_name'];

        //Déplacement et renommage du fichier photo
        $nom_explode = explode(' ',$nom)[0];
        $currentTime = time();
        $destination = __DIR__ . '/../../images/photo_importees';
        $nom_photo_finale = $nom_explode.$currentTime.'.'.$ext;
        move_uploaded_file($nom_serv_photo,$destination.'/'.$nom_photo_finale);
    }else{
        //Récupération attribut de photo
        $nom_photo_finale = explode('/',$urlPhoto)[3];
    }
    
    // Récupération des champs pour l'insertion
    $idCategorie = htmlentities($_POST['categorie']);
    $nom = htmlentities($_POST['nom']);
    $prixHT = htmlentities($_POST['prix']);
    $qteStock = htmlentities($_POST['qteStock']);
    $remise = htmlentities($_POST['remise']);
    $enLigne = htmlentities($_POST['mettreEnLigne']);
    $description = htmlentities($_POST['description']);
    $unite = htmlentities($_POST['unite']);
    $qteUnite = htmlentities($_POST['qteUnite']);
    // Champs spécifiques à la promotion
    $dateDebutPromotion = isset($_POST['dateDebutPromotion']) ? htmlentities($_POST['dateDebutPromotion']) : date('Y-m-d');
    $dateFinPromotion = htmlentities($_POST['dateFinPromotion']);
    $dateFinPromotion = trim($dateFinPromotion);
    $dateFinPromotion = ($dateFinPromotion === '') ? null : $dateFinPromotion;
    $labelPromo = isset($_POST['labelPromo']) ? $_POST['labelPromo'] : null;

    // Récupération du nom de la catégorie pour la gestion de la tva
    foreach ($tab_categories as $c) {
        if ($c['id_categorie'] == $idCategorie) {
            $nomCategorie = $c['libelle_categorie'];
        }
    }
    
    // S'il n'est pas coché il faut mettre est_masque dans la BDD à true en chaine pour eviter les problèmes
    if($_POST['mettreEnLigne'] == false){
        $enLigne = 'true';
    }else{
        $enLigne = 'false';
    }


    // Vérification du prix et du stock
    if (verifPrix($prixHT) && verifQteStock($qteStock)){
        try{
            if ($nomCategorie == 'Alimentaire') {
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'reduit') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }else{
                foreach ($tab_tva as $t) {
                    if ($t['nom_tva'] === 'normal') {
                        $tva = $t['id_tva'];
                        $pourcentageTVA = $t['pourcentage_tva'];
                    }
                }
            }

            //Calcul prixTTC
            $prixTTC = $prixHT*(1+$pourcentageTVA);

            //Update du produit
            $updateProduit = $dbh -> query("UPDATE sae3_skadjam._produit SET
                                                        libelle_produit = '$nom',
                                                        description_produit = '$description',
                                                        prix_ht = $prixHT,
                                                        prix_ttc = $prixTTC,
                                                        est_masque = $enLigne,
                                                        quantite_stock = $qteStock,
                                                        quantite_unite = $qteUnite,
                                                        unite = '$unite',
                                                        id_categorie = $idCategorie,
                                                        id_vendeur = $idCompte,
                                                        id_tva = $tva
                                                    WHERE id_produit = $idProduit;");

            // Gestion de la promotion
            // Vérifier si le produit est promu ou non
            $stmt = $dbh->prepare("SELECT id_promotion 
                                    FROM sae3_skadjam._promu 
                                    WHERE id_produit = :id_produit");
            $stmt->execute([':id_produit' => $idProduit]);
            $promotion = $stmt->fetch(PDO::FETCH_ASSOC);

            $estPromu = !empty($promotion);
            $caseCochee = isset($_POST['mettreEnPromotion']);

            // La case "Mettre en promotion" est cochée et que le produit n'est pas déjà promu
            if ($caseCochee && !$estPromu) {
                $dbh->beginTransaction();
                // Création de la promotion
                try {
                    if(verifDate($dateDebutPromotion) && $dateDebutPromotion >= date('Y-m-d')){
                        // Une date de fin à été ajoutée
                        if(isset($dateFinPromotion) && $dateFinPromotion >= $dateDebutPromotion){
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
                                ':id_vendeur' => $idCompte,
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
                                ':id_vendeur' => $idCompte,
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
                        ':id_produit'   => $idProduit
                    ]);
                    $dbh->commit();
                } catch (Exception $e) {
                    $dbh->rollBack();
                    throw $e;
                }
            }

            // Si la case "Mettre en promotion" n'est pas cochée et que le produit est déjà promu
            if (!$caseCochee && $estPromu) {
                $dbh->beginTransaction();
                $idPromotion = $promotion['id_promotion'];
                // Suppression de la promotion existante
                try {
                    $dbh->prepare("DELETE FROM sae3_skadjam._promu
                                    WHERE id_produit = :id_produit")->execute([':id_produit' => $idProduit]);

                    $dbh->prepare("DELETE FROM sae3_skadjam._promotion
                                    WHERE id_promotion = :id_promotion")->execute([':id_promotion' => $promotion['id_promotion']]);
                    $dbh->commit();
                } catch (Exception $e) {
                    $dbh->rollBack();
                    throw $e;
                }
            }

            // Si la case "Mettre en promotion" est cochée et que le produit est déjà promu
            if($caseCochee && $estPromu){
                $idPromotion = $promotion['id_promotion'];
                // Mise à jour des dates de la promotion existante
                if(verifDate($dateDebutPromotion) && $dateDebutPromotion >= date('Y-m-d')){
                    if(isset($dateFinPromotion) && $dateFinPromotion !== ''){
                        if(verifDate($dateFinPromotion) && $dateFinPromotion >= $dateDebutPromotion){
                            $stmtUpdatePromo = $dbh->prepare("UPDATE sae3_skadjam._promotion
                                                            SET date_debut_promotion = :date_debut,
                                                                date_fin_promotion = :date_fin
                                                            WHERE id_promotion = :id_promotion");
                            $stmtUpdatePromo->execute([
                                ':date_debut'   => formatDate($dateDebutPromotion),
                                ':date_fin'     => formatDate($dateFinPromotion),
                                ':id_promotion' => $idPromotion
                            ]);
                        }
                    // Suppression de la date de fin de promotion + Mise à jour de la date de début
                    }else if($dateFinPromotion === null){
                        $stmtUpdatePromo = $dbh->prepare("UPDATE sae3_skadjam._promotion
                                                        SET date_debut_promotion = :date_debut,
                                                            date_fin_promotion = :date_fin
                                                        WHERE id_promotion = :id_promotion");
                        $stmtUpdatePromo->execute([
                            ':date_debut'   => formatDate($dateDebutPromotion),
                            ':date_fin'     => null,
                            ':id_promotion' => $idPromotion
                        ]);
                    }else{  
                        echo "La date de fin de promotion est invalide.";
                    }
                }else{
                    echo "La date de début de promotion est invalide.";
                }

                // Mise à jour du libellé de la promotion
                if(strlen($labelPromo) < 20){
                    $stmtLibelle = $dbh->prepare("UPDATE sae3_skadjam._promotion SET
                                                label = :label
                                                WHERE id_promotion = :id_promotion");
                    $stmtLibelle->execute([
                        ':label' => $labelPromo,
                        ':id_promotion' => $idPromotion
                    ]);
                }else{
                    echo "Le libellé de la promotion est invalide.";
                }
            }

            //Update de la photo dans la table photo
            $updatePhoto = $dbh -> query("UPDATE sae3_skadjam._photo SET
                                            url_photo = '/images/photo_importees/$nom_photo_finale', 
                                            alt = '$nom', 
                                            titre = '$nom'
                                        WHERE id_photo = $idPhoto;");
            
            //Update remise
            // pour la supression d'une remise
            $deleteRemise = $dbh->prepare("
                DELETE FROM sae3_skadjam._remise
                WHERE id_remise = ?");
            $deleteReduit = $dbh->prepare("
                DELETE FROM sae3_skadjam._reduit
                WHERE id_remise = ? AND id_produit = ?");

            // pour créer une nouvelle remise
            $insertRemise = $dbh->prepare("
                WITH id_remise AS (
                    INSERT INTO sae3_skadjam._remise(pourcentage_remise, date_debut_remise) 
                    VALUES (?, ?) RETURNING id_remise
                )
                INSERT INTO sae3_skadjam._reduit(id_produit, id_remise) 
                    SELECT ?, id_remise FROM id_remise");
                

            // pour la modification d'une remise
            $updateRemise = $dbh->prepare("
                UPDATE sae3_skadjam._remise
                SET pourcentage_remise = ?
                WHERE id_remise = ?");
            
            //mise à jour de la base de données
            $pourcentage = $_POST["remise"];
            $pourcentage = ($pourcentage/100);
            $existe = false;  //si le produit a déjà une remise
            foreach($dbh->query("SELECT * FROM sae3_skadjam._reduit WHERE id_produit = $idProduit", PDO::FETCH_ASSOC) as $row){
                $existe = true;
                // modification d'une remise
                if (verifPourcentage($pourcentage) && $pourcentage != 0) {
                    $updateRemise->execute([$pourcentage, $row['id_remise']]);
                }
                // supression d'une remise
                elseif(verifPourcentage($pourcentage) && $pourcentage == 0){
                    $deleteReduit->execute([$row['id_remise'], $idProduit]);
                    $deleteRemise->execute([$pourcentage]);
                }
                else{
                    echo "le format du pourcentage n'est pas correcte";
                }
            }
            // insertion d'une remise
            if (!$existe && $pourcentage != 0){
                $date = date('d/m/Y'); 
                $insertRemise->execute([$pourcentage, $date, $idProduit]);
            }
            
        }catch (PDOException $e) {
            print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
        }
    }else{
        echo "Le prix ou la quantité saisi est incorrect.";
    }
    header("Location: ./details_produit.php?idProduit=$idProduit");
}
else { ?>

<!DOCTYPE html>
<html lang="fr">
    <?php include __DIR__."/../../php/structure/head_back.php";?>
    <head>
        <title>Modifier <?php echo $nom; ?></title>
        <style>
            button a:hover{
                color : black;
            }
        </style>
    </head>
    <body>
        <?php include __DIR__ . '/../../php/structure/header_back.php';?>
        <?php include __DIR__ . '/../../php/structure/navbar_back.php';?>
        <main class="flex flex-col items-center">
            <h2>Modifier <?php echo $nom; ?></h2>
            <form class="grid grid-cols-[40%_60%] w-11/12 self-center" action="modifier_produit.php?idProduit=<?php echo $idProduit;?>" method="post" enctype="multipart/form-data">
                <!-- Image -->
                <div class="row-start-1 row-span-3 m-2 p-4 grid grid-rows-[2/3-1/3] justify-items-center">
                    <input type="file" id="photo" name="photo" class="hidden">
                    <!-- label qui agit comme bouton -->
                    <label id="labelImage" for="photo" class=" w-60 h-60 rounded-xl image-produit" style="background-image: url('<?php echo $urlPhoto ?>'); background-repeat: no-repeat; background-position: center; background-size: 100%;"></label>
                    <label class="cursor-pointer" for="photo">Ajouter une image*</label>
                </div>
                
                <!-- Nom produit -->
                <div class="col-start-2 row-start-1 flex flex-col w-155 m-2 p-2">
                    <label for="nom">Nom produit *:</label>
                    <input value="<?php echo $nom;?>" placeholder="Confiture fraises des bois 200g" class="border-4 border-beige rounded-2xl m-2 placeholder-gray-500" type="text" name="nom" id="nom" required>
                </div>

                <div class="col-start-2 row-start-2 flex flex-row justify-between w-155 m-2 p-2">
                    <!-- Prix hors taxe -->
                    <div class="flex flex-col">
                        <label for="prix">Prix *(hors taxe):</label>
                        <input placeholder="3.99" value="<?php echo $prixHT;?>" class="placeholder-gray-500 border-4 border-beige rounded-2xl w-40 m-2" type="number" name="prix" id="prix" min="0.0" step="0.01" required>
                    </div>

                    <!-- Remise -->
                    <div class="flex flex-col">
                        <label for="remise">Remise (%):</label>
                        <input value="<?php echo $remise*100;?>" placeholder="0" class="border-4 border-beige rounded-2xl w-40 m-2 placeholder-gray-500" type="number" name="remise" id="remise" min="0" max="100">
                    </div>

                    <!-- Quantite en stock -->
                    <div class="flex flex-col">
                        <label for="qteStock">Quantité en stock* :</label>
                        <input value="<?php echo $qteStock;?>" placeholder="50" class="border-4 border-beige rounded-2xl w-40 m-2 placeholder-gray-500" type="number" name="qteStock" id="qteStock" min="0" required>
                    </div>
                </div>
                    
                <div class="col-start-2 row-start-3 col-span-2 flex flex-row justify-between w-155 m-2 p-2">
                    <!-- Catégorie -->
                    <div class="flex flex-col">
                        <label for="categorie">Catégorie* :</label>
                        <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="categorie" id="categorie" required>
                            <option value="<?php echo $idCategorie;?>"><?php echo $nomCategorie;?></option>
                            <?php foreach ($tab_categories as $categorie) {?>
                                <option value="<?php echo $categorie['id_categorie']?>"><?php echo $categorie['libelle_categorie']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <!-- Unité -->
                    <div class="flex flex-col">
                        <label for="unite">Unité* :</label>
                        <select class="border-4 border-beige rounded-2xl m-2 p-2 w-40 h-14 cursor-pointer" name="unite" id="unite" required>
                        <option value="<?php echo $unite;?>"><?php echo $unite;?></option>
                        <?php foreach ($tab_unite as $unite) {?>
                            <option value="<?php echo $unite;?>"><?php echo $unite;?></option>
                        <?php } ?>
                    </select>
                    <!-- Quantite unité -->
                    </div>
                    <div class="flex flex-col">
                        <label for="qteUnite">Quantité par unité :</label>
                        <input value="<?php echo $qteUnite;?>" placeholder="200" class="placeholder-gray-500 border-4 border-beige rounded-2xl w-40 m-2" type="number" name="qteUnite" id="qteUnite" min="0" required>
                    </div>
                </div>

                
                <div class="col-start-1 row-start-4 col-span-2 flex flex-row justify-around m-2 p-2">
                    <!-- Mettre en ligne -->
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnLigne">Mettre en ligne</label>
                        <input class="cursor-pointer appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige checked:border-vertFonce" type="checkbox" name="mettreEnLigne" id="mettreEnLigne" <?php echo ($enLigne == 'true') ? 'checked' : ''; ?>>
                    </div>
                    <?php 
                        $stmtNbPromos = $dbh->prepare("SELECT COUNT(*) AS nb_promotions
                                                        FROM sae3_skadjam._promu pu
                                                        INNER JOIN sae3_skadjam._promotion pn
                                                            ON pu.id_promotion = pn.id_promotion
                                                        WHERE pn.id_vendeur = :id_vendeur");
                        $stmtNbPromos->execute([':id_vendeur' => $_SESSION['idCompte']]);
                        $nbPromos = $stmtNbPromos->fetch(PDO::FETCH_ASSOC)['nb_promotions'];
                    ?>
                    <div class="flex flex-row mr-4 ml-4">
                        <label class="mr-4" for="mettreEnPromotion">Mettre en promotion<?php if ($nbPromos >= 2 && !$caseCochee) { echo " (Limite atteinte)"; } ?></label>
                        <input id="promoCheck" type="checkbox" name="mettreEnPromotion" class="<?php echo ($nbPromos >= 2 && !$caseCochee) ? 'cursor-not-allowed' : 'cursor-pointer'; ?> appearance-none w-10 h-10 border-4 border-beige rounded-md checked:bg-beige checked:border-vertFonce" <?php echo $caseCochee ? 'checked' : ''; ?> <?php echo ($nbPromos >= 2 && !$caseCochee) ? 'disabled' : ''; ?>>
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
                                <input class="border-4 border-beige rounded-2xl w-45" type="date" name="dateFinPromotion" id="dateFinPromotion" value="<?php if(isset($dateFinPromotion)){echo $dateFinPromotion;} ?>">
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-row justify-around m-2 p-2">
                        <div class="flex flex-row mr-4 ml-4">
                            <label class="mr-4" for="labelPromo">Libellé de la promotion :</label>
                            <input class="border-4 border-beige rounded-2xl w-45" maxlength="20" type="text" name="labelPromo" id="labelPromo" value="<?php if(isset($labelPromo)){echo $labelPromo;}?>">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="col-start-1 col-span-2 row-start-6 flex flex-col m-2 p-2 ">
                    <label for="description">Description *:</label>
                    <textarea placeholder="Pot de confiture de fraises des bois" class="border-4 border-beige rounded-2xl w-3/4 self-center placeholder-gray-500" name="description" id="description" cols="100" rows="10" required><?php echo $description ;?></textarea>
                </div>
                
                <!-- Validation -->
                <div class="col-start-1 col-span-2 row-start-7 flex flex-row justify-around m-4">
                    <a href="../bo/details_produit.php?idProduit=<?php echo $idProduit ;?>" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer">Retour</a>
                    <input class="border-2 border-vertFonce rounded-2xl w-40 h-14 cursor-pointer" type="submit" value="Valider">
                </div>
            </form>
        </main>
        <?php include __DIR__ . '/../../php/structure/footer_back.php';?> 
        <script src="../../js/bo/changement_image_produits.js"></script>
    </body>
</html>
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
<?php } ?>

