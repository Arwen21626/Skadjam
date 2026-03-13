<?php
session_start();
require_once __DIR__ . "/verification_formulaire.php";
require_once __DIR__ . "/modification_variable.php";
require_once __DIR__ . "/../../connections_params.php";

$_SESSION['erreurs'] = [];
$_SESSION['old'] = $_POST;

$isCreation = isset($_POST['mdp'], $_POST['verifMdp']);
$isModification = !$isCreation;

// Champs communs obligatoires
$required = ['nom','prenom','pseudo','naissance','telephone','mail'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        $_SESSION['erreurs'][$field] = 'Champ obligatoire';
    }
}

// Validations communes
if (!verifNomPrenom($_POST['nom'] ?? '')) $_SESSION['erreurs']['nom'] = 'Nom invalide';
if (!verifNomPrenom($_POST['prenom'] ?? '')) $_SESSION['erreurs']['prenom'] = 'Prénom invalide';
if (!verifPseudo($_POST['pseudo'] ?? '')) $_SESSION['erreurs']['pseudo'] = 'Pseudo invalide';
if (!verifMail($_POST['mail'] ?? '')) $_SESSION['erreurs']['mail'] = 'Email invalide';
if (!verifTelephone($_POST['telephone'] ?? '')) $_SESSION['erreurs']['telephone'] = 'Téléphone invalide';
if (!verifDate($_POST['naissance'] ?? '')) $_SESSION['erreurs']['naissance'] = 'Date invalide';
if (!verifAge($_POST['naissance'] ?? '')) $_SESSION['erreurs']['age'] = 'Vous devez être majeur';

// Validation mot de passe (création uniquement)
if ($isCreation) {
    if (!verifMotDePasse($_POST['mdp'])) $_SESSION['erreurs']['mdp'] = 'Mot de passe invalide';
    if (!confirmationMotDePasse($_POST['mdp'], $_POST['verifMdp'])) $_SESSION['erreurs']['verifMdp'] = 'Les mots de passe ne correspondent pas';
}

// Erreurs -> retour formulaire
if (!empty($_SESSION['erreurs'])) {
    if($_SESSION['role'] === 'visiteur'){ // Création d'un compte
        header('Location: ../html/fo/creation_compte_client.php');
    }else{ // Modification d'un compte
        header('Location: ../html/fo/modifier_compte_client.php');
    }
    exit;
}

try {
    $dbh = new PDO("$driver:host=$server;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $nom = formatPrenom($_POST['nom']);
    $prenom = formatPrenom($_POST['prenom']);
    $mail = $_POST['mail'];
    $telephone = formatTel($_POST['telephone']);
    $pseudo = $_POST['pseudo'];
    $naissance = formatDate($_POST['naissance']);

    // Pour créer un compte client
    if ($isCreation) {
        if (!mailUnique($mail)) {
            $_SESSION['erreurs']['mail'] = 'Email déjà utilisé';
            header('Location: ../html/fo/creation_compte_client.php');
            exit;
        }

        $mdp = password_hash($_POST['mdp'], PASSWORD_DEFAULT);

        // Insertion dans la table compte
        $stmt = $dbh->prepare(
            "INSERT INTO sae3_skadjam._compte (nom_compte, prenom_compte, adresse_mail, mot_de_passe, numero_telephone, bloque)
             VALUES (?, ?, ?, ?, ?, false) RETURNING id_compte"
        );
        $stmt->execute([$nom, $prenom, $mail, $mdp, $telephone]);
        $idCompte = $stmt->fetchColumn();

        // Insertion dans la table client
        $stmt = $dbh->prepare(
            "INSERT INTO sae3_skadjam._client (id_compte, pseudo, date_naissance)
             VALUES (?, ?, ?)"
        );
        $stmt->execute([$idCompte, $pseudo, $naissance]);

        $_SESSION['idCompte'] = $idCompte;
        $_SESSION['role'] = 'client';
        
        $stmt = $dbh->prepare(
            "SELECT id_panier FROM sae3_skadjam._client WHERE id_compte = ?"
        );
        $stmt->execute([$idCompte]);
        $idPanier = $stmt->fetchColumn();

        // Insertion dans la table panier
        $stmt = $dbh->prepare(
            "INSERT INTO sae3_skadjam._panier (id_panier, nb_produit_total, montant_total_ttc, date_derniere_modif, id_client)
             VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$idPanier, $_SESSION['panier']['nb_produit_total'], $_SESSION['panier']['montant_total_ttc'], $naissance, $idCompte]);

        // Insertion dans la table futurs achats
        $stmt = $dbh->prepare(
            "INSERT INTO sae3_skadjam._futur_achat (id_produit, id_client)
             VALUES (?, ?)"
        );
        foreach ($_SESSION['futurAchat'] as $id) {
            $stmt->execute([$id,$idCompte]);
        }
        
        unset($_SESSION['old']);
        header('Location: /index.php');
        exit;
    }

    // Pour modifier un compte client
    if ($isModification) {
        $idCompte = $_SESSION['idCompte'];

        // Vérification unicité mail (sauf ancien)
        $stmt = $dbh->prepare("SELECT adresse_mail FROM sae3_skadjam._compte WHERE id_compte = ?");
        $stmt->execute([$idCompte]);
        $ancienMail = $stmt->fetchColumn();

        if ($mail !== $ancienMail && !mailUnique($mail)) {
            $_SESSION['erreurs']['mail'] = 'Email déjà utilisé';
            header('Location: ../html/fo/modifier_compte_client.php');
            exit;
        }

        // Update compte
        $stmt = $dbh->prepare(
            "UPDATE sae3_skadjam._compte
             SET nom_compte = ?, prenom_compte = ?, adresse_mail = ?, numero_telephone = ?
             WHERE id_compte = ?"
        );
        $stmt->execute([$nom, $prenom, $mail, $telephone, $idCompte]);

        // Update client
        $stmt = $dbh->prepare(
            "UPDATE sae3_skadjam._client
             SET pseudo = ?, date_naissance = ?
             WHERE id_compte = ?"
        );
        $stmt->execute([$pseudo, $naissance, $idCompte]);

        // Update adresses
        if (isset($_POST['adresse'])) {
            foreach ($_POST['adresse'] as $index => $adresse) {
                $numRue = tabAdresse($adresse['adressePostal'])[0];
                $complement = tabAdresse($adresse['adressePostal'])[1];
                $nomRue = tabAdresse($adresse['adressePostal'])[2];

                if (!verifAdresse($adresse['adressePostal'])) {
                    $_SESSION['erreurs']['adresse_'.$index] = 'Adresse invalide';
                }
                if (!verifVille($adresse['ville'])) {
                    $_SESSION['erreurs']['ville_'.$index] = 'Ville invalide';
                }
                if (!verifCp($adresse['codePostal'])) {
                    $_SESSION['erreurs']['codePostal_'.$index] = 'Code postal invalide';
                }
            }
        }

        if (!empty($_SESSION['erreurs'])) {
            header('Location: ../html/fo/modifier_compte_client.php');
            exit;
        }

        unset($_SESSION['old']);
        header('Location: ../html/fo/profil_client.php');
        exit;
    }

} catch (PDOException $e) {
    $_SESSION['erreurs']['bdd'] = 'Erreur serveur';
    header('Location: ../html/fo/creation_compte_client.php');
    exit;
}
