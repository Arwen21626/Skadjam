<?php
$size = 1024;
$id1;
$id2;
$id3;
$conn = fsockopen("127.0.0.1",8080);
fwrite($conn, "CONN mewen 1234\n");
$message = fread($conn, $size);
$id_suivie;
if ($message === "CONNEXION SUCCESS"){
    echo "$message\n";
    fwrite($conn, "ADD 144254 alizon |1 rue branly| 22300 roussel mewen |3 rue machin| 22450\n");
    $message = fread($conn, $size);
    echo "$message\n";

    $line = explode(" ", $message);
    if ($line[0] === "BORD"){
        $id1 = $line[1];
        fwrite($conn, "ETA " . $id1 . "\n");
        $message = fread($conn, $size);
        echo "$message\n";

    }
    sleep(10);

    fwrite($conn, "ADD 15223 alizon |1 rue branly| 22300 parveau korentin |5 rue bidule| 29130\n");
    $message = fread($conn, $size);
    echo "$message\n";
    $line = explode(" ", $message);
    if ($line[0] === "BORD"){
        $id2 = $line[1];
        fwrite($conn, "ETA " . $id2 . "\n");
        $message = fread($conn, $size);
        echo "$message\n";
    }

    fwrite($conn, "ETA " . $id1 . "\n");
    $message = fread($conn, $size);
    echo "$message\n";

    sleep(10);

    $id_suivie = "ALI1245369547";
    fwrite($conn, "ETA " . $id_suivie . "\n");
    $message = fread($conn, $size);
    echo "$message\n";

    sleep(20);
    fwrite($conn, "ADD a1215m4454 alizon |1 rue branly| 22300 parveau korentin |5 rue bidule| 29130\n");
    $message = fread($conn, $size);
    echo "$message\n";
    $line = explode(" ", $message);
    if ($line[0] === "BORD"){
        $id3 = $line[1];
        fwrite($conn, "ETA " . $id3 . "\n");
        $message = fread($conn, $size);
        echo "$message\n";
    }

    fwrite($conn, "ETA " . $id2 . "\n");
    $message = fread($conn, $size);
    echo "$message\n";

    fwrite($conn, "ETA " . $id1 . "\n");
    $message = fread($conn, $size);
    echo "$message\n";

    $id_suivie = "ALI1245369547";
    fwrite($conn, "ETAT " . $id_suivie . "\n");
    $message = fread($conn, $size);
    echo "$message\n";

    fclose($conn);

    
}
if (!is_resource($conn) || feof($conn)) {
    echo "Connexion fermée par le serveur\n";
    exit;
}


?>