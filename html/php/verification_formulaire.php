<?php

function verifNomPrenom($nom){
    if (strlen($nom) > 100 || !preg_match("/^[A-Za-z -]+$/", $nom)){
        return false;
    }
    else{
        return true;
    }
}

function verifTelephone($tel){
    if (!preg_match("/^\+33[0-9]{9}$/", $tel)){
        return false;
    }
    else{
        return true;
    }
}

function verifMail($mail){
    if (strlen($mail) > 150 || !preg_match("/^[A-Za-z0-9.]+@[A-Za-z]+.[A-Za-z]+$/", $mail)){
        return false;
    }
    else{
        return true;
    }
}

function verifMotDePasse($mdp){
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-@_#$.£!?%*+:;,&~|^])[^\s<>]{10,}$/", $mdp)){
        return false;
    }
    else{
        return true;
    }
}

//modification
include("../01_premiere_connexion.php");

function confirmationMotDePasse($mdp, $confMdp){
    return $mdp === $confMdp;
}

function mailUnique($mail){
    global $dbh;
    $stmt = $dbh->prepare("SELECT adresse_mail FROM sae3_skadjam._compte WHERE adresse_mail = ?");
    $stmt->execute([$mail]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if($row){
        return false;
    }else{
        return true;
    }
}

function verifDenomination($denomination){
    return preg_match("/^[a-zA-Z- 0-9]{1,}$/", $denomination);
}

function verifSiren($siren){
    if (000000000<=$siren && $siren<=999999999){
        $explodeSiren = str_split($siren);
        $explodeSiren[1] *= 2;
        $explodeSiren[3] *= 2;
        $explodeSiren[5] *= 2;
        $explodeSiren[7] *= 2;
        foreach ($explodeSiren as &$number){
            if ($number > 9){
                $number = $number - 9;
            }
        }
        $somme = array_sum($explodeSiren);
        if ($somme%10 === 0){
            return true;
        }else{
            return false;
        }
    }else{
        return false;
    }
}

function verifIban($iban){
    if (preg_match("/^FR[0-9]{25}$/", $iban)){
        return true;
    }else{
        return false;
    }
}

function verifPrix($prix){
    if ($prix >= 0){
        return true;
    }
    else{
        return false;
    }
}

function verifQteStock($qteStock){
    if ($qteStock >= 0){
        return true;
    }
    else{
        return false;
    }
}


