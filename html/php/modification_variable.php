<?php 
function formatPrenom($nom){
    // renvoi un prenom ou un nom avec le format : une majuscule suivie de miniscules

    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function formatTel($tel){
    // renvoi un numéro de téléphone avec le format : +33 suivie de 9 chiffres

    $tel = str_replace(" ", "", trim($tel));
    return "+33". substr($tel, 1);
}

function formatDatePourBDD($date){
    // reçois une date au format : aaaa-mm-jj
    // et renvoi une date au format : jj/mm/aaaa 

    $dateBonFormat = explode('-', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
}

function formatDatePourInput($date){
    // reçois une date au format : jj/mm/aaaa 
    // et renvoi une date au format : aaaa-mm-jj

    $dateBonFormat = explode('/', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
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
