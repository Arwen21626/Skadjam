<?php
session_start();
if($_SESSION['role'] != 'client'){
    header('Location: ./404.php');
}else{
    include(__DIR__ . '/../../php/verification_formulaire.php');
    include __DIR__ . '/../../01_premiere_connexion.php';
    
    $achatValide = false;
    if(isset($_POST['numero'])){
    // Initialisation des variables
        $erreurNumero = false;
        $erreurExpiration = false;
        $erreurCryptogramme = false;
        $erreurNom = false;

        $numero = htmlentities($_POST['numero']);
        $mois = htmlentities($_POST['mois']);
        $annee = htmlentities($_POST['annee']);
        $expiration = $mois . '/' . $annee;
        $cryptogramme = htmlentities($_POST['cryptogramme']);
        $nom = htmlentities($_POST['nom']);
        $enregistrerCarte = htmlentities($_POST['enregistrerCarte']);
        $codePromo = htmlentities($_POST['codePromo']);
        $carteCadeau = htmlentities($_POST['carteCadeau']);

        //echo $_POST['expiration']; // 22/25
        if(!verifExpiration($expiration)){
            $erreurExpiration = true;
        }
        if(!verifNomPrenom($nom)){
            $erreurNom = true;
        }
        if(!verifNumCarte($numero)){
            $erreurNumero = true;
        }

        if(isset($enregistrerCarte)){
            if($enregistrerCarte == 'on'){
                $idCompte = $_SESSION['idCompte'];
                $nouvCarte = $dbh->prepare("INSERT INTO sae3_skadjam._carte_bancaire(numero_carte, cryptogramme, nom, expiration, id_client) VALUES($numero, $cryptogramme, '$nom', $expiration, $idCompte)");
                $nouvCarte->execute();
            }
        }
        $achatValide = true;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include(__DIR__ . '/../../php/structure/head_front.php');?>
    <title>Paiement</title>
    <style>
        button a:hover{
            color : black;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/../../php/structure/header_front.php');?>
    <?php include(__DIR__ . '/../../php/structure/navbar_front.php');?>
    <?php if(!$achatValide){?>
        <main class="md:min-h-[800px] min-h-[600px]">
            <form action="paiement.php" method="post">

                <div class="flex flex-col md:items-center items-start ml-5 md:ml-0">
                    <div class="flex flex-col mb-5 mt-5">
                        <label for="numero">Numéro de carte* :</label>
                        <input placeholder="0000 1111 2222 3333" pattern="[0-9]{16}" value="<?= isset($_POST['numero'])? $numero : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 md:w-100 w-75" type="text" name="numero" id="numero" required>
                        <?php
                            if($erreurNumero){ ?>
                                <p class="text-rouge"><?php echo "Le numéro n'est pas bon";?></p>
                        <?php } ?>
                    </div>
                    
                    <div class="flex flex-col mb-5">
                        <div class="flex flex-col w-100">
                            <label for="expiration">Date d'expiration* :</label>
                            <p class="flex flex-row">
                                <input placeholder="MM" value="<?= isset($_POST['mois'])? $mois : "" ?>" maxlength="2" pattern="0[1-9]|1[0-2]" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-15" type="text" name="mois" id="mois" required>
                                /
                                <input placeholder="AA" value="<?= isset($_POST['annee'])? $annee : "" ?>" maxlength="2" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-15" type="text" name="annee" id="annee" required>
                            </p>
                            <?php if($erreurExpiration){ ?>
                                <p class="text-rouge"><?php echo "La date n'est pas bonne";?></p>
                            <?php } ?>
                        </div>
                        
        
                        <div class="flex flex-col mt-5">
                            <label for="cryptogramme">Cryptogramme* :</label>
                            <input placeholder="000" pattern="[0-9]{3}" value="<?= isset($_POST['cryptogramme'])? $cryptogramme : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-50" type="text" name="cryptogramme" id="cryptogramme" required>
                            
                            <?php if($erreurCryptogramme){ ?>
                                <p class="text-rouge"><?php echo "Le cryptogramme n'est pas bon";?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="flex flex-col mb-5">
                        <label for="nom">Nom du titulaire* :</label>
                        <input placeholder="M Alizon" value="<?= isset($_POST['nom'])? $nom : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 md:w-100 w-75 ml-0" type="text" name="nom" id="nom" required>
                        
                        <?php if($erreurNom){ ?>
                                <p class="text-rouge"><?php echo "Le nom n'est pas bon";?></p>
                        <?php } ?>
                    </div>
                    <div class="md:w-100 md:ml-5 ml-0">
                        <label for="enregistrerCarte">Enregistrer cette carte pour les prochains paiements?</label>
                        <input type="checkbox" name="enregistrerCarte" id="enregistrerCarte">
                    </div>
        
                    <!-- <div class="flex flex-row mb-5">
                        <div class="flex flex-col">
                            <label for="codePromo">Code promotionnel :</label>
                            <input placeholder="" value="<?= isset($_POST['codePromo'])? $codePromo : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-50" type="text" name="codePromo" id="codePromo">
                            
                            <?php if($erreurCodePromo){ ?>
                                <p class="text-rouge"><?php echo "Le code promo n'est pas bon";?></p>
                            <?php } ?>
                        </div>
            
                        <div class="flex flex-col ml-6">
                            <label for="carteCadeau">Code carte cadeau :</label>
                            <input placeholder="" value="<?= isset($_POST['carteCadeau'])? $carteCadeau : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-50" type="text" name="carteCadeau" id="carteCadeau">
                            
                            <?php if($erreurCarteCadeau){ ?>
                                <p class="text-rouge"><?php echo "La carte cadeau n'est pas valide";?></p>
                            <?php } ?>
                        </div>
                    </div> -->
                </div>

                <div class="flex flex-row justify-center">
                    <a href="../../index.php"><button class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer m-5">Retour</button></a>
                    <input class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer m-5" type="submit" value="Suivant">
                </div>
            </form>
        </main>
    <?php }else{ ?>
        <main class="text-center min-h-[500px]">
            <div class="mt-30">
                <H1 class="">Votre achat à bien été validé</h1>
                <a href="../../index.php"><button class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer m-7">Retour à l'acceuil</button></a>
            </div>
        </main>
    <?php }?>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>