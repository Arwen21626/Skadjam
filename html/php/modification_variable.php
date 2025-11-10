<?php 
function formatPrenom($nom){
    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function formatTel($tel){
    $tel = str_replace(" ", "", trim($tel));
    return "+33". substr($tel, 1);
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