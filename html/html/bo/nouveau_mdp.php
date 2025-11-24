<?php
session_start();
if (isset($_SESSION["idCompte"]) && $_SESSION["role"] === "vendeur"){
    $idCompte = $_SESSION["idCompte"];
    $role = $_SESSION["role"];

    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
    </head>
    <body>
        <form method="POST"></form>
    </body>
    </html>

<?php
}
?>
