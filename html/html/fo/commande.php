<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require_once __DIR__ . "/../../php/fonctions.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
    include __DIR__ . '/../../connexion_recupraptor.php';
    $idCompte = $_SESSION['idCompte'];
    $idCommande = $_GET['idCommande'];

    //=== Récupération de la commande ===
    try{               
        $sql = "SELECT 
                c.id_commande, 
                c.date_commande, 
                p.libelle_produit, 
                p.id_produit, 
                p.id_vendeur,
                v.raison_sociale,
                d.quantite, 
                p.prix_ht, 
                p.prix_ttc, 
                p.prix_remise,
                r.pourcentage_remise,
                p.prix_ttc * d.quantite as sous_total_ttc,
                c.montant_total_ttc,
                p.prix_ht * d.quantite as sous_total_ht,
                p.prix_remise * d.quantite as sous_total_remise
                FROM sae3_skadjam._commande c
                INNER JOIN sae3_skadjam._details d
                    ON d.id_commande = c.id_commande
                INNER JOIN sae3_skadjam._produit p
                    ON p.id_produit = d.id_produit
                INNER JOIN sae3_skadjam._vendeur v
                    ON v.id_compte = p.id_vendeur
                LEFT JOIN sae3_skadjam._reduit rd
                    ON rd.id_produit = p.id_produit 
                LEFT JOIN sae3_skadjam._remise r
                    ON r.id_remise = rd.id_remise
                WHERE c.id_commande = :id_commande";

        $stmt = $dbh->prepare($sql);
        $stmt->execute([':id_commande' => $idCommande]);                      
        $tabInfosCommande = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($tabInfosCommande)) {
            die("Erreur : commande inexistante");
        }

        //Déclaration variables
        $tabVendeur = [];
        $date = $tabInfosCommande[0]['date_commande'];
        $total_ht = 0;
        $total_ttc = $tabInfosCommande[0]['montant_total_ttc'];
        $quantite_totale = 0;
        $sous_total_final = 0;
        $total_ligne = 0;
        $total_final = 0;

        foreach($tabInfosCommande as $ligne){
            if(in_array($ligne['id_vendeur'], $tabVendeur) == false){
                $tabVendeur[$ligne['id_vendeur']] = $ligne['raison_sociale'];
            }
        }
    }

    catch (PDOException $e) {
        print "Erreur !: " . $e->getMessage() . "<br/>";
        die();
    }

    $raison = 0;
    $image = 0;

    try {
        $query = "SELECT etat, id_suivi FROM sae3_skadjam._commande WHERE id_commande = ?";
        $stmt = $dbh->prepare($query);
        $stmt->execute([$idCommande]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        $etat = $res['etat'];

        if ($etat === "Refusé"){
            $rpr->get_etat($res['id_suivi']);
            $raison = $rpr->get_raison();
            
        }else if ($etat === "Livré absent"){
            $rpr->get_etat($res['id_suivi']);
            $img_url = $rpr->get_image_url();
            $image = 1;
        }
    } catch (Exception $e){
        echo "Erreur : " . $e->getMessage();
        $etat = "err";
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de la commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <div id="div_lvr" class="hidden fixed bg-black/50 inset-0 z-10 items-center justify-center">
        <img id="img_lvr" src="../../images<?= $img_url ?>" alt="image livraison" class=" w-2xs md:w-2xl rounded-2xl shadow-lg">
    </div>
    <main class="min-h-[600px]">
        
        <h2 class="mt-10">Récapitulatif de la commande</h2>

        <!---boutons retour et imprimer en haut de la page format téléphone--->
        <div class="flex justify-between md:hidden m-10">
            <a href="liste_commandes.php" class="flex justify-center"><button class="border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Retour</button></a>
            <button class="imprimer border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Imprimer</button>
        </div>

        <!---numéro de commande--->
        <div class="ml-5 flex flex-row items-center mt-10">
            <h3 class="mr-3">Numéro de la commande : </h3> 
            <h3 id="numeroCommande"><?php echo $idCommande;?></h3>
        </div>
        
        <div class="md:flex md:justify-between items-center">
            <!---date--->
            <h3 class="ml-5 mr-3">Date : <?php echo $date;?></h3>
            <!---bouton imprimer page--->
            <button class="imprimer border-vertClair border-4 rounded-lg md:rounded-2xl w-35 h-10 md:w-65 md:h-14 px-7 mr-5 cursor-pointer hidden md:block">Imprimer la facture</button>
        </div>

        <!---état de la commande--->
        <div class="ml-5 flex flex-row md:items-center">
            <h3 class="mr-3">
                <span class="block md:hidden">Etat :</span>
                <span class="hidden md:block"> Etat de la livraison :</span>
            </h3> 
            <h3 id="numeroCommande"><?= $etat ?></h3>
        </div>
        
        <?php
        if ($raison != 0){ ?>
        <div class="ml-5 flex flex-row items-center mt-5">
            <h3 class="mr-3">Raison : </h3> 
            <h3 id="numeroCommande"><?= $raison ?></h3>
        </div>
        <?php
        } else if ($image != 0){ ?>
        <button id="btn_img" class="border-vertClair border-4 rounded-lg mt-2 ml-5 md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-1 md:px-7 cursor-pointer">
            Pièce jointe
        </button>


        <?php
        }
        ?>

        <div class="flex justify-center md:mt-10">
        <!--TABLEAU DE LA COMMANDE VERSION TABLETTE-->
            <table class="table-auto w-290 md:inline-table hidden">
                <thead>
                    <tr>
                        <!---noms des colonnes--->
                        <th class="text-left w-90 pl-3"><h4>Article</h4></th>
                        <th class="pr-3"><h4>Prix unitaire HT</h4></th>
                        <th class="pr-3"><h4>Prix unitaire TTC</h4></th>
                        <th class="pr-3"><h4>Pourcentage remise</h4></th>
                        <th class="pr-3"><h4>Quantité</h4></th>
                        <th class="pr-3"><h4>Total</h4></th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php $ligneIndex = 0;
                    foreach($tabVendeur as $vendeur){?>
                        <!---affichage du vendeur--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex)?> border-t-2 border-solid border-black">
                            <th colspan="6" class="text-left py-3 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                        </tr>
                        <?php foreach($tabInfosCommande as $ligne){ 
                            if($ligne['raison_sociale'] == $vendeur){?>
                                <!---affichage des informations de chaque produit de la commande--->
                                <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                                    <td class="text-left py-3 pl-3"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                    <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ht']);?>€</p></td>
                                    <td class="text-center py-3"><p><?php echo str_replace('.',',',$ligne['prix_ttc']);?>€</p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['pourcentage_remise']*100;?>%</p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['quantite'];?></p></td>
                                    <!---affichage du total de ligne (quantite et remise comprise)--->
                                    <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                            $total_ligne = $ligne['prix_remise'] * $ligne['quantite'] ;    
                                    } 
                                    else{
                                        $total_ligne = $ligne['prix_ttc'] * $ligne['quantite'] ;
                                    }?>
                                    <td class="text-center py-3 pr-3"><p><?php echo str_replace('.',',',$total_ligne);?>€</p></td>
                                    <?php 
                                        //calcul du total ht de la commande
                                        $total_ht += $ligne['sous_total_ht'];
                                        //calcul du sous total final par vendeur
                                        $sous_total_final += $total_ligne;
                                        //calcul du nombre de produit final
                                        $quantite_totale += $ligne['quantite'];
                                    ?>
                                </tr>
                            <?php } 
                        }?>
                        <!---sous-total par vendeur--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                            <th colspan="5" class="text-left w-90 pl-3"><p>Sous-total :</p></th>
                            <th class="text-center py-3 pr-3"><p><?php echo str_replace('.',',',$sous_total_final);?>€</p></th>
                        </tr>
                        <?php 
                            //calcul du total final de la commande remise(s) comprise(s)
                            $total_final += $sous_total_final;
                            $sous_total_final = 0;
                    } ?>
                </tbody>
                <tfoot>
                    <!---affichage des totaux de la commande--->
                    <tr class="py-4 <?= ligneCouleur($ligneIndex)?>">
                        <th class="text-left w-90 pl-3"><h4>Total :</h4></th>
                        <th class="text-center py-3"><h4><?php echo str_replace('.',',',$total_ht);?>€</h4></th>
                        <th class="text-center py-3"><h4><?php echo str_replace('.',',',$total_ttc);?>€</h4></th>
                        <th></th>
                        <th class="text-center py-3"><h4><?php echo $quantite_totale;?></h4></th>
                        <th class="text-center py-3 pr-3"><h4><?php echo str_replace('.',',',$total_final);?>€</h4></th>
                    </tr>
                </tfoot>
            </table>

            <!--TABLEAU DE LA COMMANDE VERSION TELEPHONE-->
            <table class="table-auto w-95 md:hidden block mt-6">
                <tbody>
                    <?php $ligneIndex = 0;
                    foreach($tabVendeur as $vendeur){ ?>
                    <!---affichage du vendeur--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                            <th colspan="2" class="text-left py-2 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                        </tr>
                        <?php foreach($tabInfosCommande as $ligne){ 
                            if($ligne['raison_sociale'] == $vendeur){ ?>  
                                <!---affichage des informations de chaque produit de la commande--->
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4 class="w-40">Article</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Prix HT</h4></th>
                                    <td class="text-left"><p><?php echo str_replace('.',',',$ligne['prix_ht']);?>€</p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Prix TTC</h4></th>
                                        <td class="text-left"><p><?php echo str_replace('.',',',$ligne['prix_ttc']);?>€</p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>% remise</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['pourcentage_remise']*100;?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Quantité</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['quantite'];?></p></td>
                                </tr>
                                <!---affichage du total du produit (quantite et remise comprise)--->
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-b-2 border-solid border-black">
                                    <th class="text-left py-2 pl-3"><h4>Total produit</h4></th>
                                    <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                        $total_ligne = $ligne['prix_remise'] * $ligne['quantite'] ;    
                                    } 
                                    else{
                                        $total_ligne = $ligne['prix_ttc'] * $ligne['quantite'] ;
                                    } ?>
                                    <td class="text-left"><p><?php echo str_replace('.',',',$total_ligne);?>€</p></td>
                                </tr>

                                <?php 
                                    //calcul du sous-total par vendeur
                                    $sous_total_final += $total_ligne;         
                            } 
                        } ?>
                        <!---sous-total par vendeur--->
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                            <th class="text-left py-2 pl-3"><h4>Sous-total vendeur</h4></th>
                            <th class="text-left"><p><?php echo str_replace('.',',',$sous_total_final);?>€</p></th>
                        </tr>
                        <?php
                            $sous_total_final = 0;
                    } ?>
                </tbody>
                <tfoot>
                    <!---affichage des totaux de la commande--->
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                        <th class="text-left py-2 pl-3"><h4>Total HT : </h4></th>
                        <th class="text-left"><h4><?php echo str_replace('.',',',$total_ht);?>€</h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Total TTC : </h4></th>
                        <th class="text-left"><h4><?php echo str_replace('.',',',$total_ttc);?>€</h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Quantité totale : </h4></th>
                        <th class="text-left"><h4><?php echo $quantite_totale;?></h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Total Final : </h4></th>
                        <th class="text-left"><h4><?php echo str_replace('.',',',$total_final);?>€</h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!---bouton retour version tablette--->
        <a href="liste_commandes.php" class="hidden md:flex justify-center mt-15 mb-15"><button class="border-vertClair border-4 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
        
        <!---boutons retour et imprimer version téléphone--->
        <div class="flex justify-between md:hidden m-10">
            <a href="liste_commandes.php" class="flex justify-center"><button class="border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Retour</button></a>
            <button class="imprimer border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Imprimer</button>
        </div>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>

        <!---script pour l'impression d'une facture--->
    <script>
        let btnImprimmer = document.getElementsByClassName("imprimer");
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

        btnImprimmer[0].addEventListener("click", () => {affichagePageImpression()});
        btnImprimmer[1].addEventListener("click", () => {affichagePageImpression()});
        btnImprimmer[2].addEventListener("click", () => {affichagePageImpression()});

        const divImage = document.getElementById("div_lvr");
        const btnImage = document.getElementById("btn_img");
        const imageLvr = document.getElementById("img_lvr")
        btnImage.addEventListener("click" , (e) => {
            e.stopPropagation();
            divImage.classList.remove("hidden");
            divImage.classList.add("flex");
            document.body.style.overflow = "hidden"
        });

        document.addEventListener("click" , (e) => {
            if (!imageLvr.contains(e.target)){
                divImage.classList.remove("flex");
                divImage.classList.add("hidden");
                document.body.style.overflow = ""
            }
        })

    </script>
</body>
</html>