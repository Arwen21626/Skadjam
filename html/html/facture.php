<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../php/verif_role_bo.php";
require(__DIR__ . '/../../01_premiere_connexion.php');

$idCommande = $_GET['idCommande'];

// récupération de tous les produits de la commande
$donnees = [];
foreach($dbh->query("SELECT 
                c.id_commande, c.date_commande, p.libelle_produit, 
                p.id_produit, p.id_vendeur, v.raison_sociale, d.quantite, 
                p.prix_ht, p.prix_ttc, p.prix_remise, c.montant_total_ttc,
                d.sous_total, f.montant_ht, 
                FROM sae3_skadjam._commande c
                INNER JOIN sae3_skadjam._details d
                    ON d.id_commande = c.id_commande
                INNER JOIN sae3_skadjam._produit p
                    ON p.id_produit = d.id_produit
                INNER JOIN sae3_skadjam._vendeur v
                    ON v.id_compte = p.id_vendeur
                INNER JOIN sae3_skadjam._facture f
                    ON f.numero_facture = c.id_facture
                INNER JOIN sae3_skadjam._habire h
                    ON h.numero_facture = c.id_facture
                INNER JOIN sae3_skadjam._adresse a
                    ON f.numero_facture = c.id_facture
                INNER JOIN sae3_skadjam._adresse_livraison f
                    ON f.numero_facture = c.id_facture
                WHERE c.id_commande = :id_commande", 
            PDO::FETCH_ASSOC) as $row){
    $infoVendeur[$row['id_vendeur']]['raisonSociale'] = $row['raison_sociale'];
    $infoVendeur[$row['id_vendeur']]['adresse_mail'] = $row['adresse_mail'];
    $infoVendeur[$row['id_vendeur']]['adresse'] = $row['numero_rue'].' rue '.$row['adresse_postale'].' '.$row['ville'].', '.$row['code_postal'].' batiment : '.$row['numero_bat'];
    //$infoClient[$row['id_client']]['adresse'] = $row['numero_rue'].' rue '.$row['adresse_postale'].' '.$row['ville'].', '.$row['code_postal'];
    $donnees[$row['id_vendeur']][] = [];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="/css/print.css" media="print" rel="stylesheet" />
</head>
<body>
    <?php foreach ($donnees as $vend => $produit){?>
        <h1> Facture n°<?php echo $numeroCommande?></h1>
    
        <section>
            <h2>Emmeteur</h2>
            <p><?php echo $vend['raison_social']?></p>
            <p><?php echo $mailEmmeteur?></p>
            <p><?php echo $adrEmmeteur?></p>
        </section>
        <section>
            <h2>Destinataire</h2>
            <p><?php echo $nomDestinataire?></p>
            <p><?php echo $mailDestinataire?></p>
            <p><?php echo $adrDestinataire?></p>
        </section>
        <section> 
            <p><?php echo $numeroCommande?></p>
            <p><?php echo $dateCommande?></p>
            <p>payment immédiat</p>
        </section>
        <table>
            <thead>
                <th>article</th>
                <th>prix unitaire HT</th>
                <th>prix unitaire TTC</th>
                <th>pourcentage de remise</th>
                <th>quantite</th>
                <th>total</th>
            </thead>
            <tbody>
                <?php foreach($produits as $prod){?>
                    <tr>
                        <td><?php echo $prod['id_produit'].' - '.$prod['libelle_produit']?></td>
                        <td><?php echo $prod['prix_ht']?></td>
                        <td><?php echo $prod['prix_ttc']?></td>
                        <td><?php echo $prod['pourcentage_remise']?></td>
                        <td><?php echo $prod['quantite']?></td>
                        <td><?php echo $prod['prix_remise']*$prod['quantite']?></td>
                    </tr>
                    <tr><td> sous total</td></tr>
                <?php }?>
            </tbody>
        </table>
    <?php }?>
</body>
</html>