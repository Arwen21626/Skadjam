<?php
echo getcwd();
//include('../connections_param.php');
try {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", 
            $user, $pass);
    foreach($dbh->query('SELECT * from sae3_skadjam._compte', 
                        PDO::FETCH_ASSOC) 
                as $row) {
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    }
    $dbh = null;
} catch (PDOException $e) {
    print "Erreur !: " . $e->getMessage() . "<br/>";
    die();
}

?>
