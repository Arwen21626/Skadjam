<?php 

    if ($_SESSION["role"] !== "vendeur") {
        header("location:/404.php");
    }
?>