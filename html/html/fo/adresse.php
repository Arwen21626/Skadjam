<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    include __DIR__ . '/../../php/verification_formulaire.php';

    $erreurNom = false;
    $erreurPrenom = false;
    $erreurAdresse = false;
    $erreurVille = false;
    $erreurCodePostal = false;


    if(isset($_POST['nom'])){
        $nom = htmlentities($_POST['nom']);
        $prenom = htmlentities($_POST['prenom']);
        $adresse = htmlentities($_POST['adresse']);
        $ville = htmlentities($_POST['ville']);
        $codePostal = htmlentities($_POST['codePostal']);

        if(isset($_POST['complement'])){
            $complement = $_POST['complement'];
        }

        if(isset($_POST['numBat'])){
            $numBat = $_POST['numBat'];
        }

        if(isset($_POST['numAppart'])){
            $numAppart = $_POST['numAppart'];
        }

        // Vérifiation des erreurs
        if(!verifAdresse($adresse)){
            $erreurAdresse = true;
        }

        if(!verifCp($codePostal)){
            $erreurCodePostal = true;
        }

        if(!verifVille($ville)){
            $erreurVille = true;
        }

        if(!verifNomPrenom($nom)){
            $erreurNom = true;
        }

        if(!verifNomPrenom($prenom)){
            $erreurPrenom = true;
        }


        // Si tout est bon alors redirection vers la page paiement
        if($erreurNom == false && $erreurPrenom == false && $erreurAdresse == false && $erreurVille == false && $erreurCodePostal == false){
            header('Location : /../fo/paiement.php');
        }

    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresse</title>
</head>
<?php include(__DIR__ . '/../../php/structure/head_front.php');?>
<body>
    <?php include __DIR__ . '/../../php/structure/header_front.php'?>        
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <main class="flex flex-col justify-center">
        <h2>Adresse de livraison</h2>
        <form class="flex flex-col self-center" action="adresse.php" method="post">
            
            <div class="flex flex-row justify-between">
                <div class="flex flex-col max-w-70">
                    <label for="nom">Nom* :</label>
                    <input placeholder="Cobrec" value="<?= isset($_POST['nom'])? $nom : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 max-w-70" type="text" name="nom" id="nom" required>
                    <?php 
                    if($erreurNom){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre nom</p>
                    <?php } ?>
                </div>

                <div class="flex flex-col max-w-70">
                    <label for="prenom">Prénom* :</label>
                    <input placeholder="Alizon" value="<?= isset($_POST['prenom'])? $prenom : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 max-w-70" type="text" name="prenom" id="prenom" required>
                    <?php 
                    if($erreurPrenom){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre prenom</p>
                    <?php } ?>
                </div>
            </div>
            
            <div class="flex flex-col mt-5">
                <label for="adresse">Adresse postale* :</label>
                <input placeholder="1 rue des fleurs" value="<?= isset($_POST['adresse'])? $adresse : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="adresse" id="adresse" required>
                <?php 
                if($erreurAdresse){ ?>
                    <p class="text-rouge">Une erreur est survenue au niveau de votre adresse</p>
                <?php } ?>
            </div>
            

            <div class="flex flex-col mt-5">
                <label for="complement">Complément :</label>
                <input placeholder="" value="<?= isset($_POST['complement'])? $complement : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="complement" id="complement">
            </div>
            
            <div class="flex flex-col mt-5 ">
                <div class="flex flex-row justify-between">
                    <div class="flex self-center flex-col">
                        <label for="numBat">Numéro de batiment :</label>
                        <input placeholder="3C" value="<?= isset($_POST['numBat'])? $numBat : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numBat" id="numBat">
                    </div>
                    
    
                    <div class="flex flex-col">
                        <label for="numAppart">Numéro d'appartement :</label>
                        <input placeholder="22C" value="<?= isset($_POST['numAppart'])? $numAppart : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500" type="text" name="numAppart" id="numAppart">
                    </div>
                    
                </div>
            </div>

            <div class="flex flex-col">
                <div class="flex flex-col mt-5">
                    <label for="ville">Ville* :</label>
                    <input placeholder="Lannion" value="<?= isset($_POST['ville'])? $ville : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200" type="text" name="ville" id="ville" required>
                    <?php 
                    if($erreurVille){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre ville</p>
                    <?php } ?>
                </div>
                
                <div class="flex flex-col mt-5">
                    <label for="codePostal">Code postal* :</label>
                    <input placeholder="22300" value="<?= isset($_POST['codePostal'])? $codePostal : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-200 " type="text" name="codePostal" id="codePostal" required>
                    <?php 
                    if($erreurCodePostal){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre code postal</p>
                    <?php } ?>
                </div>
            </div>

            <div class="flex flex-row mt-5">
                <label for="enregistrerAdr" class="mr-5">Enregistrer cette adresse ?</label>
                <input type="checkbox" name="enregistrerAdr" id="enregistrerAdr" class="w-5 h-5 mt-1">
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