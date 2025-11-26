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
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <main >
        <h2>Adresse</h2>
        <form class="w-4/5 self-center" action="">
            
            <div class="flex flex-row">
                <div class="flex flex-col">
                    <label for="nom">Nom* :</label>
                    <input placeholder="Cobrec" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="nom" id="nom" required>
                </div>
                

                <div class="flex flex-col">
                    <label for="prenom">Prénom* :</label>
                    <input placeholder="Alizon" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="prenom" id="prenom" required>
                </div>
                
            </div>
            
            <div class="flex flex-col">
                <label for="adresse">Adresse postale* :</label>
                <input placeholder="1 rue des fleurs" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="adresse" id="adresse" required>
            </div>
            

            <div class="flex flex-col">
                <label for="complement">Complément :</label>
                <input placeholder="" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="complement" id="complement">
            </div>
            
            
            <div class="flex flex-row">
                <div class="flexself-center flex-col">
                    <label for="numBat">Numéro de batiment :</label>
                    <input placeholder="3C" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numBat" id="numBat">
                </div>
                

                <div class="flex flex-col">
                    <label for="numAppart">Numéro d'appartement :</label>
                    <input placeholder="22C" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numAppart" id="numAppart">
                </div>
                
            </div>

            <div class="flex flex-row">
                <div class="flex flex-col">
                    <label for="ville">Ville* :</label>
                    <input placeholder="Lannion" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="ville" id="ville" required>
                </div>
                
                <div class="flex flex-col">
                    <label for="codePostal">Code postal* :</label>
                    <input placeholder="22300" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="codePostal" id="codePostal" required>
                </div>
            </div>

            <label for="enregistrerAdr">Enregistrer cette adresse ?</label>
            <input type="checkbox" name="enregistrerAdr" id="enregistrerAdr">

            <div class="flex flex-row">
                <button class="border-vertClair border-2 rounded-2xl w-40 h-14"><a href="">Retour</a></button>
                <input class="border-vertClair border-2 rounded-2xl w-40 h-14" type="submit" value="Suivant">
            </div>
            

        </form>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>