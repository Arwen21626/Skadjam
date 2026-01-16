<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="/css/print.css" media="print" rel="stylesheet" />
</head>
<body>
    <h1> Facture n°<?php echo $numeroCommande?></h1>
    <section>
        <h2>Emmeteur</h2>
        <p><?php echo $nomEmmeteur?></p>
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
            <th>prix HT</th>
            <th>prix TTC</th>
            <th>pourcentage de remise</th>
            <th>quantite</th>
            <th>total</th>
        </thead>
        <tbody>
            <?php foreach($produits as $row){?>
                <tr>
                    <td><?php echo $row['id_produit'].' - '.$row['libelle_produit']?></td>
                    <td><?php echo $row['prix_ht']?></td>
                    <td><?php echo $row['prix_ttc']?></td>
                </tr>
            <?php }?>
        </tbody>
    </table>
</body>
</html>