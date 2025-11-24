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
        // Récupérer les produits du vendeur
        $nbProduit = 0;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                        INNER JOIN sae3_skadjam._produit p
                            ON c.id_compte = p.id_vendeur
                        WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $produit){
            $idProduit[$nbProduit] = $produit['id_produit'];
            $libelleProduit[$nbProduit] = $produit['libelle_produit'];
            $descriptionProduit[$nbProduit] = $produit['description_produit'];
            $prixHT[$nbProduit] = $produit['prix_ht'];
            $prixTTC[$nbProduit] = $produit['prix_ttc'];
            $estMasque[$nbProduit] = $produit['est_masque'];
            $nbProduit++;
        }
        // Récupérer les promotions du vendeur
        $nbPromotion = 0;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                        INNER JOIN sae3_skadjam._promotion promo
                            ON c.id_compte = promo.id_vendeur
                        INNER JOIN sae3_skadjam._promu promu
                            ON promo.id_promotion = promu.id_promotion
                        WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $promotion){
            $idPromotion[$nbPromotion] = $promotion['id_promotion'];
            $dateDebut[$nbPromotion] = $promotion['date_debut_promotion'];
            $dateFin[$nbPromotion] = $promotion['date_fin_promotion'];
            $periodicite[$nbPromotion] = $promotion['periodicite'];
    heure_debut CHARACTER VARYING(5) NOT NULL,
    heure_fin CHARACTER VARYING(5),
    id_produit INT NOT NULL
            $nbPromotion++;
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
        <div>
            <h2>Produit.s</h2>
            <?php
            for ($i=0; $i < $nbProduit; $i++) { // Affiche tous les produits du vendeur
                echo "<h3>produit n°$i</h3>";
                echo "id : $idProduit[$i]<br>";
                echo "libelle : $libelleProduit[$i]<br>";
                echo "description : $descriptionProduit[$i]<br>";
                echo "prix HT : $prixHT[$i]<br>";
                echo "prix TTC : $prixTTC[$i]<br>";
                echo "ce produit est masqué : ";
                if($estMasque){
                    echo "oui<br>";
                }else{
                    echo "non<br>";
                }
            }
            ?>
        </div>
    </main>
</body>
</html>