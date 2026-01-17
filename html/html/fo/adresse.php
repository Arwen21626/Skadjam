<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
    require_once __DIR__ . "/../../01_premiere_connexion.php";
    require_once __DIR__ . "/../../php/modification_variable.php";

    $erreurNom = false;
    $erreurPrenom = false;
    $erreurAdresse = false;
    $erreurVille = false;
    $erreurCodePostal = false;

    $idClient = $_SESSION['idCompte'];
    $idPanier = $_REQUEST['idPanier'];

    if(isset($_POST['nom'])){
        include __DIR__ . '/../../php/verification_formulaire.php';

        $nom = htmlentities($_POST['nom']);
        $prenom = htmlentities($_POST['prenom']);
        $adresse = htmlentities($_POST['adresse']);
        $ville = htmlentities($_POST['ville']);
        $codePostal = htmlentities($_POST['codePostal']);

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

        $adresseExplode = tabAdresse($adresse);

        if(isset($_POST['enregistrerAdr'])){
            // mettre dans habite
            if($_POST['enregistrerAdr'] == 'on' && ($erreurAdresse == false && $erreurVille == false && $erreurCodePostal == false)){
                $nouvCarte = $dbh->prepare("INSERT INTO sae3_skadjam._adresse(
                                                adresse_postale, complement_adresse, numero_rue, 
                                                numero_bat, numero_appart, code_postal, ville
                                            ) 
                                            VALUES(?, ?, ?, ?, ?, ?, ?)");
                echo "salut 2 : $adresseExplode[2], $adresseExplode[1], $adresseExplode[0],
                                    $numBat, $numAppart, $codePostal, $ville";
                $nouvCarte->execute([$adresseExplode[2], $adresseExplode[1], $adresseExplode[0],
                                    $numBat, $numAppart, $codePostal, $ville]);
            }
        }
        // Si tout est bon alors redirection vers la page paiement
        if($erreurNom == false && $erreurPrenom == false && $erreurAdresse == false && $erreurVille == false && $erreurCodePostal == false){
            $nouvCarte = $dbh->prepare("INSERT INTO sae3_skadjam._adresse_livraison(
                                                adresse_postale, complement_adresse, numero_rue, 
                                                numero_bat, numero_appart, code_postal, ville
                                            ) 
                                            VALUES(?, ?, ?, ?, ?, ?, ?)");
                echo "salut 0 : $adresseExplode[2], $numAdr,
                                    $numBat, $numAppart, $codePostal, $ville";
                $nouvCarte->execute([$adresseExplode[2], $adresseExplode[1], $adresseExplode[0],
                                    $numBat, $numAppart, $codePostal, $ville]);

            header('Location: /html/fo/paiement.php?idPanier=' . $idPanier);
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
    <?php include __DIR__ . '/../../php/structure/header_front.php'; ?>        
    <?php include __DIR__ . '/../../php/structure/navbar_front.php'; ?>
    <main class="flex flex-col justify-center">
        <h2>Adresse de livraison</h2>
        <form class="flex flex-col self-center" method="post">
            
            <div class="flex flex-row justify-between">
                <div class="flex flex-col max-w-70">
                    <label for="nom">Nom* :</label>
                    <input placeholder="Cobrec" value="<?= isset($_POST['nom'])? $nom : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 max-w-70" type="text" name="nom" id="nom" required>
                    <?php 
                    if($erreurNom){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre nom</p>
                    <?php } ?>
                </div>

                <div class="flex flex-col max-w-70">
                    <label for="prenom">Prénom* :</label>
                    <input placeholder="Alizon" value="<?= isset($_POST['prenom'])? $prenom : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 max-w-70" type="text" name="prenom" id="prenom" required>
                    <?php 
                    if($erreurPrenom){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre prénom</p>
                    <?php } ?>
                </div>
            </div>
            
            <div class="flex flex-col mt-5">
                <label for="adresse">Adresse postale* :</label>
                <input placeholder="1 rue des fleurs" value="<?= isset($_POST['adresse'])? $adresse : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-200" type="text" name="adresse" id="adresse" required>
                <?php 
                if($erreurAdresse){ ?>
                    <p class="text-rouge">Une erreur est survenue au niveau de votre adresse</p>
                <?php } ?>
            </div>
            
            <div class="flex flex-col mt-5 ">
                <div class="flex flex-row justify-between">
                    <div class="flex self-center flex-col">
                        <label for="numBat">Numéro de bâtiment :</label>
                        <input placeholder="3C" value="<?= isset($_POST['numBat'])? $numBat : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500" type="text" name="numBat" id="numBat">
                    </div>
                    
    
                    <div class="flex flex-col">
                        <label for="numAppart">Numéro d'appartement :</label>
                        <input placeholder="22C" value="<?= isset($_POST['numAppart'])? $numAppart : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500" type="text" name="numAppart" id="numAppart">
                    </div>
                    
                </div>
            </div>

            <div class="flex flex-col">
                <div class="flex flex-col mt-5">
                    <label for="ville">Ville* :</label>
                    <input placeholder="Lannion" value="<?= isset($_POST['ville'])? $ville : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-200" type="text" name="ville" id="ville" required>
                    <?php 
                    if($erreurVille){ ?>
                        <p class="text-rouge">Une erreur est survenue au niveau de votre ville</p>
                    <?php } ?>
                </div>
                
                <div class="flex flex-col mt-5">
                    <label for="codePostal">Code postal* :</label>
                    <input placeholder="22300" value="<?= isset($_POST['codePostal'])? $codePostal : "" ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-200 " type="text" name="codePostal" id="codePostal" required>
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
                <a href="/html/fo/recapitulatif_commande.php?idPanier=<?php echo $idPanier ;?>" class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer flex justify-center items-center">Retour</a>
                <input class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer" type="submit" value="Suivant">
            </div>
        </form>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>