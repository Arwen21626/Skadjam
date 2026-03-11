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

    // Chercher dans la BDD si l'adresse à été sauvegardé
    $adresseExistante = $dbh->prepare("SELECT al.* FROM sae3_skadjam._adresse_livraison al
                                        INNER JOIN sae3_skadjam._commande c
                                            ON al.id_adresse = c.id_adresse
                                        WHERE c.id_client = ?
                                        ORDER BY c.id_commande DESC LIMIT 1");
    $adresseExistante->execute([$idClient]);
    $adresseE = $adresseExistante->fetch(PDO::FETCH_ASSOC);

    $save = $adresseE['sauvegarde'] ?? '';

    if(!$save) {
        $adresseE = [];
    }

    if(isset($_POST['nom'])){
        include __DIR__ . '/../../php/verification_formulaire.php';

        $nom = htmlentities($_POST['nom']);
        $prenom = htmlentities($_POST['prenom']);
        $adresse = htmlentities($_POST['adresse']);
        $ville = htmlentities($_POST['ville']);
        $codePostal = htmlentities($_POST['codePostal']);

        $numBat = !empty($_POST['numBat']) ? $_POST['numBat'] : null;

        $numAppart = !empty($_POST['numAppart']) ? $_POST['numAppart'] : null;

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

        // Si tout est bon -> redirection vers la page paiement
        if(!$erreurNom && !$erreurPrenom && !$erreurAdresse && !$erreurVille && !$erreurCodePostal){
            $nouvAdrLivraison = $dbh->prepare("INSERT INTO sae3_skadjam._adresse_livraison(nom, prenom, adresse_postale, complement_adresse, numero_rue, numero_bat, numero_appart, code_postal, ville) 
                                                VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?) RETURNING id_adresse");
                $nouvAdrLivraison->execute([$nom, $prenom, $adresseExplode[2], $adresseExplode[1], $adresseExplode[0],
                                    $numBat, $numAppart, $codePostal, $ville]);
            $idAdresse = $nouvAdrLivraison->fetchColumn();

            // Si case cochée -> enregistrement adresse
            if(isset($_POST['enregistrerAdr']) && $_POST['enregistrerAdr'] == 'on'){
                $nouvAdr = $dbh->prepare("UPDATE sae3_skadjam._adresse_livraison
                                        SET sauvegarde = true
                                        WHERE id_adresse = ?");

                $nouvAdr->execute([$idAdresse]);
            }
            header('Location: /html/fo/paiement.php?idPanier=' . $idPanier.'&idAdresse='.$idAdresse);
        }

    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adresse</title>
    <link rel="stylesheet" href="../../css/fo/fil_d_ariane.css">
</head>
<?php include __DIR__ . '/../../php/structure/head_front.php'; ?>
<body>
    <?php include __DIR__ . '/../../php/structure/header_front.php'; ?>        
    <?php include __DIR__ . '/../../php/structure/navbar_front.php'; ?>
    <main class="flex flex-col justify-center">
        
        <!---fil d'ariane processus d'achat--->
        <ul class="flex list-none p-0 m-8">
            <!-- étape faite -->
            <li etape="1"
                class="relative flex-1 text-center py-2
                before:content-[attr(etape)] before:block before:w-[30px] before:h-[30px]
                before:mx-auto before:mb-2 before:leading-[30px]
                before:rounded-full before:bg-vertClair before:text-white before:font-bold
                after:content-[''] after:absolute after:top-[22px] after:-right-1/2
                after:w-full after:h-[4px] after:bg-vertClair after:-z-10">
                Récapitulatif <span class="hidden md:block">commande</span>
            </li>

            <!-- étape actuelle -->
            <li etape="2"
                class="relative flex-1 text-center py-2
                before:content-[attr(etape)] before:block before:w-[50px] before:h-[50px]
                before:mx-auto before:mb-2 before:-mt-[10px] before:leading-[50px]
                before:rounded-full before:bg-vertMoyen before:text-white before:font-bold
                after:content-[''] after:absolute after:top-[22px] after:-right-1/2
                after:w-full after:h-[4px] after:bg-[#ccc] after:-z-10">
                Adresse <span class="hidden md:block">de livraison</span>
            </li>

            <!-- étape à faire -->
            <li etape="3"
                class="relative flex-1 text-center py-2
                before:content-[attr(etape)] before:block before:w-[30px] before:h-[30px]
                before:mx-auto before:mb-2 before:leading-[30px]
                before:rounded-full before:bg-[#ccc] before:text-white before:font-bold
                after:content-[''] after:absolute after:top-[22px] after:-right-1/2
                after:w-full after:h-[4px] after:bg-[#ccc] after:-z-10">
                Paiement
            </li>

            <!-- dernière étape -->
            <li etape="4"
                class="relative flex-1 text-center py-2
                before:content-[attr(etape)] before:block before:w-[30px] before:h-[30px]
                before:mx-auto before:mb-2 before:leading-[30px]
                before:rounded-full before:bg-[#ccc] before:text-white before:font-bold
                after:hidden">
                Commande validée
            </li>
        </ul>

        <h2>Adresse de livraison</h2>

        <form class="flex flex-col self-center p-10" method="post">
            
            <div class="flex flex-col md:flex-row justify-between">
                <div id="nomForm" class="flex flex-col max-w-70">
                    <label for="nom">Nom* :</label>
                    <input placeholder="Cobrec" value="<?php 
                        if(isset($_POST['nom'])){
                            echo $_POST['nom'];
                        }else if(!empty($adresseE['nom'])){
                            echo $adresseE['nom'];
                        }else{
                            echo "";
                        }
                    ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 max-w-70" type="text" name="nom" id="nom" required>
                    <?php 
                    if($erreurNom){ ?>
                        <p id="erreurNomPHP" class="text-rouge">Votre nom ne peut contenir que des lettres majuscules, minuscules, tirets, espaces et des accents</p>
                    <?php } ?>
                </div>

                <div id="prenomForm" class="flex flex-col max-w-70">
                    <label for="prenom">Prénom* :</label>
                    <input placeholder="Alizon" value="<?php
                        if(isset($_POST['prenom'])){
                            echo $_POST['prenom'];
                        }else if(!empty($adresseE['prenom'])){
                            echo $adresseE['prenom'];
                        }else{
                            echo "";
                        }
                    ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 max-w-70" type="text" name="prenom" id="prenom" required>
                    <?php 
                    if($erreurPrenom){ ?>
                        <p id="erreurPrenomPHP" class="text-rouge">Votre prenom ne peut contenir que des lettres majuscules, minuscules, tirets, espaces et des accents</p>
                    <?php } ?>
                </div>
            </div>
            
            <div id="adresseForm" class="flex flex-col mt-5">
                <label for="adresse">Adresse postale* :</label>
                <input placeholder="1 rue des fleurs" value="<?php
                    if(isset($_POST['adresse'])){
                        echo $_POST['adresse'];
                    }else if(!empty($adresseE['adresse_postale'])){
                        echo $adresseE['numero_rue'] . " " . $adresseE['adresse_postale'] . " " . $adresseE['complement_adresse'];
                    }else{
                        echo "";
                    }
                ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-100 md:w-200" type="text" name="adresse" id="adresse" required>
                <?php 
                if($erreurAdresse){ ?>
                    <p id="erreurAdressePHP" class="text-rouge w-100 md:w-200">
                        Votre adresse postal ne peut contenir que des chiffres, lettres majuscules ou minuscules, 
                        virgules et espaces.
                    </p>
                <?php } ?>
            </div>
            
            <div class="flex flex-col mt-5">
                <div class="flex flex-col md:flex-row md:justify-between">
                    <div class="flex md:self-center flex-col">
                        <label for="numBat">Numéro de bâtiment :</label>
                        <input placeholder="3C" value="<?php
                            if(isset($_POST['numBat'])){
                                echo $_POST['numBat'];
                            }else if(!empty($adresseE['numero_bat'])){
                                echo $adresseE['numero_bat'];
                            }else{
                                echo "";
                            }
                        ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-70 w-50" type="text" name="numBat" id="numBat">
                    </div>
                    
    
                    <div class="flex flex-col">
                        <label for="numAppart">Numéro d'appartement :</label>
                        <input placeholder="22C" value="<?php
                            if(isset($_POST['numAppart'])){
                                echo $_POST['numAppart'];
                            }else if(!empty($adresseE['numero_appart'])){
                                echo $adresseE['numero_appart'];
                            }else{
                                echo "";
                            }
                        ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-70 w-50" type="text" name="numAppart" id="numAppart">
                    </div>
                    
                </div>
            </div>

            <div class="flex flex-col">
                <div id="villeForm" class="flex flex-col mt-5">
                    <label for="ville">Ville* :</label>
                    <input placeholder="Lannion" value="<?php
                        if(isset($_POST['ville'])){
                            echo $_POST['ville'];
                        }else if(!empty($adresseE['ville'])){
                            echo $adresseE['ville'];
                        }else{
                            echo "";
                        }
                    ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-200 w-40" type="text" name="ville" id="ville" required>
                    <?php 
                    if($erreurVille){ ?>
                        <p id="erreurVillePHP" class="text-rouge">Une erreur est survenue au niveau de votre ville</p>
                    <?php } ?>
                </div>
                
                <div id="codePostalForm" class="flex flex-col mt-5">
                    <label for="codePostal">Code postal* :</label>
                    <input placeholder="22300" value="<?php
                        if(isset($_POST['codePostal'])){
                            echo $_POST['codePostal'];
                        }else if(!empty($adresseE['code_postal'])){
                            echo $adresseE['code_postal'];
                        }else{
                            echo "";
                        }
                    ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-200 w-40" type="text" name="codePostal" id="codePostal" required>
                    <?php 
                    if($erreurCodePostal){ ?>
                        <p id="erreurCodePostalPHP" class="text-rouge">Votre code posal doit se composer de 5 chiffres</p>
                    <?php } ?>
                </div>
            </div>

            <div class="flex flex-row mt-5">
                <label for="enregistrerAdr" class="mr-5">Enregistrer cette adresse ?</label>
                <input type="checkbox" name="enregistrerAdr" id="enregistrerAdr" class="w-5 h-5 mt-1" <?php if($save) echo 'value="on" checked'; ?>>
            </div>

            <div class="flex flex-row mt-5 mb-10 justify-between">
                <a href="/html/fo/recapitulatif_commande.php?idPanier=<?php echo $idPanier ;?>" class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer flex justify-center items-center">Retour</a>
                <input class="border-vertClair border-2 rounded-2xl w-40 h-14 cursor-pointer" type="submit" value="Suivant">
            </div>
        </form>



        <script src="../../js/verifForm.js"></script>
        <script>
            
            // initialisation
            let nom = document.getElementById("nom")
            let prenom = document.getElementById("prenom")
            let adresse = document.getElementById("adresse")
            let numBat = document.getElementById("numBat")
            let numAppart = document.getElementById("numAppart")
            let ville = document.getElementById("ville")
            let codePostal = document.getElementById("codePostal")


            // Verif nom
            let nomForm = document.getElementById("nomForm")
            let erreurNom = document.createElement("p")
            erreurNom.textContent = "Votre nom ne peut contenir que des lettres majuscules, minuscules, tirets, espaces et des accents"
            erreurNom.classList.add("md:text-rouge")

            nom.addEventListener("change", function(){
                if(!validerNomPrenom(nom.value)){                    
                    erreurNom.classList.remove("md:hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurNom.classList.add("md:hidden")
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                nomForm.appendChild(erreurNom)
            })

            // Verif prenom
            let prenomForm = document.getElementById("prenomForm")
            let erreurPrenom = document.createElement("p")
            erreurPrenom.textContent = "Votre prenom ne peut contenir que des lettres majuscules, minuscules, tirets, espaces et des accents"
            erreurPrenom.classList.add("md:text-rouge", "md:text-sm")

            prenom.addEventListener("change", function(){
                if(!validerNomPrenom(prenom.value)){                    
                    erreurPrenom.classList.remove("md:hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurPrenom.classList.add("md:hidden")
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                prenomForm.appendChild(erreurPrenom)
            })

            // Verif adresse
            let adresseForm = document.getElementById("adresseForm")
            let erreurAdresse = document.createElement("p")
            erreurAdresse.textContent = "Votre adresse postal ne peut contenir que des chiffres, lettres majuscules ou minuscules, virgules et espaces"
            erreurAdresse.classList.add("md:text-rouge", "text-rouge", "w-100", "md:w-200")

            adresse.addEventListener("change", function(){
                if(!validerAdresse(adresse.value)){                    
                    erreurAdresse.classList.remove("md:hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurAdresse.classList.add("md:hidden")
                    let errPHP = document.getElementById("erreurAdressePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                adresseForm.appendChild(erreurAdresse)
            })

            // Verif ville
            let villeForm = document.getElementById("villeForm")
            let erreurVille = document.createElement("p")
            erreurVille.textContent = "Votre ville ne peut contenir que des lettres majuscules, minuscules, tirets, espaces et des accents"
            erreurVille.classList.add("md:text-rouge","text-rouge", "w-100", "md:w-200")

            ville.addEventListener("change", function(){
                if(!validerVille(ville.value)){                    
                    erreurVille.classList.remove("md:hidden")
                    let errPHP = document.getElementById("erreurVillePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurVille.classList.add("md:hidden")
                    let errPHP = document.getElementById("erreurVillePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                    
                }
                villeForm.appendChild(erreurVille)
            })

            // Verif code postal
            let codePostalForm = document.getElementById("codePostalForm")
            let erreurCodePostal = document.createElement("p")
            erreurCodePostal.textContent = "Votre code posal doit se composer de 5 chiffres"
            erreurCodePostal.classList.add("md:text-rouge", "text-rouge", "w-100", "md:w-200")

            codePostal.addEventListener("change", function(){
                if(!validerCodePostal(codePostal.value)){                    
                    erreurCodePostal.classList.remove("md:hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurCodePostal.classList.add("md:hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                codePostalForm.appendChild(erreurCodePostal)
            })

        </script>
    </main>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>