<?php

include __DIR__ . '/recupator.php';

$rpr = new Recupraptor("127.0.0.1", 8081, "mewen", "1234");
$ret = $rpr->create_bord("14222", "alison", "4 avenue fosh", 22300, "roussel", "mewen", "5 rue machin", 22450);
echo $ret;
$etat = $rpr->get_etat($ret);
echo $etat;
?>