<?php
session_start();
require_once __DIR__ . "/../01_premiere_connexion.php";

// Vérifie si l'utilisateur est connecté
if (!isset($_SESSION["idCompte"])) {
    header("Location: " . __DIR__ . "/../../html/fo/connexion.php");
    exit();
}

// verifié si c'est l'utilisateur qui fait l'action
if (empty($_SESSION['session_confirme'])){
    $_SESSION['redirect'] = '/php/supprimer_compte_vendeur.php';
    header("Location: /html/identificationView.php");
    exit;
}

// Récupère l'ID du compte à supprimer
$id = (int) $_SESSION["idCompte"];

$id_vendeur_anonyme = 42;
$tabProduits = null;

try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    

    //Modifier id_compte de la réponse vendeur pour celui du compte anonyme
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._reponse SET id_compte = :id_anonyme WHERE id_compte = :id");
    $stmt->execute([':id_anonyme' => $id_vendeur_anonyme,
                    ':id' => $id]);

    //Récupération des produits du vendeur
    foreach($dbh->query("SELECT p.id_produit 
                        FROM sae3_skadjam._produit p 
                        WHERE p.id_vendeur = $id;"
                        , PDO::FETCH_ASSOC) as $row){
        $tabProduits[] = $row;
    }

    if($tabProduits != null){
        //Suppression des promotions en cours
            //table _promu
        foreach($tabProduits as $prod){
            $idProduit = $prod['id_produit'];
            $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._promu
                                    WHERE id_produit = :id_produit");
            $stmt->execute([':id_produit' => $idProduit]);
        }

            //table _promotion
        $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._promotion
                            WHERE id_vendeur = :id");
        $stmt->execute([':id' => $id]);   
    }
    
    //Modification produit est_supprime à true
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._produit SET est_supprime = :est_supprime WHERE id_vendeur = :id");
    $stmt->execute([':est_supprime' => true,
                    ':id' => $id]);

    //Modification facture
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._facture SET emetteur = :emetteur WHERE emetteur = :id");
    $stmt->execute([':emetteur' => $id_vendeur_anonyme,
                    ':id' => $id]);
            
    // Modification id_vendeur du produit pour celui du compte anonyme 
    $stmt = $dbh->prepare("UPDATE sae3_skadjam._produit SET id_vendeur = :id_anonyme WHERE id_vendeur = :id");
    $stmt->execute([':id_anonyme' => $id_vendeur_anonyme,
                    ':id' => $id]);

    //Récupération l'id_adresse pour supprimer l'adresse du compte vendeur
    $stmt = $dbh->prepare("SELECT a.id_adresse 
                            FROM sae3_skadjam._adresse a
                            INNER JOIN sae3_skadjam._habite h
                                ON h.id_adresse = a.id_adresse
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);
    $idAdresse = (int)$stmt->fetchColumn();

    //Suppresion du n-uplet dans _habite
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._habite
                            WHERE id_adresse = :id_adresse");
    $stmt->execute([':id_adresse' => $idAdresse]);

    //Suppression de l'adresse
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._adresse
                            WHERE id_adresse = :id_adresse");
    $stmt->execute([':id_adresse' => $idAdresse]);

    //Suppression du vendeur (table vendeur)
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._vendeur
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);

    //Suppresion du compte vendeur (table compte)
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._compte
                            WHERE id_compte = :id");
    $stmt->execute([':id' => $id]);

    // Supprime les informations de session
    session_unset();
    session_destroy();

    // Redirection vers la page d'accueil
    header("Location: ../../index.php");
    exit();
} 
catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>