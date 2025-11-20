<?php
session_start();
require_once __DIR__ . "/../../01_premiere_connexion.php";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Données</title>
</head>
<body>
    <?php 
    // Connexion à la session
    //$id = (int) $_SESSION["idCompte"];
    $id = 4;
    try{
        $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname",$user,$pass);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Récupérer toutes les infos du client
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._client cli 
                                    ON c.id_compte = cli.id_compte
                                INNER JOIN sae3_skadjam._panier p
                                    ON cli.id_panier = p.id_panier
                                INNER JOIN sae3_skadjam._carte_bancaire cb
                                    ON c.id_compte = cb.id_client
                                WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $client){
            $nom = $client['nom_compte'];
            $prenom = $client['prenom_compte'];
            $pseudo = $client['pseudo'];
            $mail = $client['adresse_mail'];
            $naissance = $client['date_naissance'];
            $telephone = $client['numero_telephone'];
            $bloque = $client['bloque'];
            $idPanier = $client['id_panier'];
            $nbProduit = $client['nb_produit_total'];
            $montantTTC = $client['montant_total_ttc'];
            $dateDerniereModif = $client['date_derniere_modif'];
            $idCarte = $client['id_carte_bancaire'];
            $numeroCarte = $client['numero_carte'];
            $cryptogramme = $client['cryptogramme'];
            $nomCarte = $client['nom'];
            $expiration = $client['expiration'];
        }
        // Récupérer les adresses du client
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
            echo "pseudo : $pseudo<br>";
            echo "prénom : $prenom<br>";
            echo "nom : $nom<br>";
            echo "date de naissance : $naissance<br>";
            echo "numéro de téléphone : $telephone<br>";
            echo "adresse mail : $mail<br>";
            echo "vous avez été bloqué : ";
            if($bloque){
                echo "true<br>";
            }else{
                echo "false<br>";
            }
            ?>
        </div>
        <div>
        <h2>Adresse.s</h2>
            <?php
            for ($i=0; $i < $nbAdresse; $i++) { // Affiche toutes les adresses du client
                echo "<h3>adresse n°$i</h3>";
                echo "id : $idCli[$i]<br>";
                echo "numéro de rue : $numRue[$i]<br>";
                echo "adresse postale : $adressePostale[$i]<br>";
                echo "complément d'adresse : $complement[$i]<br>";
                echo "batiment : $batiment[$i]<br>";
                echo "appartement : $appartement[$i]<br>";
                echo "code interphone : $codeInterphone";
                echo "code postal : $codePostal[$i]<br>";
                echo "ville : $ville[$i]<br>";
            }
            ?>
        </div>
        <div>
            <h2>Informations bancaires</h2>
            <?php
            echo "id : $idCarte<br>";
            echo "numéro de carte : $numeroCarte<br>";
            echo "cryptogramme : $cryptogramme<br>";
            echo "nom sur la carte : $nomCarte<br>";
            echo "date d'expiration : $expiration<br>";
            ?>
        </div>
        <div>
            <h2>Panier</h2>
            <?php
            echo "id : $idPanier<br>";
            echo "nombre de produit dans votre panier : $nb_produit_total<br>";
            echo "montant total TTC : $montantTTC<br>";
            echo "date de la dernière modification : $dateDerniereModif<br>";
            ?>
        </div>
    </main>
</body>
</html>