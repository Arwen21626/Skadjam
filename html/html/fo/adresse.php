<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresse</title>
    <style>
        button a:hover{
            color : black;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../../php/structure/head_front.php');?>
    <?php include __DIR__ . '/../../php/structure/header_front.php'?>        
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <main class="flex flex-col justify-center">
        <h2>Adresse</h2>
        <form class="flex flex-col self-center" action="">
            
            <div class="flex flex-row justify-between">
                <div class="flex flex-col mr-4">
                    <label for="nom">Nom* :</label>
                    <input placeholder="Cobrec" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="nom" id="nom" required>
                </div>

                <div class="flex flex-col ml-5">
                    <label for="prenom">Prénom* :</label>
                    <input placeholder="Alizon" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="prenom" id="prenom" required>
                </div>
            </div>
            
            <div class="flex flex-col mt-5">
                <label for="adresse">Adresse postale* :</label>
                <input placeholder="1 rue des fleurs" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="adresse" id="adresse" required>
            </div>
            

            <div class="flex flex-col mt-5">
                <label for="complement">Complément :</label>
                <input placeholder="" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="complement" id="complement">
            </div>
            
            <div class="flex flex-col mt-5 ">
                <div class="flex flex-row justify-between">
                    <div class="flex self-center flex-col">
                        <label for="numBat">Numéro de batiment :</label>
                        <input placeholder="3C" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numBat" id="numBat">
                    </div>
                    
    
                    <div class="flex flex-col">
                        <label for="numAppart">Numéro d'appartement :</label>
                        <input placeholder="22C" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numAppart" id="numAppart">
                    </div>
                    
                </div>
            </div>

            <div class="flex flex-col">
                <div class="flex flex-col mt-5">
                    <label for="ville">Ville* :</label>
                    <input placeholder="Lannion" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="ville" id="ville" required>
                </div>
                
                <div class="flex flex-col mt-5">
                    <label for="codePostal">Code postal* :</label>
                    <input placeholder="22300" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200 " type="text" name="codePostal" id="codePostal" required>
                </div>
            </div>

            <div class="flex flex-row mt-5">
                <label for="enregistrerAdr" class="mr-5">Enregistrer cette adresse ?</label>
                <input type="checkbox" name="enregistrerAdr" id="enregistrerAdr" class="w-5 h-5">
            </div>

            <div class="flex flex-row mt-5 mb-10 justify-between">
                <button class="border-vertClair border-2 rounded-2xl w-40 h-14"><a href="">Retour</a></button>
                <input class="border-vertClair border-2 rounded-2xl w-40 h-14" type="submit" value="Suivant">
            </div>
            

        </form>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>