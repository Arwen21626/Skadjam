<?php 
function format_prenom($nom){
    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function format_tel($tel){
    $tel = str_replace(" ", "", trim($tel));
    return "+33". substr($tel, 1);
}

function num_rue($adresse){
if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
    $numero = trim($matches[1]); 

    return $numero;
}

}

function format_adresse($adresse){
    if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
    $rue = trim($matches[2]);

    return $rue;
}
}
?>