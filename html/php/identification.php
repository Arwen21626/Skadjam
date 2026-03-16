<?php

function verif_id($password, $idCompte){
    require __DIR__.'/../01_premiere_connexion.php';
    $passValide = false;
    $code = false;
    
    $stmt = $dbh->prepare("SELECT mot_de_passe, code_secret FROM sae3_skadjam._compte WHERE id_compte = ?");
    $stmt->execute([$idCompte]);
        
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $count = count($data);

    if ($count>0){
        $hashPass = $data['mot_de_passe'];
        $passValide = password_verify($password, $hashPass);
        if (!is_null($data['code_secret'])){
            $code = true;
        }
    }
    return ["pass"=>$passValide, "code"=>$code];
}


