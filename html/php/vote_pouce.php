<?php
require_once(__DIR__ . "/../01_premiere_connexion.php");
session_start();
$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

header('Content-Type: application/json');

$idCompte = $_SESSION['idCompte'] ?? null;
$id_avis = $_POST['id'] ?? null;
$type = isset($_POST['type']) ? (int)$_POST['type'] : null;
$voteSupprime = false;

if(!$idCompte || !$id_avis || !in_array($type, [1,-1])){
    echo json_encode(["error"=>"Paramètre invalide"]); exit;
}

// Vérifier vote existant
$stmt = $dbh->prepare("SELECT pouce FROM sae3_skadjam._pouces WHERE id_avis=? AND id_compte=?");
$stmt->execute([$id_avis, $idCompte]);
$voteExistant = $stmt->fetch(PDO::FETCH_ASSOC);

if($voteExistant){
    if($voteExistant['pouce'] == $type){
        // même vote cliqué → supprimer le vote
        $dbh->prepare("DELETE FROM sae3_skadjam._pouces WHERE id_avis=? AND id_compte=?")
            ->execute([$id_avis, $idCompte]);

        $voteSupprime = true;

        if($type === 1){
            $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_haut = nb_pouce_haut - 1 WHERE id_avis=?")
                ->execute([$id_avis]);
        } else {
            $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_bas = nb_pouce_bas - 1 WHERE id_avis=?")
                ->execute([$id_avis]);
        }
    }

    else {
        // vote différent → changer le vote
        $dbh->prepare("UPDATE sae3_skadjam._pouces SET pouce=? WHERE id_avis=? AND id_compte=?")->execute([$type,$id_avis,$idCompte]);

        if($type === 1){
            $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_haut=nb_pouce_haut+1, nb_pouce_bas=nb_pouce_bas-1 WHERE id_avis=?")->execute([$id_avis]);
        } else {
            $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_bas=nb_pouce_bas+1, nb_pouce_haut=nb_pouce_haut-1 WHERE id_avis=?")->execute([$id_avis]);
        }
    }
} 
else {
    // nouveau vote
    $dbh->prepare("INSERT INTO sae3_skadjam._pouces (id_avis,id_compte,pouce) VALUES (?,?,?)")->execute([$id_avis,$idCompte,$type]);

    if($type === 1){
        $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_haut=nb_pouce_haut+1 WHERE id_avis=?")->execute([$id_avis]);
    } else {
        $dbh->prepare("UPDATE sae3_skadjam._avis SET nb_pouce_bas=nb_pouce_bas+1 WHERE id_avis=?")->execute([$id_avis]);
    }
}

// renvoyer compteurs à jour
$stmt = $dbh->prepare("SELECT nb_pouce_haut, nb_pouce_bas FROM sae3_skadjam._avis WHERE id_avis=?");
$stmt->execute([$id_avis]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

// récupérer le vote utilisateur
$stmt = $dbh->prepare("SELECT pouce FROM sae3_skadjam._pouces WHERE id_avis=? AND id_compte=?");
$stmt->execute([$id_avis, $idCompte]);
$res = $stmt->fetch(PDO::FETCH_ASSOC);

$pouceUtilisateur = $res ? (int)$res['pouce'] : null;

echo json_encode([
    "likes"=>$data['nb_pouce_haut'],
    "dislikes"=>$data['nb_pouce_bas'],
    "pouce_utilisateur"=>$pouceUtilisateur
]);