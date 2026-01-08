<?php
$size = 1024;
$conn = fsockopen("127.0.0.1",8080);
fwrite($conn, "CONN mewen 1234");
$message = fread($conn, $size);
if ($message === "CONNEXION SUCCESS"){
    echo "$message\n";
    fwrite($conn, "ADD 144254 alizon |1 rue branly| 22300 roussel mewen |3 rue machin| 22450");
    $message = fread($conn, $size);
    echo "$message\n";

    fwrite($conn, "ADD 15223 alizon |1 rue branly| 22300 parveau korentin |5 rue bidule| 29130");
    $message = fread($conn, $size);
    echo "$message\n";
    fclose($conn);

}

?>