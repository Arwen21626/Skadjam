<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";

require_once __DIR__ . "/../../php/verification_formulaire.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__ . "/../../php/modification_variable.php"; // fonctions qui vérifient les données des formulaires
require_once __DIR__ . "/../../../connections_params.php"; // données de connexion à la base de données

//Connection à la base de données
$dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass); 
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

$erreurs = $_SESSION['erreurs'] ?? [];
$old = $_SESSION['old'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['old']);

// Préparation des données qui vont remplir les champs du formulaire
// Récupération du comptes clients
foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._client cli
                                    ON c.id_compte = cli.id_compte
                                WHERE c.id_compte = $idCompte", PDO::FETCH_ASSOC) as $ligne){
    $nom = $ligne['nom_compte'];
    $prenom = $ligne['prenom_compte'];
    $pseudo = $ligne['pseudo'];
    $mail = $ligne['adresse_mail'];
    $naissance = $ligne['date_naissance'];
    $telephone = $ligne['numero_telephone'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<?php include __DIR__ . "/../../php/structure/head_front.php";?>
<head>
    <title>Modification de mon compte</title>
    <style>
        button a:hover {
            color: #000; 
        }
    </style>
</head>

<body>
    <?php
    // Import du header
    include __DIR__."/../../php/structure/header_front.php";
    ?>
    <main style="margin: 0" class="flex flex-col justify-center">
        <?php
        // Import de la bar de navigation
        include __DIR__."/../../php/structure/navbar_front.php";
        ?>

        <h2 class="flex justify-center text-center">Modification du compte client</h2>
        <!-- Formulaire -->
        <form class="md:flex md:flex-wrap p-15 pt-0 justify-around"  action="../../php/traitement_donnees_compte_client.php" method="post"> 
            <div class="md:flex md:flex-wrap mt-20">
                <!-- Nom -->
                <div id="nomForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="nom">Nom* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="Dupond" type="text" name="nom" id="nom" value="<?php echo $nom;?>" required>
                    <?php if (isset($erreurs['nom'])){ ?>
                        <p id="erreurNomPHP" class="text-rouge" style="font-size: 0.90em">Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents.</p>
                    <?php } ?>
                </div>

                <!-- Prenom -->
                <div id="prenomForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="prenom">Prenom* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="Jean" type="text" name="prenom" id="prenom" value="<?php echo $prenom;?>" required>
                    <?php if (isset($erreurs['prenom'])){ ?>
                        <p id="erreurPrenomPHP" class="text-rouge" style="font-size: 0.90em">Le prénom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou les accents.</p>
                    <?php } ?>
                </div>

                <!-- Pseudo -->
                <div id="pseudoForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="pseudo">Pseudo* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="Breizh22" type="text" name="pseudo" id="pseudo" value="<?php echo $pseudo;?>" required>
                    <?php if (isset($erreurs['pseudo'])){ ?>
                        <p id="erreurPseudoPHP" class="text-rouge" style="font-size: 0.90em">Le pseudo ne peut contenir que des chiffres, des majuscules, des minuscules, des tirets, des tirets du bas ou des espaces.</p>"
                    <?php } ?>
                </div>
                
                <!-- Date de naissance -->
                <div id="naissanceForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="naissance">Date de naissance* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="date" name="naissance" id="naissance" value="<?php echo formatDate($naissance);?>" required>
                    <?php if (isset($erreurs['naissance'])){ ?>
                        <p id="erreurNaissancePHP" class="text-rouge" style="font-size: 0.90em">La date de naissance doit être de la forme : jj/mm/aaaa.</p>
                    <?php } ?>
                </div>

                <!-- Téléphone -->
                <div id="telephoneForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="telephone">Telephone* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="06 12 34 56 78" type="tel" name="telephone" id="telephone" placeholder="06 04 03 02 01" pattern="0[0-9]{9}" value="<?php echo formatTel($telephone);?>" required>
                    <?php if (isset($erreurs['telephone'])){ ?>
                        <p id="erreurTelPHP" class="text-rouge" style="font-size: 0.90em">Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres.</p>
                    <?php } ?>
                </div>

                <!-- Adresse email -->
                <div id="mailForm" class="flex flex-col basis-1/3 m-5 min-w-3xs">
                    <label for="mail">Adresse email* :</label>
                    <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="jean.dupond@mail.com" type="mail" name="mail" id="mail" value="<?php echo $mail;?>" required>
                    <?php if (isset($erreurs['mail'])){ ?>
                        <p id="erreurMailPHP" class="text-rouge" style="font-size: 0.90em">L'adresse email doit être au format : adresse@e.mail</p>
                    <?php } ?>
                </div>

                <!-- Gestion des adresses -->
                <?php 
                $compteur = 1;
                foreach($dbh->query("SELECT * FROM sae3_skadjam._habite h
                                            INNER JOIN sae3_skadjam._adresse a
                                                ON a.id_adresse = h.id_adresse
                                        WHERE h.id_compte = $idCompte
                                        ORDER BY a.id_adresse ASC", PDO::FETCH_ASSOC) as $ligne){?>
                    <div class="flex flex-col md:flex-row mt-20">
                        <div class="basis-2/3 flex flex-wrap">
                            <h3 class="basis-1/1  min-w-3xs">Adresse numéro <?php echo $compteur;?></h3>
                            
                            <!-- Adresse postal -->
                            <div id="adresseForm" class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="adressePostal">Adresse :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" type="text" id="adresse" name="adresse[<?php echo $compteur?>][adressePostal]" placeholder="1 rue des Fleurs" value="<?php echo ($ligne['numero_rue'] === '') ? '' : ($ligne['numero_rue'].$ligne['complement_adresse'].' '.$ligne['adresse_postale']);?>" required>
                                <?php if (isset($erreurs['adresse_'.$compteur])){ ?>
                                    <p id="erreurAdressePHP" class="text-rouge" style="font-size: 0.90em">L'adresse postal doit être du même format que : 1 rue des fleurs</p>
                                <?php } ?>
                            </div>

                            <!-- Ville -->
                            <div id="villeForm" class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="ville">Ville :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="Lannion" type="text" id="ville" name="adresse[<?php echo $compteur?>][ville]" value="<?php echo $ligne['ville'];?>" required>
                                <?php if (isset($erreurs['ville_'.$compteur])){ ?>
                                    <p id="erreurVillePHP" class="text-rouge" style="font-size: 0.90em">La ville ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents.</p>
                                <?php } ?>
                            </div>

                            <!-- Code postal -->
                            <div id="codePostalForm" class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="cp">Code Postal :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="22300" type="text" id="codePostal" name="adresse[<?php echo $compteur?>][codePostal]" value="<?php echo $ligne['code_postal'];?>" required>
                                <?php if (isset($erreurs['codePostal_'.$compteur])){ ?>
                                    <p id="erreurCodePostalPHP" class="text-rouge" style="font-size: 0.90em">Le code postal doit être composé de 5 chiffres.</p>
                                <?php } ?>
                            </div>

                            <!-- Batiment -->
                            <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                                <label for="batiment">Batiment :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="3C" type="text" id="batiment" name="adresse[<?php echo $compteur?>][batiment]" value="<?php echo $ligne['numero_bat'];?>">
                            </div>

                            <!-- Apartement -->
                            <div class="flex flex-col m-5 basis-1/3  min-w-3xs">
                                <label for="apart">Apartement :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="22C" type="text" id="apart" name="adresse[<?php echo $compteur?>][apart]" value="<?php echo $ligne['numero_appart'];?>">                                
                            </div>

                            <!-- Interphone -->
                            <div class="flex flex-col m-5 basis-1/3 min-w-3xs">
                                <label for="interphone">Interphone :</label>
                                <input class="border-4 border-beige rounded-2xl w-1/1 p-1 pl-3 placeholder-gray-500" placeholder="1234" type="text" id="interphone" name="adresse[<?php echo $compteur?>][interphone]" value="<?php echo $ligne['code_interphone'];?>">                                
                            </div>
                        </div>
                        <div class="flex items-center md:justify-end justify-center">
                            <button class="cursor-pointer border-2 border-rouge md:rounded-2xl rounded-xl md:w-72 md:h-14 w-60 h-10 p-2 m-1 mt-5" type="button"><a href="validation_suppression_adresse.php?idAdresse=<?php echo $ligne['id_adresse'];?>">Supprimer cette adresse</a></button>
                        </div>
                    </div>
                <?php 
                    $compteur++;
                }
                ?>
            </div>
            
            <!---Ligne de boutons--->
            <div class="flex flex-col md:flex-row justify-around items-center mt-15 mb-15 w-full">
                <!---Ajouter une adresse--->
                <button class="cursor-pointer border-2 border-beige md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1" type="button">
                    <a href="./ajouter_adresse.php">Ajouter une adresse</a>
                </button>
                <!---Annuler les modifications--->
                <button class="cursor-pointer border-2 border-vertClair md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1" type="button">
                    <a href="./profil_client.php">Annuler</a>
                </button>
                <!---Valider le formulaire--->
                <input class="cursor-pointer border-2 border-vertClair md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1" type="Submit" name="submit" id="submit" value="Valider">
            </div>
        </form>

        <script src="../../js/verifForm.js"></script>
        <script>
            // initialisation
            let nom = document.getElementById("nom")
            let prenom = document.getElementById("prenom")
            let pseudo = document.getElementById("pseudo")
            let naissance = document.getElementById("naissance")
            let telephone = document.getElementById("telephone")
            let mail = document.getElementById("mail")
            let adresse = document.getElementById("adresse")
            let ville = document.getElementById("ville")
            let codePostal = document.getElementById("codePostal")

            // Verif nom
            let nomForm = document.getElementById("nomForm")
            let erreurNom = document.createElement("p")
            erreurNom.textContent = "Le nom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
            erreurNom.classList.add("md:text-rouge", "text-rouge")

            nom.addEventListener("change", function(){
                if(!verifNomPrenom(nom.value)){                    
                    erreurNom.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurNom.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                nomForm.appendChild(erreurNom)
            })

            // Verif prenom
            let prenomForm = document.getElementById("prenomForm")
            let erreurPrenom = document.createElement("p")
            erreurPrenom.textContent = "Le prenom ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
            erreurPrenom.classList.add("md:text-rouge", "text-rouge")

            prenom.addEventListener("change", function(){
                if(!verifNomPrenom(prenom.value)){                    
                    erreurPrenom.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurPrenom.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurPrenomPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                prenomForm.appendChild(erreurPrenom)
            })

            // Verif pseudo
            let pseudoForm = document.getElementById("pseudoForm")
            let erreurPseudo = document.createElement("p")
            erreurPseudo.textContent = "Le pseudo ne peut que contenir des majuscules, des minuscules, des accents, des chiffres, des tirets, tirets du bas ou des espaces."
            erreurPseudo.classList.add("md:text-rouge", "text-rouge")

            pseudo.addEventListener("change", function(){
                if(!verifPseudo(pseudo.value)){                    
                    erreurPseudo.classList.remove("md:hidden", "hidden")
                }else{
                    erreurPseudo.classList.add("md:hidden", "hidden")
                }
                
                let errPHP = document.getElementById("erreurPseudoPHP")
                if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                pseudoForm.appendChild(erreurPseudo)
            })

            // Verif naissance
            let naissanceForm = document.getElementById("naissanceForm")
            let erreurNaissance = document.createElement("p")
            erreurNaissance.textContent = "La date de naissance doit être conforme et vous devez être majeur."
            erreurNaissance.classList.add("md:text-rouge", "text-rouge")

            
            naissance.addEventListener("change", function(){
                if(!verifNaissance(naissance.value)){                 
                    erreurNaissance.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNaissancePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurNaissance.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurNaissancePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                naissanceForm.appendChild(erreurNaissance)
            })

            // Verif tel
            let telForm = document.getElementById("telephoneForm")
            let erreurTel = document.createElement("p")
            erreurTel.textContent = "Le numéro de téléphone doit commencer par 0 suivi de 9 chiffres."
            erreurTel.classList.add("md:text-rouge", "text-rouge")

            telephone.addEventListener("change", function(){
                if(!verifTelephone(telephone.value)){
                    erreurTel.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurTelPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurTel.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurTelPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                telForm.appendChild(erreurTel)
            })

            // Verif mail
            let mailForm = document.getElementById("mailForm")
            let erreurMail = document.createElement("p")
            erreurMail.textContent = "L'adresse doit être au format : adresse@e.mail"
            erreurMail.classList.add("md:text-rouge", "text-rouge")

            mail.addEventListener("change", function(){
                if(!verifMail(mail.value)){                    
                    erreurMail.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMailPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurMail.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurMailPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                mailForm.appendChild(erreurMail)
            })

            // Verif adresse
            let adresseForm = document.getElementById("adresseForm")
            let erreurAdresse = document.createElement("p")
            erreurAdresse.textContent = "L'adresse postal doit être du même format que : 1 rue des fleurs"
            erreurAdresse.classList.add("md:text-rouge", "text-rouge")

            adresse.addEventListener("change", function(){
                if(!verifAdresse(adresse.value)){                    
                    erreurAdresse.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurAdressePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurAdresse.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurAdressePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                adresseForm.appendChild(erreurAdresse)
            })

            // Verif ville
            let villeForm = document.getElementById("villeForm")
            let erreurVille = document.createElement("p")
            erreurVille.textContent = "La ville ne peut contenir que des majuscules, des minuscules, des tirets, des espaces ou des accents."
            erreurVille.classList.add("md:text-rouge", "text-rouge")

            ville.addEventListener("change", function(){
                if(!verifVille(ville.value)){                    
                    erreurVille.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurVillePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurVille.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurVillePHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                villeForm.appendChild(erreurVille)
            })

            // Verif code postal
            let codePostalForm = document.getElementById("codePostalForm")
            let erreurCodePostal = document.createElement("p")
            erreurCodePostal.textContent = "Le code postal doit être composé de 5 chiffres."
            erreurCodePostal.classList.add("md:text-rouge", "text-rouge")

            codePostal.addEventListener("change", function(){
                if(!verifCodePostal(codePostal.value)){                    
                    erreurCodePostal.classList.remove("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }else{
                    erreurCodePostal.classList.add("md:hidden", "hidden")
                    let errPHP = document.getElementById("erreurCodePostalPHP")
                    if(errPHP) errPHP.classList.add("md:hidden", "hidden")
                }
                codePostalForm.appendChild(erreurCodePostal)
            })
        </script>
    </main>

    <?php 
    // Import du footer
    include __DIR__ . "/../../php/structure/footer_front.php";

    // Fermer la connexion à la base de données
    $dbh = null;
    ?>

</body>
</html>
