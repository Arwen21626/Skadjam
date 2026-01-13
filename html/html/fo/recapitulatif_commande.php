<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require(__DIR__ . '/../../01_premiere_connexion.php');

// === Vérification utilisateur et panier ===
if (empty($_SESSION['idCompte'])) {
    die("Erreur : utilisateur non connecté");
}

$idCompte = $_SESSION['idCompte'];
//$idPanier = $_SESSION['idPanier'];
$idPanier = 1;

// === Récupération du panier ===
try{ $sql = "SELECT 
            pr.libelle_produit,
            pr.id_produit,
            pr.id_vendeur,
            v.raison_sociale,
            c.quantite_par_produit,
            pr.prix_ht,
            pr.prix_ttc,
            pr.prix_remise,
            pr.prix_ttc * c.quantite_par_produit as sous_total_ttc,
            pr.prix_ht * c.quantite_par_produit as sous_total_ht,
            p.montant_total_ttc,
            p.nb_produit_total
        FROM sae3_skadjam._panier p
        INNER JOIN sae3_skadjam._contient c
            ON c.id_panier = p.id_panier
        INNER JOIN sae3_skadjam._produit pr
            ON pr.id_produit = c.id_produit
        INNER JOIN sae3_skadjam._vendeur v
            ON v.id_compte = pr.id_vendeur
        WHERE p.id_panier = :id_panier
    ";

    $stmt = $dbh->prepare($sql);
    $stmt->execute([':id_panier' => $idPanier]);
    $tabInfosPanier = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($tabInfosPanier)) {
        die("Erreur : panier vide");
    }

    //Déclaration variables : 
    $quantite_totale = $tabInfosPanier[0]['nb_produit_total'];
    $total_ht = 0;
    $total_ttc = $tabInfosPanier[0]['montant_total_ttc'];
    $total_remise = 0;
}
catch (Exception $e){
    echo "Erreur : " . $e->getMessage();
}

//création commande, détails et facture si cgv cochées et btn valider appuyé
if(isset($_POST['case'])){
    $date_char = date("d/m/Y");
    //Insertion de la commande
    $sqlCommande = "INSERT INTO sae3_skadjam._commande (etat, date_commande, montant_total_ttc, id_client)
                    VALUES (:etat, :date_commande, :montant_total_ttc, :id_client)
                    RETURNING id_commande";


    $stmtCommande = $dbh->prepare($sqlCommande);

    $stmtCommande->execute([
        ':etat' => 'En attente',
        ':date_commande' => $date_char,
        ':montant_total_ttc' => $tabInfosPanier[0]['montant_total_ttc'],
        ':id_client' => $idCompte
    ]);

    $idCommande = $stmtCommande->fetchColumn();

    if (!$idCommande) {
        throw new Exception("id_commande non récupéré");
    }

    //Insertion dans la table donne (lien entre panier et commande)
    $sqlDonne = "INSERT INTO sae3_skadjam._donne (id_panier, id_commande)
                VALUES (:id_panier, :id_commande)";


    $stmtDonne = $dbh->prepare($sqlDonne);

    $stmtDonne->execute([
        ':id_panier' => $idPanier,
        ':id_commande' => $idCommande
    ]);
}
    
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Récapitulatif de votre commande</title>
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'?>
<body>
    <!--header-->
    <?php include __DIR__ . "/../../php/structure/header_front.php"; ?>
    <?php include __DIR__ . "/../../php/structure/navbar_front.php"; ?>

    <main class="min-h-[600px]">
        <!--<h2>Récapitulatif de votre commande</h2>
        <h3>Numéro de la commande :</h3>
        <p></p>-->
        <h3>Date :</h3>
        <p><?php echo date("d/m/Y");?></p>

        <div class="flex justify-center">
            <table class="table-auto w-280">
                <thead>
                    <tr>
                        <th class="text-left w-110 pl-3"><h4>Article</h4></th>
                        <th><h4>Référence</h4></th>
                        <th><h4>Quantité</h4></th>
                        <th><h4>Prix unitaire HT</h4></th>
                        <th><h4>Prix unitaire TTC</h4></th>
                        <th><h4>Prix remisé</h4></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $impair = 0;
                    foreach($tabInfosPanier as $ligne){ 
                        $impair ++;
                        if(fmod($impair, 2) == 0){
                            $classe = "py-4";
                        }
                        else{
                            $classe = "py-4 bg-bleu";
                        }?>
                        <tr class="<?php echo $classe; ?>">
                            <td class="text-left py-3 pl-3"><p><?php echo $ligne['libelle_produit'];?></p></td>
                            <td class="text-center py-3"><p><?php echo $ligne['id_produit'];?></p></td>
                            <td class="text-center py-3"><p><?php echo $ligne['quantite_par_produit'];?></p></td>
                            <td class="text-center py-3"><p><?php echo $ligne['prix_ht'];?></p></td>
                            <td class="text-center py-3"><p><?php echo $ligne['prix_ttc'];?></p></td>
                            <td class="text-center py-3"><p><?php echo $ligne['prix_remise'];?></p></td>
                            <?php $total_ht = $total_ht + $ligne['sous_total_ht'];
                            $total_remise = $total_remise + $ligne['prix_remise'];?>
                        </tr>
                    <?php } ?>
                </tbody>
                <tfoot>
                    <td><p>Total :</p></td>
                    <td><p><?php echo $quantite_totale;?></p></td>
                    <td><p><?php echo $total_ht;?></p></td>
                    <td><p><?php echo $total_ttc;?></p></td>
                    <td><p><?php echo $total_remise;?></p></td>
                </tfoot>
            </table>
        </div>
        <div class="flex justify-center">
            <form action="recapitulatif_commande.php" action="POST">
                <a href="cgv_fo.php">J’ai lu et j’accepte les conditions générales de vente : </a>
                <input type="checkbox" name="case" id="case">
                <a href="../fo/panier.php?idPanier=<?php echo $idPanier ;?>" class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5">Annuler</a>
                <input class="flex justify-center items-center border-2 border-vertClair rounded-2xl w-40 h-14 cursor-pointer my-5" type="submit" name="valider" value="Valider">
            </form>
        </div>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>