<?php
$tabPanier = [];
// Réupération de l'id des futurs achats du client
if ($_SESSION['role'] == "client") {
    $idCompte = $_SESSION['idCompte'];
    // Parcours des produits lié au client
    foreach ($dbh->query("SELECT c.id_produit 
            FROM sae3_skadjam._panier pa
                INNER JOIN  sae3_skadjam._contient c
                    ON c.id_panier = pa.id_panier
            WHERE pa.id_client = $idCompte", PDO::FETCH_ASSOC) as $row) {
        $tabPanier[] = $row;
    }    
}
else{
    $tabPanier = $_SESSION['panier'];
}
?>