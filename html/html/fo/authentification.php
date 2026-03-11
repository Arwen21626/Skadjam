<?php
ob_start(); // Démarre le tampon de sortie pour pouvoir utiliser ob_clean() plus tard
include __DIR__ . '/../../01_premiere_connexion.php'; // Connexion à la base de données
include __DIR__.'/../../php/structure/authentikATOR/AuthATOR.php';
include __DIR__.'/../../01_premiere_connexion.php';
session_start(); // Démarrage de la session

$role = null;
$clock = time();

// Vérifie que les données de connexion sont bien présentes en session
if (!isset($_SESSION['dataConnexion'])){
    header("Location: connexion.php"); // Redirige vers la page de connexion si les données sont absentes
    exit;
}

// Récupération des données de connexion stockées en session
$dataConnexion = $_SESSION['dataConnexion'];
$connecte = $dataConnexion['connecte'];
$role = $dataConnexion['role'];

// Un visiteur ne peut pas accéder à cette page
if ($role === 'visiteur'){
    header("Location: connexion.php");
    exit;
}

// Si le compte est déjà connecté (session active), redirige directement vers l'accueil
if (isset($_SESSION['connecte']) && $_SESSION['connecte']){
    header('Location: ../../../index.php');
}

// Récupération de l'id du compte connecté
$idClient = $dataConnexion['idCompte'];
$idCompte = $dataConnexion['idCompte'];





