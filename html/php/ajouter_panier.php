<?php
include_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

$idClient = $_SESSION["idCompte"];

foreach($dbh->query("   SELECT *
                        FROM sae3_skadjam._panier pan
                        WHERE pan.id_client = $idClient"
                        , PDO::FETCH_ASSOC) as $row){
        $infoPanier = $row;
}

$idProd = $_POST["idProduit"];

$idPanier = $infoPanier["id_panier"];




// foreach ($dbh->query("SELECT id_produit, id_panier, quantite_par_produit
//                       FROM sae3_skadjam._contient con
//                       WHERE con.id_panier = $idPanier AND con.id_produit = $idProd", PDO::FETCH_ASSOC) as $row) {
//     $isInTable = $row;
// }

$stmt = $dbh->prepare("
    SELECT id_produit, id_panier, quantite_par_produit
    FROM sae3_skadjam._contient
    WHERE id_panier = :id_panier AND id_produit = :id_produit
");

$stmt->execute([
    ':id_panier' => $idPanier,
    ':id_produit' => $idProd
]);

$estDansPanier = $stmt->fetch(PDO::FETCH_ASSOC); // soit array, soit false

if (!$estDansPanier) 
{
    $dbh->query("INSERT INTO sae3_skadjam._contient
             (id_produit, id_panier, quantite_par_produit)
             VALUES 
             ($idProd, $idPanier, 1)");
}
else
{
    $quantiteProd = $estDansPanier["quantite_par_produit"];
    $quantiteProd++;

    $maj = $dbh->prepare("
        UPDATE sae3_skadjam._contient SET
        quantite_par_produit = :quantite_par_produit
        WHERE id_panier = :id_panier AND id_produit = :id_produit
    ");

    $maj->execute([
        ':quantite_par_produit' => $quantiteProd,
        ':id_panier' => $idPanier,
        ':id_produit' => $idProd
    ]);
}








header("location:/html/fo/details_produit.php?idProduit=" . $idProd);
?>