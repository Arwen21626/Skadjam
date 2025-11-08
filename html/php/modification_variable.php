<?php 
function format_prenom($nom){
    // renvoi un prenom ou un nom avec le format : une majuscule suivie de miniscules
    
    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function format_tel($tel){
    // renvoi un numéro de téléphone avec le format : +33 suivie de 9 chiffres

    $tel = str_replace(" ", "", trim($tel));
    return "+33". substr($tel, 1);
}

function format_date($date){
    // reçois une date au format : aaaa-mm-jj
    // et renvoi une date au format : jj/mm/aaaa 

    $dateBonFormat = explode('-', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
}

                



?>