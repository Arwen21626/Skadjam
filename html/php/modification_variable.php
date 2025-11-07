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
?>