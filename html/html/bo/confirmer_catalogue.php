<?php 
    error_reporting(E_ALL & ~E_DEPRECATED);
    ob_start();
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_bo.php");
    include(__DIR__ .'/../../01_premiere_connexion.php');
    require_once(__DIR__ . "/../../php/fonctions.php");
    require_once(__DIR__ . '/../../php/verification_formulaire.php');
    $idCompte = $_SESSION['idCompte'];

    try {  
        $tabProduit = null;           
        //récupère toutes les infos des tables produits
        foreach($dbh->query("SELECT pr.id_produit, pr.libelle_produit, pr.prix_ttc, pr.note_moyenne, pr.quantite_stock, p.url_photo, p.alt, p.titre, pr.seuil_alerte, pr.description_produit
                            FROM sae3_skadjam._produit pr
                            INNER JOIN sae3_skadjam._vendeur v
                                ON pr.id_vendeur = v.id_compte
                            INNER JOIN sae3_skadjam._montre m
                                ON m.id_produit = pr.id_produit
                            INNER JOIN sae3_skadjam._photo p
                                ON p.id_photo = m.id_photo
                            WHERE v.id_compte = $idCompte AND pr.est_supprime = false 
                            ORDER BY libelle_produit ASC"
                            , PDO::FETCH_ASSOC) as $row){
            $tabProduit[] = $row;
        } 

    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

    if(isset($_POST['action']) && $_POST['action'] == 'confirmer'){
        // Traitement de la confirmation
        require '../../../vendor/autoload.php';

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 13);
        $i=0;
        $j=0;
        $y=0;
        foreach($tabProduit as $id => $valeurs){
            $idProduit = $valeurs['id_produit'];
            $imgPath = "../.." . $valeurs['url_photo'];
            $ext = strtolower(pathinfo($imgPath, PATHINFO_EXTENSION));
            if(isset($_POST[$idProduit]) && $_POST[$idProduit] == "on"){
                if(file_exists($imgPath)){
                    $img = false;

                    if($ext==="webp" && function_exists('imagecreatefromwebp')){
                        $img = imagecreatefromwebp($imgPath);
                    }elseif($ext==="png" && function_exists('imagecreatefrompng')){
                        $img = imagecreatefrompng($imgPath);
                    }elseif(($ext==="jpg" || $ext==="jpeg") && function_exists('imagecreatefromjpeg')){
                        $img = imagecreatefromjpeg($imgPath);
                    }elseif(function_exists('imagecreatefromstring')){
                        $img = @imagecreatefromstring(file_get_contents($imgPath));
                    }

                    if($img !== false){ // Charger avec les images
                        if($i===0){
                            $tmp = tempnam(sys_get_temp_dir(), 'img') . '.png';
                            imagepng($img, $tmp);

                            $y = $pdf->GetY();
                            $pdf->Image($tmp, 5, $y, 0, 20);

                            $pdf->SetXY(35, $y);

                            $titre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['titre']);
                            $pdf->MultiCell(65, 10, $titre, 0, 1);

                            $pdf->SetX(35);
                            $prix = iconv('UTF-8', 'Windows-1252', $valeurs['prix_ttc'] . " €");
                            $pdf->MultiCell(65, 10, $prix, 0, 0);

                            $pdf->SetX(10);
                            $description = html_entity_decode($valeurs['description_produit'], ENT_QUOTES, 'UTF-8');
                            $description = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['description_produit']);
                            $pdf->MultiCell(65, 10, $description, 0, 0);

                            $i=1;
                        }else{
                            $tmp = tempnam(sys_get_temp_dir(), 'img') . '.png';
                            imagepng($img, $tmp);

                            $pdf->Image($tmp, 110, $y, 0, 20);

                            $pdf->SetXY(140, $y);

                            $titre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['titre']);
                            $pdf->MultiCell(65, 10, $titre, 0, 1);

                            $pdf->SetX(140);
                            $prix = iconv('UTF-8', 'Windows-1252', $valeurs['prix_ttc'] . " €");
                            $pdf->MultiCell(65, 10, $prix, 0, 0);

                            $pdf->SetX(110);
                            $description = html_entity_decode($valeurs['description_produit'], ENT_QUOTES, 'UTF-8');
                            $description = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['description_produit']);
                            $pdf->MultiCell(65, 10, $description, 0, 0);

                            $pdf->Ln(10);

                            $i=0;
                        }
                        $j++;
                        if($j===8){
                            $pdf->AddPage();
                            $j=0;
                        }
                    }else{ // Charger sans les images
                        if($i===0){
                            $y = $pdf->GetY();

                            $pdf->SetXY(35, $y);

                            $titre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['titre']);
                            $pdf->MultiCell(65, 10, $titre, 0, 1);

                            $pdf->SetX(35);
                            $prix = iconv('UTF-8', 'Windows-1252', $valeurs['prix_ttc'] . " €");
                            $pdf->MultiCell(65, 10, $prix, 0, 0);

                            $pdf->SetX(10);
                            $description = html_entity_decode($valeurs['description_produit'], ENT_QUOTES, 'UTF-8');
                            $description = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['description_produit']);
                            $pdf->MultiCell(65, 10, $description, 0, 0);

                            $i=1;
                        }else{
                            $pdf->SetXY(140, $y);

                            $titre = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['titre']);
                            $pdf->MultiCell(65, 10, $titre, 0, 1);

                            $pdf->SetX(140);
                            $prix = iconv('UTF-8', 'Windows-1252', $valeurs['prix_ttc'] . " €");
                            $pdf->MultiCell(65, 10, $prix, 0, 0);

                            $pdf->SetX(110);
                            $description = html_entity_decode($valeurs['description_produit'], ENT_QUOTES, 'UTF-8');
                            $description = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $valeurs['description_produit']);
                            $pdf->MultiCell(65, 10, $description, 0, 0);

                            $pdf->Ln(10);

                            $i=0;
                        }
                        $j++;
                        if($j===18){
                            $pdf->AddPage();
                            $j=0;
                        }
                    }
                }else{
                    error_log("Fichier image inexistant : $imgPath");
                }
            }
        }
        $pdf->Output('D','Catalogue.pdf');
        exit;
    }else{ 
        if(empty($_POST)){
            header("Location: catalogue.php");
            exit();
        } 
    }?>
    
