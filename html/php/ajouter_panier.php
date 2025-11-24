<?php
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();

// Récupère l'id compte et l'id produit
$idClient = $_SESSION["idCompte"];
$idProd = $_POST["idProduit"];

// Récupère les infos du panier du client
foreach($dbh->query("   SELECT *        
                        FROM sae3_skadjam._panier pan
                        WHERE pan.id_client = $idClient"
                        , PDO::FETCH_ASSOC) as $row){
        $infoPanier = $row;
}

$idPanier = $infoPanier["id_panier"]; //Récupère l'id du panier
$nbProduitsTotal = $infoPanier["nb_produit_total"]; // Récupère le nombre de produits dans le panier
$montantTotalTTC = $infoPanier["montant_total_ttc"]; // Récupère le montant total du panier

// Requête pour récupérer les infos du produit à ajouter au panier et vérifie sa présence dans la BDD
$rqt = $dbh->query("SELECT * FROM sae3_skadjam._produit WHERE id_produit = $idProd", PDO::FETCH_ASSOC);

$infoProduit = $rqt->fetch();

if (!$infoProduit) // Si le produit n'existe pas dans la BDD 
{
    header("location:/404.php");
}
else
{
    //Récupération du prix_ttc du produit
    $prixTTC = $infoProduit["prix_ttc"];
    
    // Traitement et mise à jour des infos générales du panier
    $nbProduitsTotal++;
    $montantTotalTTC += $prixTTC;
    
    $maj = $dbh->prepare("
        UPDATE sae3_skadjam._panier SET
        nb_produit_total = :nbProduits,
        montant_total_ttc = :montantTot
        WHERE id_panier = :idPanier;
    ");

    $maj->execute([
        ':nbProduits' => $nbProduitsTotal,
        ':montantTot' => $montantTotalTTC,
        ':idPanier' => $idPanier
    ]);

    // Requête pour récupèrer le produit dans la table _contient qui lie le produit au panier du client
    $rqt = $dbh->prepare("
        SELECT id_produit, id_panier, quantite_par_produit
        FROM sae3_skadjam._contient
        WHERE id_panier = :id_panier AND id_produit = :id_produit
    ");

    $rqt->execute([
        ':id_panier' => $idPanier,
        ':id_produit' => $idProd
    ]);

    $estDansPanier = $rqt->fetch(PDO::FETCH_ASSOC); // soit array, soit false

    if (!$estDansPanier) // Si la requête précédente renvoie false, le produit n'est pas dans le panier, on ajoute le produit dans la table _contient
    {
        $dbh->query("INSERT INTO sae3_skadjam._contient
                (id_produit, id_panier, quantite_par_produit)
                VALUES 
                ($idProd, $idPanier, 1)");
    }
    else // Si la requête renvoie une array, on modifie la quantité du produit présent dans la panier
    {
        $quantiteProd = $estDansPanier["quantite_par_produit"]; // Récupère la quantité actuelle du produit puis l'incrémente
        $quantiteProd++;

        // Met à jour la quantité du produit présent dans le panier du client
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



    if ($_POST["pageDeRetour"] === "details") 
    {
        header("location:/html/fo/details_produit.php?idProduit=" . $idProd);
    }
    else if ($_POST["pageDeRetour"] === "panier")
    {
        header("location:/html/fo/panier.php#" . $idProd);
    }
    
}


?>

<pre>
    <?php print_r($_POST); ?>
</pre>