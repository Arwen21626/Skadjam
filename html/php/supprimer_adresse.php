<?php
session_start();
require_once __DIR__ . "/verif_role_fo.php";
require_once __DIR__ . "/../01_premiere_connexion.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["idCompte"])) {
    header("Location: " . __DIR__ . "/../../html/fo/connexion.php");
    exit();
}

$idCompte = $_SESSION['idCompte'];
$idAdresse = $_GET['idAdresse'] ?? null;

if ($idAdresse !== null) {
    // Connexion BDD
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Vérifier que l'adresse appartient bien au compte
    $stmt = $dbh->prepare("SELECT * FROM sae3_skadjam._habite WHERE id_compte = ? AND id_adresse = ?");
    $stmt->execute([$idCompte, $idAdresse]);
    $historique = $stmt->fetch();

    if (!empty($historique)) {
        // Supprimer l'entrée de l'historique
        $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._habite WHERE id_compte = ? AND id_adresse = ?");
        $stmt->execute([$idCompte, $idAdresse]);

        // Supprimer l'adresse
        $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._adresse WHERE id_adresse = ?");
        $stmt->execute([$idAdresse]);
    }
}

// Rediriger vers la page de modification du compte client
header("Location: ../html/fo/modifier_compte_client.php");
exit;

?>