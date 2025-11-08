<?php
include("../01_premiere_connexion.php");

function verifNomPrenom($nom){
    // Vérification que soit un prénom soit un nom soyent au bon format
    if (strlen($nom) > 100 || !preg_match("/^[A-Za-z -]+$/", $nom)){
        return false;
    }
    else{
        return true;
    }
}

function verifTelephone($tel){
    // Vérification que le numéro de téléphone soit au bon format
    if (!preg_match("/^0[0-9]{9}$/", $tel)){
        return false;
    }
    else{
        return true;
    }
}

function verifMail($mail){
    // Vérification que la format de l'email soit correcte
    if (strlen($mail) > 150 || !preg_match("/^[A-Za-z0-9.]+@[A-Za-z]+.[A-Za-z]+$/", $mail)){
        return false;
    }
    else{
        return true;
    }
}

function verifMotDePasse($mdp){
    // Vérification que le mot de passe à le bon format
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-@_#$.£!?%*+:;,&~|^])[^\s<>]{10,}$/", $mdp)){
        return false;
    }
    else{
        return true;
    }
}

function confirmationMotDePasse($mdp, $confMdp){
    // Vérification que le mot de passe correspond au mot de passe de vérification
    return $mdp === $confMdp;
}

function mailUnique($mail){
    // Vérification qu'un mail est unique
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
    // Vérification d'une dénomination
    return preg_match("/^[a-zA-Z- 0-9]{1,}$/", $denomination);
}

function verifSiren($siren){
    // Vérification d'un numéro de SIREN
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
    // Vérification d'un iban
    if (preg_match("/^FR[0-9]{25}$/", $iban)){
        return true;
    }else{
        return false;
    }
}

function verifPrix($prix){
    // Vérification d'un prix
    if ($prix >= 0){
        return true;
    }
    else{
        return false;
    }
}

function verifQteStock($qteStock){
    // Vérification d'une quantite en stock
    if ($qteStock >= 0){
        return true;
    }
    else{
        return false;
    }
}

function verifPseudo($pseudo){
    // Vérification du pseudo
    if (strlen($pseudo) > 30 || !preg_match("/^[0-9A-Za-z_ -]+$/", $pseudo)){
        return false;
    }
    else{
        return true;
    }
}

function verifDate($date){
    // Vérification du format des dates
    // date entrer dans le format aaaa-mm-jj
    if (!preg_match("/^[0-9]{4}-(0[0-9]|1[0-2])-([0-2][0-9]|3[01])$/", $date)){
        return false;
    }
    else{
        return true;
    }
}

function verifAge($naissance){
    // Vérification que la personne a plus de 18 ans
    // date entrer dans le format aaaa-mm-jj
    if (verifDate($naissance)){
        $dateNaissance = explode('-', $naissance);
        $dateAujourdhui = date('d-m-Y');
        $dateAujourdhui = explode('-', $dateAujourdhui);

        // Calcule de l'age
        $age = $dateAujourdhui[2] - $dateNaissance[0];

        // Vérification
        if ($age >= 18){
            return true;
        }
    }
    return false;
}



