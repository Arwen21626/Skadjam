<?php 

    if ($_SESSION["role"] === "vendeur") {
        header("location:/html/bo/404_vendeur.php");
    }
?>