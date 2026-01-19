<?php
session_start();

require(__DIR__ . '/../01_premiere_connexion.php');

$idCommande = $_GET['idCommande'];

// récupération de tous les produits de la commande

foreach($dbh->query("SELECT v.raison_sociale, cvend.adresse_mail as mail_vendeur, a.adresse_postale as adresse_postale_vendeur, a.complement_adresse as complement_adresse_vendeur, 
                        a.numero_rue as numero_rue_vendeur, a.code_postal as code_postal_vendeur, a.ville as ville_vendeur, c.date_commande, al.numero_bat as numero_bat_client, 
                        ccli.nom_compte as nom_client, ccli.prenom_compte as prenom_client, ccli.adresse_mail as adresse_mail_client, al.adresse_postale as adresse_postale_client, 
                        al.complement_adresse as complement_adresse_client, al.numero_rue as numero_rue_client, al.code_postal as code_postale_client, al.ville as ville_client, 
                        c.id_commande, f.numero_facture, c.id_commande, p.id_produit, p.libelle_produit, p.prix_ht, p.prix_ttc, r.pourcentage_remise, d.quantite, c.montant_total_ttc, 
                        d.sous_total, v.id_compte as id_vendeur
                    FROM sae3_skadjam._commande c
                    INNER JOIN sae3_skadjam._facture f ON f.id_commande = c.id_commande
                    INNER JOIN sae3_skadjam._adresse_livraison al ON al.id_adresse = c.id_adresse
                    INNER JOIN sae3_skadjam._client cli ON cli.id_compte = c.id_client
                    INNER JOIN sae3_skadjam._details d ON d.id_commande = c.id_commande
                    INNER JOIN sae3_skadjam._produit p ON p.id_produit = d.id_produit
                    LEFT JOIN sae3_skadjam._reduit rd ON rd.id_produit = p.id_produit 
                    LEFT JOIN sae3_skadjam._remise r ON r.id_remise = rd.id_remise
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._habite h ON h.id_compte = v.id_compte
                    INNER JOIN sae3_skadjam._adresse a ON a.id_adresse = h.id_adresse
                    INNER JOIN sae3_skadjam._compte cvend ON cvend.id_compte = v.id_compte
                    INNER JOIN sae3_skadjam._compte ccli ON ccli.id_compte = v.id_compte
                    WHERE c.id_commande = $idCommande AND p.id_vendeur = f.emetteur ORDER BY numero_facture ASC", 
            PDO::FETCH_ASSOC) as $row){

    $idCommande = $row['id_commande'];
    $dateCommande = $row['date_commande'];
    $nomClient = $row['nom_client'];
    $prenomClient = $row['prenom_client'];
    $adrClient = $row['numero_rue_client'].' '.$row['complement_adresse_client'].' '.$row['adresse_postale_client'].' '.$row['ville_client'].', '.$row['code_postale_client'];
    $mailClient = $row['adresse_mail_client'];
    $montantTTC = $row['montant_total_ttc'];
    
    if(($_SESSION["role"] === "vendeur" && $_SESSION["idCompte"] === $row['id_vendeur']) || ($_SESSION["role"] === "client")){
        $info[$row['numero_facture']]['vendeur'] = 
            [
                'raisonSocial' => $row['raison_sociale'],
                'adresseVendeur' => $row['numero_rue_vendeur'].' '.$row['complement_adresse_vendeur'].' '.$row['adresse_postale_vendeur'].' '.$row['ville_vendeur'].', '.$row['code_postal_vendeur'],
                'mailVendeur' => $row['mail_vendeur']
                
            ];
        
        $info[$row['numero_facture']]['produits'][$row['id_produit']] = 
            [
                'libelle' => $row['libelle_produit'],
                'prixHT' => $row['prix_ht'],
                'prixTTC' => $row['prix_ttc'],
                'pourcentageRemise' => $row['pourcentage_remise'],
                'quantite' => $row['quantite'],
                'sousTotal' => $row['sous_total']
            ];
            
        if (isset($info[$row['numero_facture']]['montantFacture'])){
            $info[$row['numero_facture']]['montantFacture'] = $info[$row['numero_facture']]['montantFacture']+$row['sous_total'];
        }
        else{
            $info[$row['numero_facture']]['montantFacture'] = $row['sous_total'];
        }
    }
    
}


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factures de la commande n°<?php echo $idCommande;?></title>
</head>
<?php include __DIR__ . '/../php/structure/head_front.php'?>
<body class=" max-w-1/1">
    <?php foreach ($info as $id => $row){?>
        <div class="h-full break-after-page">
        <h1> Facture n°<?php echo $id?></h1>
        <div class="flex mb-8">
            <section class=" max-w-1/3">
                <h2>Emmeteur</h2>
                <p><?php echo $row['vendeur']['raisonSocial']?></p>
                <p><?php echo $row['vendeur']['mailVendeur']?></p>
                <p><?php echo $row['vendeur']['adresseVendeur']?></p>
            </section>
            <section class=" max-w-1/3">
                <h2>Destinataire</h2>
                <p><?php echo $nomClient?></p>
                <p><?php echo $mailClient?></p>
                <p><?php echo $adrClient?></p>
            </section>
            <section class=" max-w-1/3"> 
                <p class="font-bold">n° de commande : <?php echo $idCommande?></p>
                <p>date : <?php echo $dateCommande?></p>
                <p>payment immédiat</p>
            </section>
        </div>
        <table class="w-1/1">
            <thead>
                <th class="border-r-2 p-1 w-3/8">article</th>
                <th class="border-r-2 p-1 w-1/8">prix unitaire HT</th>
                <th class="border-r-2 p-1 w-1/8">prix unitaire TTC</th>
                <th class="border-r-2 p-1 w-1/8">pourcentage de remise</th>
                <th class="border-r-2 p-1 w-1/8">quantite</th>
                <th class="border-r-2 p-1 w-1/8">total</th>
            </thead>
            <tbody>
                
                <?php foreach($row['produits'] as $idProd => $prod){?>
                    <tr class="text-center border-t-2">
                        <td class="text-left border-r-2 p-1"><?php echo $idProd.' - '.$prod['libelle']?></td>
                        <td class="border-r-2 p-1"><?php echo $prod['prixHT']?></td>
                        <td class="border-r-2 p-1"><?php echo $prod['prixTTC']?></td>
                        <td class="border-r-2 p-1"><?php echo ($prod['pourcentageRemise'] === null)?'0%': $prod['pourcentageRemise'].'%' ?></td>
                        <td class="border-r-2 p-1"><?php echo $prod['quantite']?></td>
                        <td class="border-r-2 p-1"><?php echo $prod['sousTotal']?></td>
                    </tr>
                <?php }?>
            </tbody>
        </table>
        <p class="border-t-2 font-bold w-1/1">SOUS TOTAL = <?php echo $row['montantFacture']?></p>
        </div>
    <?php }?>
</body>
</html>