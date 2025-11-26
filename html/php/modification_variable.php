<?php 
function formatPrenom($nom){
    // renvoi un prenom ou un nom avec le format : une majuscule suivie de miniscules

    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function formatTel($tel){
    // renvoi un numéro de téléphone avec le format : +33 suivie de 9 chiffres
    // ou un numéro de téléphone avec le format : 0 suivie de 9 chiffres
    $tel = str_replace(" ", "", trim($tel));
    if (substr($tel, 0, 1) === '+'){
        return "0".substr($tel, 3);
    }
    else{
        return "+33". substr($tel, 1);
    }
}

function formatDate($date){
    // reçois une date au format : aaaa-mm-jj
    // et renvoi une date au format : jj/mm/aaaa 
    // ou l'inverse
    
    if (substr($date, 4, 1) === '-'){
        $dateBonFormat = explode('-', $date);
        return $dateBonFormat[2].'/'.$dateBonFormat[1].'/'.$dateBonFormat[0];
    }
    else{
        $dateBonFormat = explode('/', $date);
        return $dateBonFormat[2].'-'.$dateBonFormat[1].'-'.$dateBonFormat[0];
    }
    
}

function tabAdresse($adresse){
    //recois une adresse format : x [bis,...] nomRue
    //renvoi un tableau avec le numéro le complement de numéro et le reste de l'adresse
    $num = "";
    $complementNum = "";

    //separe le numero complet du reste de l'adresse
    if (preg_match('/^(\d+\s*(?:bis|ter|quater)?)[, ]+(.+)$/ui', $adresse, $matches)) {
        $numeroComplet = trim($matches[1]);
        $adresse = trim($matches[2]); 

        //si le numéro est suivit d'un copletment ex bis ils sont séparé
        if (preg_match('/^(\d+)\s*([A-Za-z]*)$/i', $numeroComplet, $matches)){
            $num = $matches[1];
            $complementNum = $matches[2] ?: "";
        }
    }
    return [$num, $complementNum, $adresse];
}

function modifierSiegeSocial($adresse){
    // modifie l'adresse pour ne garder que la ville et le code postale
    // adresse au format : x [bis,...] nomRue, ville, cp

    $parts = explode(',', $adresse);
    if (count($parts) > 2){
        $ville = trim($parts[count($parts) - 2]);
        $cp = trim($parts[count($parts) - 1]);
        $adresseRue = trim(implode(',', array_slice($parts, 0, -2)));
        return ["adresse"=>$adresseRue, "ville"=>$ville, "cp"=>$cp];
    }else{
        return -1;
    }
}