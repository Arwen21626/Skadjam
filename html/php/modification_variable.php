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

function format_date($date){
    // reçois une date au format : aaaa-mm-jj
    // et renvoi une date au format : jj/mm/aaaa 

    $dateBonFormat = explode('-', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
}

function numRue($adresse){
if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
    $numero = trim($matches[1]); 

    return $numero;
}

}

function formatAdresse($adresse){
    if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
    $rue = trim($matches[2]);

    return $rue;
    }
}

function formatNum($num){
    $ret = trim($num);
    $ret = explode(" ", $ret);
    return $ret[0];
}

function formatCompNum($num){
        $ret = trim($num);
    $ret = explode(" ", $ret);
    return $ret[1];
}
?>
