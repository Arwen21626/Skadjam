<?php 
    if (isset($_SESSION["role"])){
        if ($_SESSION["role"] !== "vendeur") {
            header("location:/404.php");
        }
    }else{
        header("Location: /index.php");
    }
?>