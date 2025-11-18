<?php
session_start();
//echo $_SESSION['role'];
if($_SESSION['role'] != 'client'){
    echo 'Vous n\'avez pas accès, vous n\'êtes pas client';
    //header('Location: ./404.php');
}
?>

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

    <?php
    // if(isset($_POST['numero'])){
    //     // Initialisation des variables
        $erreurNumero = true;
        $erreurExpiration = true;
        $erreurCryptogramme = true;
        $erreurNom = true;


    //     echo '<pre>';
    //     print_r($_SESSION);
    //     print_r($_POST);
    //     echo '<pre>';
    
    // }
    ?>



    <?php include(__DIR__ . '/../../php/structure/head_front.php');?>
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <main>
        <form class="w-1/5 justify-self-center" action="paiement.php" method="post">

                <div class="flex flex-col">
                    <label for="numero">Numéro de carte* :</label>
                    <input placeholder="0000 1111 2222 3333" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-100" type="text" name="numero" id="numero" required>
                    <?php
                        if($erreurNumero){ ?>
                            <p class="text-rouge"><?php echo "Le numéro n'est pas bon";?></p>
                    <?php } ?>
                </div>
                
                <div class="flex flex-row">
                    <div class="flex flex-col">
                        <label for="expiration">Date d'expiration* :</label>
                        <input placeholder="MM/AA" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="month" name="expiration" id="expiration" required>
                        
                        <?php if($erreurExpiration){ ?>
                            <p class="text-rouge"><?php echo "La date n'est pas bonne";?></p>
                        <?php } ?>
                    </div>
                    

                    <div class="flex flex-col ml-7">
                        <label for="cryptogramme">Cryptogramme* :</label>
                        <input placeholder="000" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="cryptogramme" id="cryptogramme" required>
                        
                        <?php if($erreurCryptogramme){ ?>
                            <p class="text-rouge"><?php echo "Le cryptogramme n'est pas bon";?></p>
                        <?php } ?>
                    </div>
                </div>

                <div class="flex flex-col">
                    <label for="nom">Nom du titulaire* :</label>
                    <input placeholder="M Alizon" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-100" type="text" name="nom" id="nom" required>
                    
                    <?php if($erreurNom){ ?>
                            <p class="text-rouge"><?php echo "Le nom n'est pas bon";?></p>
                    <?php } ?>
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