// Traitement si le formulaire A2F est validé ou si le compte est déjà connecté (pas de code secret)
if (($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['auth'] === 'valide') || $connecte){

    

    // Détecte si la requête vient d'un appel AJAX (fetch) pour adapter la réponse
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['auth'] === 'valide'){
        $ajax = true;
        $auth = new AuthATOR($dbh, "Alizon", $idCompte, "");
        $auth->resetTentative();
    }

    // Traitement spécifique aux clients : fusion du panier visiteur avec le panier du compte
    if($role === 'client'){
        
        // Début modif korentin
        // Permet d'ajouter tout les éléments du panier du visiteur au panier du compte auquel il se connecte
        if ($_SESSION['panier']['nb_produit_total'] > 0) 
        {
            // Récupère l'id du panier du client et met à jour les données du panier en BDD
            try {
                $dbh->beginTransaction();

                // Récupère l'id du panier associé au compte client
                $stmt = $dbh->prepare("SELECT id_panier FROM sae3_skadjam._client WHERE id_compte = ?");
                $stmt->execute([$idCompte]);
                $idPanier = $stmt->fetch(PDO::FETCH_ASSOC)['id_panier'];

                // Met à jour le nb de produit total contenu dans le panier
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._panier SET nb_produit_total = nb_produit_total + ? WHERE id_panier = ?");
                $stmt->execute([$_SESSION['panier']['nb_produit_total'], $idPanier]);

                // Met à jour le montant total TTC du panier
                $stmt = $dbh->prepare("UPDATE sae3_skadjam._panier SET montant_total_ttc = montant_total_ttc + ? WHERE id_panier = ?");
                $stmt->execute([$_SESSION['panier']['montant_total_ttc'], $idPanier]);
    
                // Récupère tout les id des produits contenu dans le panier du client (pour vérifier les doublons)
                $stmt = $dbh->prepare("SELECT id_produit FROM sae3_skadjam._contient WHERE id_panier = ?");
                $stmt->execute([$idPanier]);
                $listeIdProduits = $stmt->fetchAll(PDO::FETCH_COLUMN);
                $dbh->commit();

            }catch (\Throwable $e) { // Attrape TOUT : PDOException, TypeError, Error, etc.
                if ($dbh->inTransaction()) {
                    $dbh->rollBack(); // Annule la transaction en cas d'erreur
                }
                exit;
            }

            // Parcourt les produits du panier visiteur pour les fusionner avec le panier client
            foreach ($_SESSION['panier']['contient'] as $i => $produit) 
            {
                // Si le produit est présent dans le panier du compte client, on ajoute la quantité du panier visiteur
                // Faire attention dans le panier du visiteur dans le $_SESSION, le nom de la clé de l'id du produit est 'id' simple
                if (in_array($produit['id'], $listeIdProduits))
                {
                    $stmt = $dbh->prepare("UPDATE sae3_skadjam._contient SET quantite_par_produit = quantite_par_produit + ? WHERE id_produit = ? AND id_panier = ?");
                    $stmt->execute([$produit['quantite_par_produit'], $produit['id'], $idPanier]);
                }
                else // Si le produit n'est pas présent, on insert le produit dans la table contient avec la quantité
                {
                    $stmt = $dbh->prepare("INSERT INTO sae3_skadjam._contient (id_produit, id_panier, quantite_par_produit) VALUES (?, ?, ?)");
                    $stmt->execute([$produit['id'], $idPanier, $produit['quantite_par_produit']]);
                }
            }

            // Supprime le panier du visiteur en session maintenant qu'il a été fusionné
            unset($_SESSION['panier']);
        }  
    }
    
    // Initialisation pour une redirection sur le produit si on écrivais un avis par exemple et qu'on devait se connecter
    $idProduit = 0;
    if(isset($dataConnexion['idProduit'])){
        $idProduit = $dataConnexion['idProduit'];
    }

    // Enregistrement de l'identité du compte en session
    $_SESSION["idCompte"] = $idCompte;
    $_SESSION["role"] = $role;
    $_SESSION["connecte"] = true; // Marque le compte comme connecté pour éviter de repasser par l'A2F

    ob_clean(); // Vide tout ce qui a été affiché avant d'envoyer le JSON
    header('Content-Type: application/json');

    // Redirection suivant le role
    if($role == 'vendeur'){
        // Les vendeurs sont redirigés vers leur espace back-office
        if ($ajax) {
            echo json_encode(['url' => '../bo/index_vendeur.php']);
        } else {
            header('Location: ../bo/index_vendeur.php');
        }
        exit;
    }
    else{
        // Si on était sur un produit alors redirection dessus
        if (isset($dataConnexion['veutAcheter'])) {
            // Le client voulait acheter : redirection vers le panier
            $_SESSION['veutAcheter'] = "V";
            if ($ajax) {
                echo json_encode(['url' => '../fo/panier.php']);
            } else {
                header('Location: ../fo/panier.php');
            }
            exit;
        }
        else if($_SESSION['role'] == 'client' && $idProduit != 0){
            // Le client venait d'une page produit : redirection vers ce produit
            if ($ajax){
                echo json_encode(['url' => '../fo/details_produit.php?idProduit='.$idProduit]);
            } else {
                header('Location: ../fo/details_produit.php?idProduit='.$idProduit);
            }
            exit;
        }
        else{
            // Cas par défaut : redirection vers la page d'accueil
            if ($ajax){
                echo json_encode(['url' => '../../../index.php']);
            } else {
                header('Location: ../../../index.php');
            }
            exit;
        }
    }
} else {

$auth = new AuthATOR($dbh, "Alizon", $idCompte, "");
$restant = $auth->getTempsRestant();
    // Affichage du formulaire A2F si le code n'a pas encore été validé
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__.'/../../php/structure/head_front.php' ?>
    <title>auth A2F</title>
</head>
<body class="show">
    <?php include __DIR__.'/../../php/structure/header_front.php' ?>
    <main class="flex flex-col">
        
        <h2>Authentification à deux facteurs</h2>
        <?php
        if ($restant>$clock){ 
            ?>
        
        <p>Compte bloqué, réessayez dans <?= $restant ?>.</p>
        <?php } ?>

        <?php include __DIR__.'/../../php/structure/authentikATOR/input_code.php' ?>
        <p id="result" class="hidden"></p>
    </main>
    <?php include __DIR__.'/../../php/structure/footer_front.php' ?>
</body>
<script src="./../../php/structure/authentikATOR/appelAJAX.js"></script>
<script>
    nbTentative = 0
    const res = document.getElementById("result");
    let ret 
    let reponse
    goFirst()

    async function submit(idClient){
        res.classList.add("hidden")
        initParam(idClient)
        let code = recup_code()
        ret = await verifOtp(code) // Vérifie le code OTP saisi par l'utilisateur
        console.log("connection : "+ret)
        if (ret == 0){ // Code correct
            valider.textContent = "Connexion..."
            res.textContent = "Code bon."
            res.classList.remove("hidden")

            // Envoi de la confirmation au PHP via AJAX pour finaliser la connexion
            data = new FormData()
            data.append('auth', 'valide')

            await fetch('authentification.php', {method: 'post', body: data})
            .then(r => {
                return r.json() // Récupère l'URL de redirection renvoyée par le PHP
            })
            .then(r => {
                window.location.href = r.url // Redirige le navigateur vers l'URL reçue
            })
        } else { // Code incorrect
            res.textContent = "Code incorrect, réessayez."
            res.classList.remove("hidden")
            nbTentative++
            if (nbTentative==3){
                ret = await addTempsRestant()
                window.location.href = "./authentification"
            }
        }
    }
</script>
</html>
<?php } ?>