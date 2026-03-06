<?php
    session_start();

    if (!isset($_SESSION['role'])){
        $_SESSION['role'] = 'visiteur';
    }

    $erreur = false;
    include __DIR__ . '/../../01_premiere_connexion.php';
    if(isset($_POST['mdp']) && isset($_POST['mail'])){
        // Initialisation des données
        $erreur = false;
        $mail = htmlentities($_POST["mail"]);
        $mdp = htmlentities($_POST["mdp"]);

        // Récupération des données de la bdd pour tester la connexion
        $stmt = $dbh->prepare("SELECT id_compte, mot_de_passe FROM sae3_skadjam._compte WHERE adresse_mail = ?");
        $stmt->execute([$mail]);
        $tab = $stmt->fetch(PDO::FETCH_ASSOC);

        if($tab){
            // Vérification mot de passe    
            $passCorrect = password_verify($mdp, $tab['mot_de_passe']);
    
            if ($passCorrect){
                // Initialisation de la session après confirmation du mot de passe
                $_SESSION['idCompte'] = $tab['id_compte'];
    
                // Récupération des données de la bdd pour voir si c'est un vendeur ou un client
                $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._vendeur WHERE id_compte = ?");
                $stmt->execute([$_SESSION['idCompte']]);
                $role = $stmt->fetch(PDO::FETCH_ASSOC);
                $_SESSION['role'] = 'vendeur';
                
                // si l'id du compte n'est pas dans vendeur
                if($role == null){
                    $stmt = $dbh->prepare("SELECT id_compte FROM sae3_skadjam._client WHERE id_compte = ?");
                    $stmt->execute([$_SESSION['idCompte']]);
                    $role = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Début modif korentin
                    // Permet d'ajouter tout les éléments du panier du visiteur au panier du compte auquel il se connecte
                    if ($role !== null)
                    {
                        if ($_SESSION['panier']['nb_produit_total'] > 0) 
                        {
                            // Récupère l'id du panier du client
                            $stmt = $dbh->prepare("SELECT id_panier FROM sae3_skadjam._client WHERE id_compte = ?");
                            $stmt->execute([$tab['id_compte']]);
                            $idPanier = $stmt->fetch(PDO::FETCH_ASSOC)['id_panier'];

                            // Met à jour le nb de produit total contenu dans le panier
                            $stmt = $dbh->prepare("UPDATE sae3_skadjam._panier SET nb_produit_total = nb_produit_total + ? WHERE id_panier = ?");
                            $stmt->execute([$_SESSION['panier']['nb_produit_total'], $idPanier]);

                            // Met à jour le montant total TTC du panier
                            $stmt = $dbh->prepare("UPDATE sae3_skadjam._panier SET montant_total_ttc = montant_total_ttc + ? WHERE id_panier = ?");
                            $stmt->execute([$_SESSION['panier']['montant_total_ttc'], $idPanier]);

                            // Récupère tout les id des produits contenu dans le panier
                            $stmt = $dbh->prepare("SELECT id_produit FROM sae3_skadjam._contient WHERE id_panier = ?");
                            $stmt->execute([$idPanier]);
                            $listeIdProduits = $stmt->fetchAll(PDO::FETCH_COLUMN);
                            
                            foreach ($_SESSION['panier']['contient'] as $i => $produit) 
                            {
                                // Si le produit est présent dans le panier du compte client, on ajoute la quantité du panier visiteur
                                if (in_array($produit['id'], $listeIdProduits)) // Faire attention dans le panier du visiteur dans le $_SESSION, le nom de la clé de l'id du produit est 'id' simple
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

                            // Supprimer le panier du visiteur 
                            unset($_SESSION['panier']);


                        }
                        

                        $_SESSION['role'] = 'client';
                    }
                    // Fin modif
                }
    
                // Initialisation pour une redirection sur le panier si le visiteur voulait acheter son panier et qu'il devait se connecter
                if (isset($_POST['veutAcheter']))
                {
                    $_SESSION['veutAcheter'] = "V";
                }
                
                // Initialisation pour une redirection sur le produit si on écrivais un avis par exemple et qu'on devait se connecter
                $idProduit = 0;
                if(isset($_POST['idProduit'])){
                    $idProduit = $_POST['idProduit'];
                }

                // Redirection suivant le role
                if($_SESSION['role'] == 'vendeur'){
                    header('Location: ../bo/index_vendeur.php');
                    exit;
                }
                else{
                    // Si on était sur un produit alors redirection dessus
                    if (isset($_SESSION['veutAcheter'])) {
                        header('Location: ../fo/panier.php');
                        exit;
                    }
                    else if($_SESSION['role'] == 'client' && $idProduit != 0){
                        header('Location: ../fo/details_produit.php?idProduit='.$idProduit);
                        exit;
                    }
                    else{
                        header('Location: ../../../index.php');
                        exit;
                    }
                }
                
            }
            else{
            // Erreur détecté dans l'adresse mail ou le mot de passe
            $erreur = true;
            }
        }
        else{
            // Erreur détecté dans l'adresse mail ou le mot de passe
            $erreur = true;
        }
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require_once __DIR__ . "/../../php/structure/head_front.php"?>
    <title>Connexion</title>
</head>
<body>
    <?php require_once __DIR__ . "/../../php/structure/header_front.php"; ?>
    <main class="flex flex-col self-center md:min-h-[615px]">
        <h2>Connexion</h2>
        <form method="post">
        <?php if(isset($_GET['idProduit'])){ ?>
            <input name="idProduit" id="idProduit" value="<?php echo $_GET['idProduit'];?>" class="hidden w-1">
        
        <?php }?>
        <?php if (isset($_POST['veutAcheter'])) {?> 
            <input type="hidden" name="veutAcheter" value="V">
        <?php } ?>

            <div class="flex flex-col items-center ">
                <div class="flex flex-col items-start space-y-4">
                    <!--Champ mail  -->
                    <div class="flex flex-col md:w-[550px]">
                        <label for="mail">Adresse mail :</label>
                        <input class="cursor:default border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " type="text" name="mail" id="mail" value="<?= isset($_POST['mail'])? $_POST['mail'] : "" ?>" required>
                    </div>

                    <!-- Champ MDP -->
                    <div class="flex flex-col md:w-[550px]">
                        <label for="mdp">Mot de passe :</label>
                        <div class="zone-mdp flex flex-row">
                            <!--le flex row sert a alinger l'oeil me demander pas pourquoi (signé Arwen et svp touchez plus) -->
                            <input id="mdp" type="password" class="champ-mdp border-4 border-solid rounded-2xl border-vertClair pl-3 w-70 md:w-[500px] h-15 " name="mdp" id="mdp"  value="<?= isset($_POST['mdp'])? $_POST['mdp'] : "" ?>" required>
                            <?php include __DIR__ . "/../../php/structure/bouton_mdp.php" ?>
                        </div>

                        <!-- Renvoie sur la page de réinitialisation de mot de passe -->
                        <a href="reinitialiser_mdp.php" class="underline self-end cursor-pointer hover:text-rouge">Mot de passe oublié ?</a>
                    </div>
                </div>
                
                <div class=" flex w-fit flex-col mt-6 items-center ">
                    <!-- Si erreur détecté -->
                    <?php if($erreur){ ?>
                        <p class="text-rouge items-center"><?php echo 'adresse mail ou mot de passe invalide';?></p>
                    <?php }?>
                </div>

                <!-- Boutons -->
                <div class="flex flex-row space-x-10">
                    <div class=" justify-self-center">
                        <!-- Boutton de retour à l'index.php -->
                        <a href="/index.php"><button class="cursor-pointer w-35 md:w-60 h-10 md:h-12 border-5 border-solid rounded-xl md:rounded-2xl border-vertClair pl-3" type="button">Annuler</button></a>
                    </div>
                    <div class=" justify-self-center">
                        <!-- Envoie des données en méthode POST pour se connecter -->
                        <input type="submit" value="Se connecter" class="cursor-pointer w-35 md:w-60 h-10 md:h-12 border-5 border-solid rounded-xl md:rounded-2xl border-vertClair pl-3 mb-4">
                    </div>
                </div>
            </div>
        </form>
        <!-- Renvoie sur la page de création d'un compte client -->
        <?php if (isset($_POST['veutAcheter'])) { //Modification pour rediriger vers le panier si le visiteur se crée un compte pour valider son panier?>
            <div class="flex flex-row flex-wrap justify-center m-2">
                <p class="mr-2">Pas encore client ? </p>
                <a href="./creation_compte_client.php?veutAcheter=V" class="underline! hover:text-rouge">Créer un compte client</a>
            </div>
        <?php } else { ?>
            <div class="flex flex-row flex-wrap justify-center m-2">
                <p class="mr-2">Pas encore client ? </p>
                <a href="./creation_compte_client.php" class="underline! hover:text-rouge">Créer un compte client</a>
            </div>
        <?php } ?>
        <!-- Renvoie sur la page de création d'un compte vendeur -->
        <div class="flex flex-row flex-wrap justify-center m-2">
            <p class=" mr-2">Pas encore vendeur ? </p>
            <a href="../bo/crea_compte_vendeur.php" class="underline! hover:text-rouge">Créer un compte vendeur</a>
        </div>
    </main>
    <?php require_once __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
<script src="../../js/bo/visibilite_mdp.js"></script>
</html>
