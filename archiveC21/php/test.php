<?php

include __DIR__ . '/recupraptor.php';

$rpr = new Recupraptor("127.0.0.1", 6969, "test", "1234");
$ret = $rpr->create_bord("14222", "alison");
echo $ret;
$etat = $rpr->get_etat($ret);
echo $etat;
?>
