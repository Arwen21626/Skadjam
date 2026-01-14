<?php
$size = 1024;
$i=0;
while (1){
    $conn = fsockopen("127.0.0.1",8080);
    fwrite($conn, "CONN mewen 1234\n");
    $message = fread($conn, $size);
    
    if ($message === "CONNEXION SUCCESS"){
        echo "$message\n";
        fwrite($conn, "NEXT\n");
        $message = fread($conn, $size);
        echo "$message\n";
    
    }
    if (!is_resource($conn) || feof($conn)) {
        echo "Connexion fermée par le serveur\n";
        exit;
    }
    fclose($conn);
    $i++;
    sleep(30);
}

?>