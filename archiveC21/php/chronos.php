<?php
$size = 1024;
$i = 0;


echo "\n==============================\n";
echo "[CHRONOS] Nouvelle itération : $i\n";
echo "==============================\n";

echo "[CHRONOS] Connexion au serveur...\n";
$conn = fsockopen("127.0.0.1", 6969, $errno, $errstr, 5);

if (!$conn) {
    echo "[ERREUR] Impossible de se connecter : $errstr ($errno)\n";
}

echo "[CHRONOS] Connexion établie\n";

// --- ENVOI CONN ---
echo "[CHRONOS] Envoi : CONN mewen 1234\n";
fwrite($conn, "CONN test 1234\n");

echo "[CHRONOS] Attente réponse...\n";
$message = fread($conn, $size);
echo "[CHRONOS] Réponse reçue : '$message'\n";

// --- SI OK, ENVOI NEXT ---
if (trim($message) === "CONNEXION SUCCESS") {

    echo "[CHRONOS] Authentification OK\n";

    echo "[CHRONOS] Envoi : NEXT\n";
    fwrite($conn, "NEXT\n");

    echo "[CHRONOS] Attente réponse NEXT...\n";
    $message = fread($conn, $size);
    echo "[CHRONOS] Réponse NEXT : '$message'\n";

} else {
    echo "[CHRONOS] Authentification refusée ou réponse inattendue\n";
}

// --- VÉRIFICATION DE LA CONNEXION ---
if (!is_resource($conn) || feof($conn)) {
    echo "[CHRONOS] Connexion fermée par le serveur\n";
    exit;
}

echo "[CHRONOS] Fermeture de la connexion\n";
fclose($conn);

$i++;
