<?php
session_start();
require_once __DIR__ . "/../01_premiere_connexion.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["idCompte"])) {
    header("Location: " . __DIR__ . "/../../html/fo/connexion.php");
    exit();
}

// Récupère l'ID du compte à supprimer
$id = (int) $_SESSION["idCompte"];

try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Copier les avis dans le compte anonyme
    $stmt = $dbh->prepare("SELECT * FROM sae3_skadjam._avis WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $avis = $stmt->fetchAll();

    foreach ($avis as $a) {
        $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._avis (nb_etoile,nb_pouce_haut,nb_pouce_bas,contenu_commentaire,id_produit,id_compte) VALUES (:nb_etoile,:nb_pouce_haut,:nb_pouce_bas,:contenu_commentaire,:id_produit, 41)");
        $stmt->bindParam(':id_produit', $a['id_produit'], PDO::PARAM_INT);
        $stmt->bindParam(':nb_etoile', $a['nb_etoile'], PDO::PARAM_INT);
        $stmt->bindParam(':nb_pouce_haut', $a['nb_pouce_haut'], PDO::PARAM_INT);
        $stmt->bindParam(':nb_pouce_bas', $a['nb_pouce_bas'], PDO::PARAM_INT);
        $stmt->bindParam(':contenu_commentaire', $a['contenu_commentaire'], PDO::PARAM_STR);
        $stmt->execute();
    }

    // Supprime le compte du client
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._avis WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._habite WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._futur_achat WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._carte_bancaire WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._panier WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._client WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._compte WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Supprime les informations de session
    session_unset();
    session_destroy();

    // Redirection vers la page d'accueil
    header("Location: ../../index.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>