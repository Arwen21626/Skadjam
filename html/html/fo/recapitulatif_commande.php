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
$sql = "SELECT 
            pr.libelle_produit,
            v.raison_sociale,
            c.quantite_par_produit,
            pr.prix_ht,
            pr.prix_ttc,
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

try{

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

catch (Exception $e){
    echo "Erreur : " . $e->getMessage();
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
        <h2>Récapitulatif de votre commande</h2>
        <h3>Numéro de la commande :</h3>
        <p></p>
        <h3>Date :</h3>
        <p></p>
        <table>
            <tr>
                <th>Article</th>
                <th>Référence</th>
                <th>Quantité</th>
                <th>Prix unitaire HT</th>
                <th>Prix unitaire TTC</th>
                <th>Prix remisé</th>
            </tr>
            <tr>

            </tr>
        </table>
        <form action=""></form>
    </main>

    <!--footer-->
    <?php include (__DIR__ . "/../../php/structure/footer_front.php"); ?>
</body>
</html>