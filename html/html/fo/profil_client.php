<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";

?>
<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
<head>
    <title>Mon profil</title>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="min-h-[650x]">
        <?php
        // Vérifier si le client est connecter
        if(isset($_SESSION["idCompte"])) {
            // Connexion à la session
            $id = (int) $_SESSION["idCompte"];

            try{
                $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
                $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

                // Récupérer toutes les infos du client
                foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                        INNER JOIN sae3_skadjam._client cli 
                                            ON c.id_compte = cli.id_compte
                                        WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $client){
                    $nom = htmlentities($client['nom_compte']);
                    $prenom = htmlentities($client['prenom_compte']);
                    $pseudo = htmlentities($client['pseudo']);
                    $mail = htmlentities($client['adresse_mail']);
                    $naissance = htmlentities($client['date_naissance']);
                    $telephone = htmlentities($client['numero_telephone']);
                }
                // Récupérer les adresses du client
                $nbAdresse = 0;
                foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                        INNER JOIN sae3_skadjam._habite h
                                            ON c.id_compte = h.id_compte
                                        INNER JOIN sae3_skadjam._adresse a
                                            ON h.id_adresse = a.id_adresse
                                        WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $adresse){
                    $numRue[$nbAdresse] = htmlentities($adresse['numero_rue']);
                    $adressePostale[$nbAdresse] = !empty($adresse['adresse_postale']) ? htmlentities($adresse['adresse_postale']) : '';
                    $complement[$nbAdresse] = !empty($adresse['complement_adresse']) ? htmlentities($adresse['complement_adresse']) : '';
                    $batiment[$nbAdresse] = !empty($adresse['numero_bat']) ? " " . htmlentities($adresse['numero_bat']) : '';
                    $appartement[$nbAdresse] = !empty($adresse['numero_appart']) ? " " . htmlentities($adresse['numero_appart']) : '';
                    $codePostal[$nbAdresse] = !empty($adresse['code_postal']) ? htmlentities($adresse['code_postal']) : '';
                    $ville[$nbAdresse] = !empty($adresse['ville']) ? htmlentities($adresse['ville']) : '';
                    $nbAdresse++;
                }
                $dbh = null;
            }catch(PDOException $e){
                echo "Erreur : " . $e->getMessage();
            }
            ?>
            <h2 class="text-center mt-4 mb-4">Mon profil</h2>
            <div class="flex justify-center">
                <table class="mx-3 table-auto w-180">
                    <tbody>
                        <tr class="py-4">
                            <th class="py-3 w-45 md:w-90"><h3 class="text-left">Pseudo :</h3></th>
                            <td class="py-3"><h3><?php echo $pseudo; ?></h3></td>
                        </tr>
                        <tr class="py-4">
                            <th class="py-3"><h3 class="text-left">Prénom et nom :</h3></th>
                            <td class="py-3"><h4><?php echo $prenom; ?> <?php echo $nom; ?></h4></td>
                        </tr>
                        <tr class="py-4">
                            <th class="py-3"><h3 class="text-left">Date de naissance :</h3></th>
                            <td class="py-3"><p><?php echo $naissance; ?></p></td>
                        </tr>
                        <?php if($nbAdresse != 0){ ?>
                            <tr class="py-4">
                                <th class="py-3"><h3 class="text-left">Adresse(s) :</h3></th>
                                <td class="py-3">
                                    <?php for ($i=0; $i < $nbAdresse; $i++) { // Affiche toutes les adresses du client 
                                        $j=$i+1;?>
                                    <p> n°<?php echo "$j : $numRue[$i] $complement[$i] $adressePostale[$i]$batiment[$i]$appartement[$i], $codePostal[$i] $ville[$i]"; ?></p>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="py-4">
                            <th class="py-3"><h3 class="text-left"><abbr title="Numéro">N°</abbr> de téléphone :</h3></th>
                            <td class="py-3"><p><?php echo $telephone; ?></p></td>
                        </tr>
                        <tr class="py-4">
                            <th class="py-3"><h3 class="text-left">Adresse mail :</h3></th>
                            <td class="py-3"><p><?php echo $mail; ?></p></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="md:mt-15 md:mb-15 mt-7 mb-7">
                <!-- Première ligne -->
                <div class="flex flex-col md:flex-row md:justify-center md:mb-5 items-center">
                    <!-- Supprimer le compte du client -->
                    <form action="suppression_client.php" method="post">
                        <input class="border-rouge border-2 md:mr-[5em] md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1 cursor-pointer" type="submit" value="Supprimer mon compte">
                    </form>
                    
                    <!-- Modifier les informations du client (sauf le mot de passe) -->
                    <form action="modifier_compte_client.php" method="post">
                        <input class="border-vertClair border-2 md:mr-[5em] md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1 cursor-pointer" type="submit" value="Modifier mes informations">
                    </form>

                    <!-- Modifier le mot de passe du client -->
                    <form action="nouveau_mdp.php">
                        <?php $_SESSION['adresse_mail'] = $mail; ?>
                        <input class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1 cursor-pointer" type="submit" value="Modifier mon mot de passe">    
                    </form>
                </div>

                <!-- Deuxième ligne -->
                <div class="flex flex-col md:flex-row md:justify-center items-center">
                    <!-- Déconnexion -->
                    <a href="/php/deconnexion.php" class="border-vertClair border-2 md:mr-[5em] md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1 cursor-pointer flex justify-center items-center">
                        <button>Se déconnecter</button>
                    </a>


                    <!-- A2F -->
                    <a href="/html/pass_A2F.php" class="border-vertClair border-2 md:rounded-2xl rounded-xl md:w-72 w-60 md:h-14 h-10 p-2 m-1 cursor-pointer flex justify-center items-center">
                        <button>Connexion <abbr title="Authentification à deux facteurs">A2F</abbr></button>
                    </a>
                    
                    <!--    Récupérer mes données
                    <a href="donnees_client.php" class="underline! absolute right-4 bottom-41 md:bottom-14 cursor-pointer hover:text-rouge">Demander mes données</a>
                    -->
                </div>
            </div>

        <?php
        }else{
            // Si non connecté, l'emmener à la page de connexion à la place
            header("Location: connexion.php");
            exit();
        }
        ?>
    </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
