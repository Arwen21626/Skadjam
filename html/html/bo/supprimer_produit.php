<?php 
session_start();

include __DIR__ .'/../../01_premiere_connexion.php';

$idPorduit = 1; //a changer
$idCompte = $_SESSION['idCompte'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/output.css" >
    <title>Supprimer</title>
    <style>
        button a:hover{
            color: black;
        }
    </style>
</head>
<body>
    <!--Header-->
    <?php include("../../php/structure/header_back.php"); ?>
    <?php include("../../php/structure/navbar_back.php"); ?>

    <main>
        <h2>Voulez-vous vraiment supprimer le produit ?</h2>
        <div class="mt-15 flex flex-row justify-around mb-15">
            <button id="suppProduit" class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7">Oui</button>
            <button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7"><a href="../bo/index_vendeur.php">Non</a></button>
        </div>

        <script>
            document.getElementById('suppProduit').addEventListener('click', () => {
                fetch('../../php/executer_supprimer_produit.php')
                .then(res => res.text())
                .then(data => {
                    window.location.href = "index_vendeur.php";
                });
        });
        </script>

    </main>

    <!-- Footer -->
    <?php include("../../php/structure/footer_back.php"); ?>
</body>
</html>