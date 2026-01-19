<?php
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require(__DIR__ . '/../../01_premiere_connexion.php');
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
                d.sous_total as sous_total_ht
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

    <main class="min-h-[600px]">
        
        <h2 class="mt-10">Récapitulatif de la commande</h2>

        <div class="flex justify-between md:hidden mr-5 ml-5">
            <a href="liste_commandes.php" class="flex justify-center"><button class="border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Retour</button></a>
            <button id="imprimer" class="border-vertClair border-4 rounded-lg w-35 h-10 px-7 cursor-pointer">Imprimer</button>
        </div>

        <div class="ml-5 flex flex-row items-center mt-10">
            <h3 class="mr-3">Numéro de la commande : </h3> 
            <h3 id="numeroCommande"><?php echo $idCommande;?></h3>
        </div>
        
        <div class="md:flex md:justify-between">
            <h3 class="ml-5 mr-3">Date : <?php echo $date;?></h3>
            <button id="imprimer" class="border-vertClair border-4 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 mr-5 cursor-pointer hidden md:block">Imprimer</button>
        </div>

        <div class="flex justify-center md:mt-10">
        <!--TABLEAU DES COMMANDES VERSION TABLETTE-->
            <table class="table-auto w-280 md:inline-table hidden">
                <thead>
                    <tr>
                        <th class="text-left w-90 pl-3"><h4>Article</h4></th>
                        <th class="pr-3"><h4>Prix unitaire HT</h4></th>
                        <th class="pr-3"><h4>Prix unitaire TTC</h4></th>
                        <th class="pr-3"><h4>Pourcentage remise</h4></th>
                        <th class="pr-3"><h4>Quantité</h4></th>
                        <th class="pr-3"><h4>Total</h4></th>
                        
                    </tr>
                </thead>
                <tbody>
                    <?php $impair = 1;
                    foreach($tabVendeur as $vendeur){
                        $impair ++;
                        if(fmod($impair, 2) == 0){
                            $classe = "py-4";
                        }
                        else{
                            $classe = "py-4 bg-bleu";
                        }?>
                        <tr class="<?php echo $classe; ?> border-t-2 border-solid border-black">
                            <th colspan="6" class="text-left py-3 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                        </tr>
                        <?php foreach($tabInfosCommande as $ligne){ 
                            if($ligne['raison_sociale'] == $vendeur){
                                $impair ++;
                                if(fmod($impair, 2) == 0){
                                    $classe = "py-4";
                                }
                                else{
                                    $classe = "py-4 bg-bleu";
                                }?>
                                <tr class="<?php echo $classe; ?>">
                                    <td class="text-left py-3 pl-3"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['prix_ht'];?></p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['prix_ttc'];?></p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['pourcentage_remise']*100;?>%</p></td>
                                    <td class="text-center py-3"><p><?php echo $ligne['quantite'];?></p></td>
                                    <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                            $total_ligne = $ligne['prix_remise'] * $ligne['quantite'] ;    
                                    } 
                                    else{
                                        $total_ligne = $ligne['prix_ttc'] * $ligne['quantite'] ;
                                    }?>
                                    <td class="text-center py-3"><p><?php echo $total_ligne;?></p></td>
                                    <?php 
                                        //calcul du total ht de la commande
                                        $total_ht = $total_ht + $ligne['sous_total_ht'];
                                        //calcul du sous total final par vendeur
                                        $sous_total_final += $total_ligne;
                                        //calcul du nombre de produit final
                                        $quantite_totale += $ligne['quantite'];
                                    ?>
                                </tr>
                            <?php } 
                        }
                        $impair ++;
                        if(fmod($impair, 2) == 0){
                            $classe = "py-4";
                        }
                        else{
                            $classe = "py-4 bg-bleu";
                        }?>
                        <tr class="<?php echo $classe; ?>">
                            <th colspan="5" class="text-left w-90 pl-3"><p>Sous-total :</p></th>
                            <th class="text-center py-3"><p><?php echo $sous_total_final;?></p></th>
                        </tr>
                        <?php 
                            $total_final += $sous_total_final;
                            $sous_total_final = 0;
                    } ?>
                </tbody>
                <tfoot>
                    <?php $impair ++;
                        if(fmod($impair, 2) == 0){
                            $classe = "py-4";
                        }
                        else{
                            $classe = "py-4 bg-bleu";
                        };?>
                    <tr class="<?php echo $classe; ?>">
                        <th class="text-left w-90 pl-3"><h4>Total :</h4></th>
                        <th class="text-center py-3"><h4><?php echo $total_ht;?></h4></th>
                        <th class="text-center py-3"><h4><?php echo $total_ttc;?></h4></th>
                        <th></th>
                        <th class="text-center py-3"><h4><?php echo $quantite_totale;?></h4></th>
                        <th class="text-center py-3"><h4><?php echo $total_final;?></h4></th>
                    </tr>
                </tfoot>
            </table>

            <!--TABLEAU VERSION TELEPHONE-->
            <table class="table-auto w-95 md:hidden block mt-6">
                <tbody>
                    <?php $ligneIndex = 0;
                    function ligneCouleur(&$i) {
                        $i++;
                        return ($i % 2 === 0) ? 'bg-bleu' : '';
                    }

                    foreach($tabVendeur as $vendeur){ ?>
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                            <th colspan="2" class="text-left py-2 pl-3"><h4>Vendeur : <?php echo $vendeur ;?></h4></th>
                        </tr>
                        <?php foreach($tabInfosCommande as $ligne){ 
                            if($ligne['raison_sociale'] == $vendeur){ ?>  
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4 class="w-40">Article</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['id_produit'];?> - <?php echo $ligne['libelle_produit'];?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Prix HT</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['prix_ht'];?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Prix TTC</h4></th>
                                        <td class="text-left"><p><?php echo $ligne['prix_ttc'];?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>% remise</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['pourcentage_remise']*100;?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                                    <th class="text-left py-2 pl-3"><h4>Quantité</h4></th>
                                    <td class="text-left"><p><?php echo $ligne['quantite'];?></p></td>
                                </tr>
                                <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-b-2 border-solid border-black">
                                    <th class="text-left py-2 pl-3"><h4>Total produit</h4></th>
                                    <?php if($ligne['prix_remise'] != $ligne['prix_ttc']){ 
                                        $total_ligne = $ligne['remise'] * $ligne['quantite'] ;    
                                    } 
                                    else{
                                        $total_ligne = $ligne['prix_ttc'] * $ligne['quantite'] ;
                                    } ?>
                                    <td class="text-left"><p><?php echo $total_ligne;?></p></td>
                                </tr>

                                <?php 
                                    //calcul du total ht de la commande
                                    $total_ht = $total_ht + $ligne['sous_total_ht'];
                                    //calcul du sous total final par vendeur
                                    $sous_total_final += $total_ligne;   
                                    //calcul du nombre de produit final
                                    $quantite_totale += $ligne['quantite'];       
                            } 
                        } ?>
                        <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                            <th class="text-left py-2 pl-3"><h4>Sous-total vendeur</h4></th>
                            <th class="text-left"><p><?php echo $sous_total_final;?></p></th>
                        </tr>
                        <?php
                            $total_final += $sous_total_final;
                            $sous_total_final = 0;
                    } ?>
                </tbody>
                <tfoot>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?> border-t-2 border-solid border-black">
                        <th class="text-left py-2 pl-3"><h4>Total HT : </h4></th>
                        <th class="text-left"><h4><?php echo $total_ht;?></h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Total TTC : </h4></th>
                        <th class="text-left"><h4><?php echo $total_ttc;?></h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Quantité totale : </h4></th>
                        <th class="text-left"><h4><?php echo $quantite_totale;?></h4></th>
                    </tr>
                    <tr class="py-4 <?= ligneCouleur($ligneIndex) ?>">
                        <th class="text-left py-2 pl-3"><h4>Total Final : </h4></th>
                        <th class="text-left"><h4><?php echo $total_final;?></h4></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <a href="liste_commandes.php" class="flex justify-center mt-15 mb-15"><button class="border-vertClair border-4 rounded-lg md:rounded-2xl w-35 h-10 md:w-50 md:h-14 px-7 cursor-pointer">Retour</button></a>
    </main>
    <script>
        let btnImprimmer = document.getElementById("imprimer");
        let numeroCommande = document.getElementById("numeroCommande");

        function fermerPageImpression() {
            // fermer la page d'impression
            document.body.removeChild(this); 
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

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>

</body>
</html>