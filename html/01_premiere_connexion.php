<?php

// Julien : C'est pour mes test, tout marche nickel

//echo __DIR__;
include (__DIR__ . '/../connections_params.php');
try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

?>
