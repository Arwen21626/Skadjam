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
$id_vendeur_anonyme = 42;

try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    //Modifier id_compte de la réponse vendeur pour celui du compte anonyme
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._reponse SET id_compte = :id_anonyme WHERE id_compte = :id");
    $stmt->execute([':id_anonyme' => $id_vendeur_anonyme,
                    ':id' => $id]);

    // Modifier id_vendeur du produit pour celui du compte anonyme 
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._produit SET id_vendeur = :id_anonyme WHERE id_vendeur = :id");
    $stmt->execute([':id_anonyme' => $id_vendeur_anonyme,
                    ':id' => $id]);

    //Récupérer l'id_adresse pour supprimer l'adresse du compte vendeur
    $stmt = $dbh->prepare("SELECT id_adressse 
                            FROM sae3_skadjam._adresse a
                            INNER JOIN sae3_skadjam._habite h
                                ON h.id_adresse = a.id_adresse
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);
    $idAdresse = $stmt->fetchAll();

    //Suppresion du n-uplet dans _habite
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._habite
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);

    //Suppression de l'adresse
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._adresse
                            WHERE is_adresse = :id_adresse");
    $stmt->execute([':id_adresse' => $idAdresse]);

    //Suppression du vendeur (table vendeur)
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._vendeur
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);


    //Suppresion du compte (table compte)
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._compte
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);

    // Supprime les informations de session
    session_unset();
    session_destroy();

    // Redirection vers la page d'accueil
    header("Location: ../../index_vendeur.php");
    exit();
} 
catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>