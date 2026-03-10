<?php 
session_start();

// Supprime toutes les variables de session
session_unset();

// Détruit la session
session_destroy();

// Redirection vers la page principale
header("Location: ../../index.php");
exit();

?>