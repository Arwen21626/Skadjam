<?php 
function format_prenom($nom){
    $ret = trim($nom);
    $ret = str_replace(" ", "", $ret);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}
?>