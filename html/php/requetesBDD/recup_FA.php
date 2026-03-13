<?php
$tabFA = [];
// Réupération de l'id des futurs achats du client
if ($_SESSION['role'] == "client") {
    $idCompte = $_SESSION['idCompte'];
    // Parcours des produits lié au client
    foreach ($dbh->query("SELECT id_produit FROM sae3_skadjam._futur_achat WHERE id_client = $idCompte", PDO::FETCH_ASSOC) as $row) {
        $tabFA[$row['id_produit']] = $row['id_produit'];
    }    
}
else{
    $tabFA = $_SESSION['futurAchat'];
}
?>