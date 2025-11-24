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
            $nom = $client['nom_compte'];
            $prenom = $client['prenom_compte'];
            $pseudo = $client['pseudo'];
            $mail = $client['adresse_mail'];
            $naissance = $client['date_naissance'];
            $telephone = $client['numero_telephone'];
            $bloque = $client['bloque'];
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
        // Récupérer les informations du panier du client
        foreach($dbh->query("SELECT * FROM sae3_skadjam._client cli
                                INNER JOIN sae3_skadjam._panier p
                                    ON cli.id_panier = p.id_panier
                                WHERE cli.id_compte = $id", PDO::FETCH_ASSOC) as $panier){
            $idPanier = $panier['id_panier'];
            $dateDerniereModif = $panier['date_derniere_modif'];
        }
        // Récupérer les informations bancaires du client
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._carte_bancaire cb
                                    ON c.id_compte = cb.id_client
                                WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $carteB){
            $idCarte = $carteB['id_carte_bancaire'];
            $numeroCarte = $carteB['numero_carte'];
            $cryptogramme = $carteB['cryptogramme'];
            $nomCarte = $carteB['nom'];
            $expiration = $carteB['expiration'];
        }
        // Récupérer les informattions des commandes du client
        $nbCommande = 0;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._client cli
                                INNER JOIN sae3_skadjam._commande co
                                    ON cli.id_compte = co.id_client
                                INNER JOIN sae3_skadjam._details d
                                    ON co.id_commande = d.id_commande
                                INNER JOIN sae3_skadjam._produit prod
                                    ON d.id_produit = prod.id_produit
                                WHERE cli.id_compte = $id", PDO::FETCH_ASSOC) as $commande){
            $idCo[$nbCommande] = $commande['id_commande'];
            $etat[$nbCommande] = $commande['etat'];
            $dateCo[$nbCommande] = $commande['date_commande'];
            $montantCoTTC[$nbCommande] = $commande['montant_total_ttc'];
            $idFacture[$nbCommande] = $commande['id_facture'];
            $quantite[$nbCommande] = $commande['quantite'];
            $sousTotal[$nbCommande] = $commande['sous_total'];
            $idProdCo[$nbCommande] = $commande['id_produit'];
            $nbCommande++;
        }
        // Récupérer les informattions des avis du client
        $nbAvis = 0;
        foreach($dbh->query("SELECT * FROM sae3_skadjam._compte c
                                INNER JOIN sae3_skadjam._avis av
                                    ON c.id_compte = av.id_compte
                                WHERE c.id_compte = $id", PDO::FETCH_ASSOC) as $avis){
            $idAvis[$nbAvis] = $avis['id_avis'];
            $nbEtoile[$nbAvis] = $avis['nb_etoile'];
            $contenu[$nbAvis] = $avis['contenu_commentaire'];
            $idProdAv[$nbAvis] = $avis['id_produit'];
            $nbAvis++;
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
                echo "oui<br>";
            }else{
                echo "non<br>";
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
                echo "code interphone : $codeInterphone<br>";
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
            echo "date de la dernière modification : $dateDerniereModif<br>";
            ?>
        </div>
        <div>
            <h2>Commande.s</h2>
            <?php
            for ($i=0; $i < $nbCommande; $i++) { // Affiche toutes les commande du client
                echo "<h3>commande n°$i</h3>";
                echo "id : $idCo[$i]<br>";
                echo "etat : $etat[$i]<br>";
                echo "date de la commande : $dateCo[$i]<br>";
                echo "montant total TTC : $montantCoTTC[$i]<br>";
                echo "id de la facture : $idFacture[$i]<br>";
                echo "quantité : $quantite[$i]<br>";
                echo "sous total : $sousTotal<br>";
                echo "id du produit : $idProd[$i]<br>";
                echo "libelle du produit : $libelleProd[$i]<br>";
                echo "id du vendeur : $idVendeur[$i]<br>";
            }
            ?>
        </div>
        <div>
            <h2>Avis</h2>
            <?php
            for ($i=0; $i < $nbAvis; $i++) { // Affiche tous les avis du client
                echo "<h3>avis n°$i</h3>";
                echo "id : $idAvis[$i]<br>";
                echo "nombre d'étoiles : $nbEtoile[$i]<br>";
                echo "contenu de l'avis : <p>$contenu[$i]</p><br>";
                echo "id du produit : $idProdAv[$i]<br>";
            }
            ?>
        </div>
    </main>
</body>
</html>