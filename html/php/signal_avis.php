<?php
session_start();
require_once(__DIR__ . "/../01_premiere_connexion.php");

header('Content-Type: application/json');

if (!isset($_POST['idAvis'])) {
    echo json_encode(["error" => "ID manquant"]);
    exit;
}

$idAvis = $_POST['idAvis'];
$idCompte = $_SESSION['idCompte'] ?? null;

if (!$idCompte) {
    echo json_encode(["error" => "Non connecté"]);
    exit;
}

// Vérifier si l'avis existe
$stmtAvis = $dbh->prepare("SELECT id_avis FROM sae3_skadjam._avis WHERE id_avis = ?");
$stmtAvis->execute([$idAvis]);
if (!$stmtAvis->fetch()) {
    echo json_encode(["error" => "Avis inexistant"]);
    exit;
}

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['idCompte']) || $_SESSION['role'] === 'visiteur') {
    $response['error'] = "Vous devez être connecté pour signaler un avis.";
    echo json_encode($response);
    exit;
}

// Mettre à jour l'avis
$updateAvis = $dbh->prepare("UPDATE sae3_skadjam._avis SET signaler = true WHERE id_avis = ?");
$updateAvis->execute([$idAvis]);


// Vérifier si le signalement existe déjà
$stmtCheck = $dbh->prepare("SELECT 1 FROM sae3_skadjam._a_signaler WHERE id_avis = ? AND id_compte = ?");
$stmtCheck->execute([$idAvis, $idCompte]);

if (!$stmtCheck->fetch()) {
    $insert = $dbh->prepare("INSERT INTO sae3_skadjam._a_signaler (id_avis, id_compte) VALUES (?, ?)");
    $insert->execute([$idAvis, $idCompte]);
}

echo json_encode([
    "success" => true,
    "idAvis" => $idAvis
]);