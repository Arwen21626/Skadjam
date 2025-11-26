<?php 
    if (isset($_SESSION["role"])){
        if ($_SESSION["role"] === "vendeur") {
            header("location:/html/bo/404_vendeur.php");
        }
    }else{
        header("Location : /index.php");
    }
?>