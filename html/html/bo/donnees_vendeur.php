<?php
session_start();
require_once __DIR__ . "/../../01_premiere_connexion.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_back.php"; ?>
    <title>Données</title>
</head>
<body>
    <?php 
    // Connexion à la session
    $id = (int) $_SESSION["idCompte"];
    try{
        $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Récupérer toutes les infos du vendeur
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._vendeur v 
                                    ON c.id_compte = v.id_compte
                                WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $vendeur){
            $nom = $vendeur['nom_compte'];
            $prenom = $vendeur['prenom_compte'];
            $mail = $vendeur['adresse_mail'];
            $telephone = $vendeur['numero_telephone'];
            $raisonSociale = $vendeur['raison_sociale'];
            $siren = $vendeur['siren'];
            $description = $vendeur['description_vendeur'];
            $iban = $vendeur['iban'];
            $denomination = $vendeur['denomination'];
            $bloque = $vendeur['bloque'];
        }
        // Récupérer les adresses du vendeur
        $nbAdresse = 0;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                        INNER JOIN sae3_skadjam._habite h
                            ON c.id_compte = h.id_compte
                        INNER JOIN sae3_skadjam._adresse a
                            ON h.id_adresse = a.id_adresse
                        WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $adresse){
            $idCli[$nbAdresse] = $adresse['id_adresse'];
            $numRue[$nbAdresse] = $adresse['numero_rue'];
            $adressePostale[$nbAdresse] = $adresse['adresse_postale'];
            $complement[$nbAdresse] = $adresse['complement_adresse'];
            $batiment[$nbAdresse] = $adresse['numero_bat'];
            $appartement[$nbAdresse] = $adresse['numero_appart'];
            $codeInterphone[$nbAdresse] = $adresse['code_interphone'];
            $codePostal[$nbAdresse] = $adresse['code_postal'];
            $ville[$nbAdresse] = $adresse['ville'];
            $nbAdresse++;
        }

        $dbh = null;
    }catch(PDOException $e){
        echo "Erreur : " . $e->getMessage();
    }
    ?>
    <main>
        <div>
            <h2>Compte</h2>
            <?php
            echo "id : $id<br>";
            echo "prénom : $prenom<br>";
            echo "nom : $nom<br>";
            echo "numéro de téléphone : $telephone<br>";
            echo "adresse mail : $mail<br>";
            echo "raison sociale : $raisonSociale<br>";
            echo "siren : $siren<br>";
            echo "description : $description<br>";
            echo "iban : $iban<br>";
            echo "dénomination : $denomination<br>";
            echo "vous avez été bloqué : ";
            if($bloque){
                echo "oui<br>";
            }else{
                echo "non<br>";
            }
            ?>
        </div>
        <div>
            <h2>Adresse.s</h2>
            <?php
            for ($i=0; $i < $nbAdresse; $i++) { // Affiche toutes les adresses du vendeur
                echo "<h3>adresse n°$i</h3>";
                echo "id : $idCli[$i]<br>";
                echo "numéro de rue : $numRue[$i]<br>";
                echo "adresse postale : $adressePostale[$i]<br>";
                echo "complément d'adresse : $complement[$i]<br>";
                echo "batiment : $batiment[$i]<br>";
                echo "appartement : $appartement[$i]<br>";
                echo "code interphone : $codeInterphone<br>";
                echo "code postal : $codePostal[$i]<br>";
                echo "ville : $ville[$i]<br>";
            }
            ?>
        </div>
    </main>
</body>
</html>