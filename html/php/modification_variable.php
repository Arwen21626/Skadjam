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
    //recois une adresse complete
    //renvoi le numéro et le complement
    if (preg_match('/^(\d+\s*(?:bis|ter|quater)?)[, ]+(.+)$/ui', $adresse, $matches)) {
        $numero = trim($matches[1]); 

        return $numero;
    }

}

function formatAdresse($adresse){
    //recois une adresse complete
    //renvoi la rue
    if (preg_match('/^(\d+\s*(?:bis|ter|quater)?)[, ]+(.+)$/ui', $adresse, $matches)) {
    $rue = trim($matches[2]);
    return $rue;
    }
}

function formatNum($num){
    //recois le numéro et le complement
    //renvoi le numero
    $num = trim($num);
    $ret = '';
    if (preg_match('/^(\d+)\s*([A-Za-z]*)$/i', trim($num), $matches)) {
        $ret = $matches[1];
        return $ret;
    }
    return $ret;
}

function formatCompNum($num){
    //recois le numéro et le complement
    //renvoi le complément
    $comp = trim($num);
    $ret = '';
    if (preg_match('/^(\d+)\s*([A-Za-z]*)$/i', trim($num), $matches)) {
        $ret = $matches[2] ?: ''; // vide si pas de suffixe
        return $ret;
    }
    return $ret;
}
?>
