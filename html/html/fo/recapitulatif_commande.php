<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require(__DIR__ . '/../../01_premiere_connexion.php');

// === Vérification utilisateur et panier ===
if (empty($_SESSION['idCompte'])) {
    die("Erreur : utilisateur non connecté");
}

$idCompte = (int)$_SESSION['idCompte'];
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

// === Transaction ===
try {
    $dbh->beginTransaction();

    $date = date("Y-m-d");

    // === Insertion de la commande ===
    $sqlCommande = "
        INSERT INTO sae3_skadjam._commande
        (etat, date_commande, montant_total_ttc, id_client)
        VALUES (:etat, :date_commande, :montant_total_ttc, :id_client)
        RETURNING id_commande
    ";

    $stmtCommande = $dbh->prepare($sqlCommande);
    $stmtCommande->execute([
    ':etat' => 'Attente de validation',
    ':date_commande' => $date,
    ':montant_total_ttc' => $tabInfosPanier[0]['montant_total_ttc'],
    ':id_client' => $idCompte
    ]);

    $idCommande = $dbh->lastInsertId('sae3_skadjam._commande_id_commande_seq');
    var_dump($idCommande);
    die;
    //$idCommande = $stmtCommande->fetchColumn();
    if (!$idCommande) {
        throw new Exception("Erreur : id_commande non généré");
    }

    // === Calcul total HT ===
    $montant_total_ht = 0;
    foreach ($tabInfosPanier as $infoPanier) {
        $montant_total_ht += $infoPanier['sous_total_ht'];
    }

    // === Insertion de la facture ===
    $sqlFacture = "
        INSERT INTO sae3_skadjam._facture
        (montant_ht, destinataire, date_commande, id_commande)
        VALUES (:montant_ht, :destinataire, :date_commande, :id_commande)
        RETURNING numero_facture
    ";

    $stmtFacture = $dbh->prepare($sqlFacture);
    $stmtFacture->execute([
        ':montant_ht' => $montant_total_ht,
        ':destinataire' => $idCompte,
        ':date_commande' => $date,
        ':id_commande' => $idCommande
    ]);

    $numeroFacture = $stmtFacture->fetchColumn();
    if (!$numeroFacture) {
        throw new Exception("Erreur : numéro_facture non généré");
    }

    // === Mise à jour de la commande avec l'id_facture ===
    $sqlUpdate = "
        UPDATE sae3_skadjam._commande
        SET id_facture = :id_facture
        WHERE id_commande = :id_commande
    ";

    $stmtUpdate = $dbh->prepare($sqlUpdate);
    $stmtUpdate->execute([
        ':id_facture' => $numeroFacture,
        ':id_commande' => $idCommande
    ]);

    // === Commit ===
    $dbh->commit();

    echo "Commande et facture créées avec succès !\n";
    echo "id_commande = $idCommande\n";
    echo "numero_facture = $numeroFacture\n";

} catch (Exception $e) {
    $dbh->rollBack();
    echo "Erreur : " . $e->getMessage();
}
?>
