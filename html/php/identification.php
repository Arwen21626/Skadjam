<?php

function verif_id($idCompte, $password){
    require __DIR__.'/../01_premiere_connexion.php';
    $stmt = $dbh->prepare("SELECT mot_de_passe FROM sae3_skadjam._compte WHERE id_compte = ?");
    $stmt->execute([$idCompte]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $hashPass = $data['mot_de_passe'];
    $passValide = password_verify($password, $hashPass);
    return $passValide;
}

