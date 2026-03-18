<?php
include __DIR__ . "/../01_premiere_connexion.php";

function verifNomPrenom($nom){
    // Vérification que soit un prénom soit un nom soyent au bon format
    if (strlen($nom) > 100 || !preg_match("/^[A-Za-zÀ-öø-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/", $nom)){
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
    if (strlen($mail) > 150 || !preg_match("/^[A-Za-z0-9.]+@[A-Za-z-]+.[A-Za-z]+$/", $mail)){
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
    return preg_match("/^[a-zA-ZÀ-öø-ÿ0-9 -]*$/", $denomination);
}

function verifSiren($siren){
    // Vérification d'un numéro de SIREN

    if (000000000<=$siren && $siren<=999999999 && preg_match("/^[0-9]{9}$/", $siren)){
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
    if (strlen($pseudo) > 30 || !preg_match("/^[A-Za-zÀ-öø-ÿ]+(( |-{1,2})[A-Za-zÀ-öø-ÿ]+)*$/", $pseudo)){
        return false;
    }
    else{
        return true;
    }
}


function verifDate($date){
    // Vérifie que la date est au format jj-mm-aaaa
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

function verifAge($naissance){
    if (!verifDate($naissance)) return false;

    $dateNaissance = new DateTime($naissance);
    $aujourdHui = new DateTime();

    // Calcul exact de l'âge
    $age = $aujourdHui->diff($dateNaissance)->y;

    return $age >= 18;
}

function verifCp($cp){
    // verifie le format du code postale
    return ($cp > 1 && $cp<99999 && preg_match("/^[0-9]{5}$/", $cp));
}

function verifVille($ville){
    //verifie le format de la ville
    return preg_match("/^[A-Za-zÀ-öø-ÿ -]+$/", $ville);
}

function verifAdresse($adresse){
    //verifie le format de l'adresse
    return (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse));
}


function verifNumCarte($num){
    if(preg_match('/[0-9]{16}/',$num)){
        return true;
    }else{
        $exp = explode(" ", $num);
        $numero = $exp[0].$exp[1].$exp[2].$exp[3];
        //Vérfie que le numéro à bien 16 chiffres
        return (preg_match('/[0-9]{16}/',$numero));
    }
}

function verifExpiration($date){
    //Vérifie que la date d'expiration n'est pas dépassé
    $dateCut = explode('/', $date, 2);
    $mois = $dateCut[0];
    $annee = $dateCut[1];
    $anneeEnCours = date('Y');
    $moisEnCours = date('m');
    $annee += 2000;
    $valide = false;

    if($annee > $anneeEnCours && $mois > 0 && $mois <= 12){
        $valide = true;
    }
    else if($annee == $anneeEnCours){
        if($mois >= $moisEnCours && $mois > 0 && $mois <= 12){
            $valide = true;
        }else{
            $valide = false;
        }
    }
    else{
        $valide = false;
    }
    return $valide;
}

function verifCryptogramme($cryptogramme){
    // Vérifie que le cryptogramme à bien 3 chiffres
    return (preg_match('[0-9]{3}',$cryptogramme));
}

function verifPourcentage($pourcentage){
    if ($pourcentage <= 1 && $pourcentage >= 0){
        return true;
    }
    else{
        return false;
    }
}