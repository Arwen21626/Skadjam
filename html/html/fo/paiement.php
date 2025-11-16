<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <style>
        button a:hover{
            color : black;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../../php/structure/head_front.php');?>
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <main>
        <form class="w-4/5" action="">

                <div class="flex flex-col">
                    <label for="numero">Numéro de carte* :</label>
                    <input class="border-4 border-vertClair rounded-2xl" type="text" name="numero" id="numero" required>
                </div>
                
                <div class="flex flex-row">
                    <div class="flex flex-col">
                        <label for="expiration">Date d'expiration* :</label>
                        <input class="border-4 border-vertClair rounded-2xl" type="date" name="expiration" id="expiration" required>
                    </div>
                    

                    <div class="flex flex-col">
                        <label for="cryptogramme">Cryptogramme* :</label>
                        <input class="border-4 border-vertClair rounded-2xl" type="text" name="cryptogramme" id="cryptogramme" required>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label for="nom">Nom du titulaire* :</label>
                    <input class="border-4 border-vertClair rounded-2xl" type="text" name="nom" id="nom" required>
                </div>

            <label for="enregistrerCarte">Enregistrer cette carte pour les prochains paiements?</label>
            <input type="checkbox" name="enregistrerCarte" id="enregistrerCarte">

            <div class="flex flex-row">
                <button class="border-vertClair border-2 rounded-2xl w-40 h-14"><a href="">Retour</a></button>
                <input class="border-vertClair border-2 rounded-2xl w-40 h-14" type="submit" value="Suivant">
            </div>
        </form>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>