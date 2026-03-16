<?php
require_once(__DIR__ . "/../01_premiere_connexion.php");

session_start();


if ($_SESSION['role'] === 'client') 
{
    // Récupère l'id du compte
    $idClient = $_SESSION["idCompte"];

    // Récupère pour quelle raison on vide le panier | 
    // --- normal -> l'utilisateur a cliqué sur le bouton "Vider le panier" de son panier -> redirection sur la page panier
    // --- achat -> l'utilisateur a réalisé le processus d'achat des produits contenus dans son panier et le processus a abouti -> on vide le panier et on redirige vers la suite

    $achatValide;
    $typeVider = $_GET["typeVider"];

    if (isset($_GET["achatValide"])) {
        $achatValide = $_GET["achatValide"];
    }


    // Récupère l'id du panier du client

    $rqt = $dbh->query("SELECT id_panier FROM sae3_skadjam._panier WHERE id_client = $idClient", PDO::FETCH_ASSOC);
    $idPanier = $rqt->fetch()["id_panier"];

    // Suppression des produits du panier dans la table _contient

    $rqt = $dbh->prepare("
        DELETE FROM sae3_skadjam._contient
        WHERE id_panier = :idPanier
    ");

    

    

    
    // Redirection différente selon pourquoi on vide le panier -> façon normal ou lors de l'achat
    if ($typeVider === "normal") 
    {
        header("location:/html/fo/panier.php?panierModif=V");
    }
    else if ($typeVider === "achat")
    {
        $stmt = $dbh->prepare("SELECT id_panier FROM sae3_skadjam._panier WHERE id_client = ?");
        $stmt->execute([$_SESSION['idCompte']]);
        $idPanierAchete = $stmt->fetch(PDO::FETCH_ASSOC)['id_panier'];

        $stmt = $dbh->prepare("SELECT id_produit, quantite_par_produit, quantite_stock FROM sae3_skadjam._contient NATURAL JOIN sae3_skadjam._produit WHERE id_panier = ?");
        $stmt->execute([$idPanierAchete]);
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        //màj base de données
        $updateStock = $dbh->prepare("
            UPDATE sae3_skadjam._produit
            SET quantite_stock = :qte
            WHERE id_produit = :id
        ");

        foreach ($res as $ligne) {            
            $updateStock->execute([
                ':qte' => $ligne['quantite_stock']-$ligne['quantite_par_produit'],
                ':id'  => $ligne['id_produit']
            ]);
        }

        header("location:/html/fo/paiement.php?achatValide=" . $achatValide);
    }

    $rqt->execute([
        ':idPanier' => $idPanier
    ]);

    // Mise à 0 de la table panier
    $dbh->query("UPDATE sae3_skadjam._panier
                 SET nb_produit_total = 0, montant_total_ttc = 0
                 WHERE id_panier = $idPanier");
}
else if ($_SESSION['role'] === 'visiteur')
{
    $_SESSION['panier']['nb_produit_total'] = 0;
    $_SESSION['panier']['montant_total_ttc'] = 0;
    $_SESSION['panier']['contient'] = [];

    header("location:/html/fo/panier.php?panierModif=V");
}


?>