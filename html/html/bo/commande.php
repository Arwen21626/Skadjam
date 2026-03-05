<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_bo.php";
    require_once __DIR__ . "/../../php/fonctions.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];
    $idCommande = $_GET['idCommande'];

    //=== Récupération de la commande ===
    try{               
        $sql = "SELECT 
                c.id_commande, 
                c.date_commande, 
                pr.libelle_produit, 
                pr.id_produit, 
                v.raison_sociale,
                d.quantite, 
                pr.prix_ht, 
                pr.prix_ttc, 
                pr.prix_remise,
                r.pourcentage_remise,
                pr.prix_ttc * d.quantite as sous_total_ttc,
                pr.prix_ht * d.quantite as sous_total_ht,
                pr.prix_remise * d.quantite as sous_total_remise,
                p.url_photo, 
                p.alt, 
                p.titre
                FROM sae3_skadjam._commande c
                INNER JOIN sae3_skadjam._details d
                    ON d.id_commande = c.id_commande
                INNER JOIN sae3_skadjam._produit pr
                    ON pr.id_produit = d.id_produit
                INNER JOIN sae3_skadjam._vendeur v
                    ON v.id_compte = pr.id_vendeur
                LEFT JOIN sae3_skadjam._reduit rd
                    ON rd.id_produit = pr.id_produit 
                LEFT JOIN sae3_skadjam._remise r
                    ON r.id_remise = rd.id_remise
                INNER JOIN sae3_skadjam._montre m
                    ON m.id_produit = pr.id_produit
                INNER JOIN sae3_skadjam._photo p
                    ON p.id_photo = m.id_photo
                WHERE c.id_commande = :id_commande AND pr.id_vendeur = :id_vendeur";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([':id_commande' => $idCommande,':id_vendeur'  => $idCompte]);
                      
        $tabInfosCommande = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($tabInfosCommande)) {
            die("Erreur : commande inexistante");
        }

        //Déclaration variables
        $date = $tabInfosCommande[0]['date_commande'];
        $v_quantite_totale = 0;
        $v_total_ht = 0;
        $v_total_ttc = 0;
        $total_ligne = 0;
        $v_total_final = 0;
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
    <title>Récapitulatif de la commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_back.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_back.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_back.php"; ?>

    <main class="min-h-[600px]">
        <h2 class="mt-10">Récapitulatif de la commande</h2>

        <!---numéro de commande--->
        <div class="ml-5 flex flex-row items-center mt-10">
            <h3 class="mr-3">Numéro de la commande : </h3> 
            <h3 id="numeroCommande"><?php echo $idCommande;?></h3>
        </div>
        
        <div class="md:flex md:justify-between">
            <!---date--->
            <h3 class="ml-5 mr-3">Date : <?php echo $date;?></h3>
            <!---bouton imprimer--->
            <button id="imprimer" class="border-vertFonce border-2 rounded-2xl w-65 h-14 px-7 mr-5 cursor-pointer">Imprimer la facture</button>
        </div>

        <div class="flex justify-center mt-10">
            <?php //tableau de la commande ?>
            <table class="table-auto w-290">
                <thead>
                    <tr>
                        <!---noms des colonnes--->
                        <th class="pr-3 w-24"></th>
                        <th class="pr-3"><h4 class="text-left">Article</h4></th>
                        <th class="pr-3"><h4>Prix unitaire <abbr title="Hors Taxe">HT</abbr></h4></th>
                        <th class="pr-3"><h4>Prix unitaire <abbr title="Toutes Taxes Comprises">TTC</abbr></h4></th>
                        <th class="pr-3"><h4>Pourcentage remise</h4></th>
                        <th class="pr-3"><h4>Quantité</h4></th>
                        <th class="pr-3"><h4>Total</h4></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $ligneIndex = 1;
                        foreach($tabInfosCommande as $ligne){ ?>
                            <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                <!---affichage des informations de chaque produit de la commande--->
                                <td class="py-3 w-24 text-center">
                                    <img class="w-16 h-16 object-contain inline-block" 
                                        src="<?php echo $ligne['url_photo'];?>" 
                                        alt="<?php echo $ligne['alt'];?>" 
                                        title="<?php echo $ligne['titre'];?>">
                                </td>
                                <td class="text-left py-3 pl-3"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ht']);?>€</p></td>
                                <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ttc']);?>€</p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['pourcentage_remise']*100;?>%</p></td>
                                <td class="text-center py-3"><p><?php echo $ligne['quantite'];?></p></td>
                                <!---affichage du total de ligne (quantite et remise comprise)--->
                                <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                        $total_ligne = $ligne['sous_total_remise'];    
                                } 
                                else{
                                    $total_ligne = $ligne['sous_total_ttc'];
                                }
                                $prix = explode(".", "$total_ligne");?>
                                <td class="text-center py-3 pr-3"><p>
                                    <?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€
                                </p></td>
                                <?php 
                                    //calcul des totaux
                                    $v_quantite_totale += $ligne['quantite'];
                                    $v_total_ht += $ligne['sous_total_ht'];
                                    $v_total_ttc += $ligne['sous_total_ttc'];
                                    $v_total_final += $total_ligne;
                                ?>
                            </tr>
                        <?php } ?>
                </tbody>
                <tfoot>
                    <!---affichage des totaux de la commande--->
                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                        <th class="text-left w-90 pl-3" colspan="2"><h4>Total :</h4></th>
                        <!---total ht--->
                        <?php $prix = explode(".", "$v_total_ht");?>
                        <th class="text-center py-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        <!---total ttc--->
                        <?php $prix = explode(".", "$v_total_ttc");?>
                        <th class="text-center py-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                        <th></th>
                        <!---quantité totale--->
                        <th class="text-center py-3"><h4><?php echo $v_quantite_totale;?></h4></th>
                        <!---total final--->
                        <?php $prix = explode(".", "$v_total_final");?>
                        <th class="text-center py-3 pr-3"><h4><?php echo htmlentities($prix[0].",".((preg_match("/^[1-9]$/", $prix[1]))?$prix[1]."0":$prix[1]));?>€</h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <!---bouton retour--->
        <a href="liste_commandes.php" class="flex justify-center m-10">
            <button class="border-vertFonce border-2 rounded-sm md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button>
        </a>
    </main>
    
    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_back.php"); ?>

    <!---script pour l'impression d'une facture--->
    <script>
        let btnImprimmer = document.getElementById("imprimer");
        let numeroCommande = document.getElementById("numeroCommande");

        function fermerPageImpression() {
            // fermer la page d'impression
            let iframe = document.getElementsByTagName("iframe")[0];
            let body = document.getElementsByTagName("body")[0];
            body.removeChild(iframe); 
        }

        function gestionPageImpression() {
            // définie quand est ce qu'on peut fermer la page d'impression
            // et définie un iframe de type impression
            this.contentWindow.onbeforeunload = fermerPageImpression;
            this.contentWindow.onafterprint = fermerPageImpression;
            this.contentWindow.print(); // indique que c'est une page qui permet d'imprimmer
        }
        function affichagePageImpression(){
            const hideFrame = document.createElement("iframe"); // création d'un iframe
            hideFrame.onload = gestionPageImpression;
            hideFrame.src = "../facture.php?idCommande="+numeroCommande.innerText;
            document.body.appendChild(hideFrame); // ajoute dans le body le iframe pour l'impression
        }

        btnImprimmer.addEventListener("click", () => {affichagePageImpression()});
    </script>
</body>
</html>