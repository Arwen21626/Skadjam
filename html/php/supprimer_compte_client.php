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
    $_SESSION['redirect'] = '/php/supprimer_compte_client.php';
    header("Location: /html/identificationView.php");
    exit;
}

// Récupère l'ID du compte à supprimer
$id = (int) $_SESSION["idCompte"];

try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Copier les avis dans le compte anonyme
    $stmt = $dbh->prepare("SELECT * FROM sae3_skadjam._avis WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $avis = $stmt->fetchAll();

       
    foreach ($avis as $a) {
        // Insérer l'avis du compte en train d'être supprimé dans le compte anonyme
        $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._avis (nb_etoile, nb_pouce_haut, nb_pouce_bas, contenu_commentaire, id_produit, id_compte) 
                                VALUES (:nb_etoile, :nb_pouce_haut, :nb_pouce_bas, :contenu_commentaire, :id_produit, 41)
                                RETURNING id_avis");

        $stmt->execute([':nb_etoile' => $a['nb_etoile'],
                        ':nb_pouce_haut' => $a['nb_pouce_haut'],
                        ':nb_pouce_bas' => $a['nb_pouce_bas'],
                        ':contenu_commentaire' => $a['contenu_commentaire'],
                        ':id_produit' => $a['id_produit']]);

        $nouvelIdAvis = $stmt->fetchColumn();

        // Copier les réponses
        $stmtRep = $dbh->prepare("SELECT * FROM sae3_skadjam._reponse WHERE id_avis = :ancien_id");
        $stmtRep->execute([':ancien_id' => $a['id_avis']]);
        $reponses = $stmtRep->fetchAll();

        // Pour chaque réponse trouvée, on l'insère dans les avis du compte anonyme
        foreach ($reponses as $r) {
            $stmtInsertRep = $dbh->prepare("INSERT INTO sae3_skadjam._reponse (contenu_reponse, id_avis, id_compte)
                                            VALUES (:contenu, :nouvel_id, :id_vendeur)");

            $stmtInsertRep->execute([':contenu' => $r['contenu_reponse'], ':nouvel_id' => $nouvelIdAvis, ':id_vendeur' => $r['id_compte']]);
        }
    }

    // Supprime le compte du client
    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._reponse r
                            USING sae3_skadjam._avis a
                            WHERE r.id_avis = a.id_avis
                            AND a.id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._avis WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._habite WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._futur_achat WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._carte_bancaire WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._panier WHERE id_client = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._client WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $stmt = $dbh->prepare("DELETE FROM sae3_skadjam._compte WHERE id_compte = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Supprime les informations de session
    session_unset();
    session_destroy();

    // Redirection vers la page d'accueil
    header("Location: ../../index.php");
    exit();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>