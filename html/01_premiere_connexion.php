<?php

// Julien : C'est pour mes test, tout marche nickel

// echo __DIR__;
include 'connections_params.php';
try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", 
            $user, $pass);
    
    // foreach($dbh->query('SELECT * from sae3_skadjam._vendeur', PDO::FETCH_ASSOC) as $row) {
    //     echo "<pre>";
    //     print_r($row);
    //     echo "</pre>";
    //     echo $row['id_compte'];
    // }
    // $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

?>
