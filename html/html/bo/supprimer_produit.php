<?php 
session_start();

include __DIR__ .'/../../01_premiere_connexion.php';

$idProduit = $_GET['idProduit'];
$idCompte = $_SESSION['idCompte'];
?>
<!DOCTYPE html>
<html lang="fr">
    <?php include(__DIR__."/../../php/structure/head_back.php");?>
    <head>
        <title>Supprimer</title>
        <style>
            button a:hover{
                color: black;
            }
        </style>
    </head>
    <body>
        <!--Header-->
        <?php include(__DIR__ . "/../../php/structure/header_back.php"); ?>
        <?php include(__DIR__ . "/../../php/structure/navbar_back.php"); ?>

        <main class=" min-h-[600px]">
            <h2>Voulez-vous vraiment supprimer le produit ?</h2>
            <div class="mt-15 flex flex-row justify-around mb-15">
                <button id="suppProduit" class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Oui</button>
                <a href="../bo/index_vendeur.php"><button class="border-2 border-vertFonce rounded-2xl w-auto h-14 px-7 cursor-pointer">Non</button></a>
            </div>

            <script>
                document.getElementById('suppProduit').addEventListener('click', () => {
                    fetch('../../php/executer_supprimer_produit.php?idProduit=<?php echo $idProduit?>')
                    .then(res => res.text())
                    .then(data => {
                        window.location.href = "index_vendeur.php";
                    });
            });
            </script>

        </main>

        <!-- Footer -->
        <?php include(__DIR__ . "/../../php/structure/footer_back.php"); ?>
    </body>
</html>