<!DOCTYPE html>
<html lang="fr">
<?php include(__DIR__ . "/../../php/structure/head_back.php");?>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un catalogue</title>
</head>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[545px]">
        <h2>Produits choisis</h2>

        <!---affichage si catalogue vide--->
        <?php if(empty($tabProduit)){ ?>
            <p>Vous n'avez pas choisi de produits</p>
            <a href="./catalogue.php" class="flex justify-center md:mt-15 md:mb-15 mt-5 mb-5"><button class="border-vertFonce border-2 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        <?php }else{ ?>
            <form class="flex justify-center flex-row-reverse mb-14" method="post" action="./confirmer_catalogue.php">

                <div class="flex justify-around flex-col sticky top-1/4 h-50">
                    <!---bouton retour--->
                    <a href="./catalogue.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer">Retour</a>
                    <!---bouton télécharger--->
                    <input type="hidden" name="action" id="action" value="confirmer">
                    <input type="submit" formaction="./confirmer_catalogue.php" class="flex justify-center items-center border-2 border-vertFonce rounded-2xl w-45 h-14 cursor-pointer" value="Télécharger">
                </div>

                <!---tableau liste des stocks--->
                <table class="table-auto w-2/3">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"><h3 class="text-left">Produit</h3></th>
                            <th scope="col"><h3>Prix</h3></th>
                            <th scope="col"><h3>Note</h3></th>
                            <th scope="col"><h3>Stock</h3></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $ligneIndex = 1;
                            foreach($tabProduit as $id => $valeurs){
                                $idProduit = $valeurs['id_produit'];
                                if(isset($_POST[$idProduit]) && $_POST[$idProduit] == "on"){ ?>
                                    <input type="hidden" name="<?php echo $idProduit; ?>" id="<?php echo $idProduit; ?>" value="on">
                                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                        <!---informations des stocks--->
                                        <td class="py-3 w-24 text-center">
                                            <img class="w-16 h-16 object-contain inline-block" 
                                                src="<?php echo $valeurs['url_photo'];?>" 
                                                alt="<?php echo $valeurs['alt'];?>" 
                                                title="<?php echo $valeurs['titre'];?>">
                                        </td>
                                        <td scope="row" class="text-left py-3" ><a href="<?php echo htmlentities("details_produit.php?idProduit=".$idProduit);?>"><?php echo $valeurs['libelle_produit']; ?></a></td>
                                        <td class="text-center py-3"><p><?php echo str_replace('.',',',$valeurs['prix_ttc']);?> €</p></td>
                                        
                                        <td class="text-center py-3">
                                            <div class="flex justify-center items-center">
                                                <?php 
                                                    $note = $valeurs['note_moyenne'];
                                                    affichageNote($note); 
                                                ?>
                                            </div>
                                        </td>
                                        <td class="text-center py-3">
                                            <p><?php echo htmlentities($valeurs['quantite_stock']); ?></p>
                                            <p class="hidden"><?php echo ($valeurs['seuil_alerte']!== null)?$valeurs['seuil_alerte']:0; ?></p>
                                        </td>
                                        <td class=" min-w-15 bg-white"></td>
                                    </tr>
                                <?php }} ?>
                    </tbody>
                </table>
            </form>
        <?php } ?>
    </main>

    <!--footer-->
    <?php include __DIR__ . "/../../php/structure/footer_back.php"; ?>
    <script>
        let lignesTab = document.getElementsByTagName("tr");

        let rouge = "#A70101";
        let rougeClaire = "#E04C4C";

        let seuil;

        for(let i = 1; i < lignesTab.length; i++){
            seuil = lignesTab[i].children[4].children[1].textContent;

        }

    </script>
</body>
</html>