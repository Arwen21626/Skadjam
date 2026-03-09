<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";

if($_SESSION['role'] != 'client'){
    header('Location: /index.php');
}else{
    include __DIR__ . "/../../connexion_recupraptor.php";
    include __DIR__ . '/../../php/verification_formulaire.php';
    include __DIR__ . '/../../01_premiere_connexion.php';
    
    // Initialisation des variables
    $achatValide = false;
    $erreurNumero = false;
    $erreurExpiration = false;
    $erreurCryptogramme = false;
    $erreurNom = false;
    $erreurCarteCadeau = false;
    $erreurCodePromo = false;
    $enregistrerCarte = false;
    $idCompte = $_SESSION['idCompte'];

    // Chope l'attribut Get du script vider panier
    if (isset($_GET["achatValide"])) {
        $achatValide = $_GET["achatValide"];
    }else{
        /*// Récupérer la carte bancaire si enregistrée
        $commande = "SELECT * FROM sae3_skadjam._carte_bancaire WHERE id_client = ?";
        $stmt = $dbh->prepare($commande);
        $stmt->execute([$idCompte]);
        $carteBancaire = $stmt->fetch(PDO::FETCH_ASSOC);
        // Récupérer la date d'expiration
        $dateExpiration = explode('/', $carteBancaire['expiration']);*/

        //Récupération du panier
        $commande = "SELECT id_panier FROM sae3_skadjam._panier WHERE id_client = ?";
        $stmt = $dbh->prepare($commande);
        $stmt->execute([$idCompte]);
        $idPanier = $stmt->fetch(PDO::FETCH_ASSOC)['id_panier'];
        $sql = "SELECT 
                pr.libelle_produit,
                pr.id_produit,
                pr.id_vendeur,
                v.raison_sociale,
                c.quantite_par_produit,
                pr.prix_ht,
                pr.prix_ttc,
                pr.prix_remise,
                pr.prix_ttc * c.quantite_par_produit as sous_total_ttc,
                pr.prix_ht * c.quantite_par_produit as sous_total_ht,
                p.montant_total_ttc,
                p.nb_produit_total
            FROM sae3_skadjam._panier p
            INNER JOIN sae3_skadjam._contient c
                ON c.id_panier = p.id_panier
            INNER JOIN sae3_skadjam._produit pr
                ON pr.id_produit = c.id_produit
            INNER JOIN sae3_skadjam._vendeur v
                ON v.id_compte = pr.id_vendeur
            WHERE p.id_panier = :id_panier
        ";
    
        $stmt = $dbh->prepare($sql);
        $stmt->execute([':id_panier' => $idPanier]);
        $tabInfosPanier = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if (empty($tabInfosPanier)) {
            die("Erreur : panier vide : " . $idPanier);
        }
    
        if(isset($_POST['numero']) && !$achatValide){
        // Initialisation des variables
            
    
            $numero = htmlentities($_POST['numero']);
            $mois = htmlentities($_POST['mois']);
            $annee = htmlentities($_POST['annee']);
            $expiration = $mois . '/' . $annee;
            $cryptogramme = htmlentities($_POST['cryptogramme']);
            $nom = htmlentities($_POST['nom']);
            $idAdr = $_GET['idAdresse'];
    
            
    
            if(isset($_POST['enregistrerCarte'])){
                $enregistrerCarte = htmlentities($_POST['enregistrerCarte']);
            }
    
            if(isset($_POST['codePromo'])){
                $codePromo = htmlentities($_POST['codePromo']);
            }
            
            if(isset($_POST['carteCadeau'])){
                $carteCadeau = htmlentities($_POST['carteCadeau']);
            }
    
            //echo $_POST['expiration']; // 22/25
            if(!verifExpiration($expiration)){
                $erreurExpiration = true;
            }
            if(!verifNomPrenom($nom)){
                $erreurNom = true;
            }
            if(!verifNumCarte($numero)){
                $erreurNumero = true;
            }

            // Si case cochée -> enregistrement de la carte bancaire
            /*if(isset($enregistrerCarte) && $enregistrerCarte == 'on'){
                $numeroHasher = password_hash($numero, PASSWORD_DEFAULT);
                $cryptogrammeHasher = password_hash($cryptogramme, PASSWORD_DEFAULT);

                $nouvCarte = $dbh->prepare("INSERT INTO sae3_skadjam._carte_bancaire(numero_carte, cryptogramme, nom, expiration, id_client) 
                                            VALUES(?, ?, ?, ?, ?)");
                $nouvCarte->execute([$numeroHasher, $cryptogrammeHasher, $nom, $expiration, $idCompte]);
            }*/

            // S'il n'y a pas d'erreurs -> procéder à la commande
            if(!$erreurCryptogramme && !$erreurExpiration && !$erreurNom && !$erreurNumero || $achatValide){
                $achatValide = true;
    
                //Insertion de la commande
                $date_char = date("d/m/Y");
                $sqlCommande = "INSERT INTO sae3_skadjam._commande (etat, date_commande, montant_total_ttc, id_client, id_adresse)
                                VALUES (:etat, :date_commande, :montant_total_ttc, :id_client, :id_adresse)
                                RETURNING id_commande";
    
                $stmtCommande = $dbh->prepare($sqlCommande);

                if (!$stmtCommande->execute([
                    ':etat' => 'En attente',
                    ':date_commande' => $date_char,
                    ':montant_total_ttc' => $tabInfosPanier[0]['montant_total_ttc'],
                    ':id_client' => $idCompte,
                    ':id_adresse' => $idAdr
                ])){
                    throw new Exception("id_client : " . $idCompte);
                }
                $idCommande = $stmtCommande->fetchColumn();
                /*
                //creation numéro de suivi
                try{
                    $id_suivi = $rpr->create_bord($idCommande, "alizon");
                    if ($etat = $rpr->get_etat($id_suivi)){

                        $commande = "UPDATE sae3_skadjam._commande SET id_suivi = ?, etat = ? WHERE id_commande = ?";
                        
                        //recuperation de l'etat de la commande
                        $stmt = $dbh->prepare($commande);
                        if (!$stmt->execute([$id_suivi, $etat, $idCommande])){
                            throw new Exception("insertion numero de suivi et etat");
                        }
                    }
                } catch (Exception $e){
                    
                }
                */
                if (!$idCommande) {
                    throw new Exception("id_commande non récupéré");
                }

                //Insertion dans la table donne (lien entre panier et commande)
                $sqlDonne = "INSERT INTO sae3_skadjam._donne (id_panier, id_commande)
                            VALUES (:id_panier, :id_commande)";
    
    
                $stmtDonne = $dbh->prepare($sqlDonne);

                $stmtDonne->execute([
                    ':id_panier' => $idPanier,
                    ':id_commande' => $idCommande
                ]);
                
                header("location:/php/vider_panier.php?typeVider=achat&achatValide=" . $achatValide);
    
            }
            
        }
    }

}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php include __DIR__ . '/../../php/structure/head_front.php';?>
    <title>Paiement</title>
    <link rel="stylesheet" href="../../css/fo/fil_d_ariane.css">
    <style>
        button a:hover{
            color : black;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../../php/structure/header_front.php';?>
    <?php include __DIR__ . '/../../php/structure/navbar_front.php';?>
    <?php if(!$achatValide){?>
        <main class="">
            <!---fil d'ariane processus d'achat--->
            <ul class="etapes-processus-achat">
                <li class="fait" etape="1">Récapitulatif commande</li>
                <li class="fait" etape="2">Adresse de livraison</li>
                <li class="actuel" etape="3">Paiement</li>
                <li etape="4">Commande validée</li>
            </ul>

            <h2>Paiement</h2>

            <form method="post">
                <div class="flex flex-col md:items-center items-start ml-5 md:ml-0">
                    <div class="flex flex-col mb-5 mt-5">
                        <label for="numero">Numéro de carte* :</label>
                        <input placeholder="0000 1111 2222 3333" maxlength="19" value="<?php
                        if(isset($_POST['numero'])){ 
                            echo $numero; 
                        }/*else if(!empty($carteBancaire['numero_carte'])) {
                            echo $carteBancaire['numero_carte'];
                        }*/ ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-100 w-75" type="text" name="numero" id="numero" required>
                        <?php
                            if($erreurNumero){ ?>
                                <p class="text-rouge"><?php echo "Le numéro n'est pas bon";?></p>
                        <?php } ?>
                    </div>
                    
                    <div class="flex flex-col mb-5">
                        <div class="flex flex-col w-100">
                            <label for="expiration">Date d'expiration* :</label>
                            <p class="flex flex-row">
                                <input placeholder="MM" value="<?php 
                                    if(isset($_POST['mois'])){
                                        echo $mois; 
                                    }/*else if(!empty($dateExpiration[0])) {
                                        echo $dateExpiration[0];
                                    }*/ ?>" maxlength="2" pattern="0[1-9]|1[0-2]" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-15" type="text" name="mois" id="mois" required>
                                /
                                <input placeholder="AA" value="<?php 
                                    if(isset($_POST['annee'])){ 
                                        echo $annee; 
                                    }/*else if(!empty($dateExpiration[1])) {
                                        echo $dateExpiration[1];
                                    }*/ ?>" maxlength="2" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-15" type="text" name="annee" id="annee" required>
                            </p>
                            <?php if($erreurExpiration){ ?>
                                <p class="text-rouge"><?php echo "La date n'est pas bonne";?></p>
                            <?php } ?>
                        </div>
                        
        
                        <div class="flex flex-col mt-5">
                            <label for="cryptogramme">Cryptogramme* :</label>
                            <input placeholder="000" pattern="[0-9]{3}" value="<?php 
                            if(isset($_POST['cryptogramme'])){ 
                                echo $cryptogramme; 
                            }/*else if(!empty($carteBancaire['cryptogramme'])) {
                                echo $carteBancaire['cryptogramme'];
                            }*/ ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 w-50" type="text" name="cryptogramme" id="cryptogramme" required>

                            <?php if($erreurCryptogramme){ ?>
                                <p class="text-rouge"><?php echo "Le cryptogramme n'est pas bon";?></p>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="flex flex-col mb-5">
                        <label for="nom">Nom du titulaire* :</label>
                        <input placeholder="M Alizon" value="<?php
                        if(isset($_POST['nom'])){ 
                            echo $nom; 
                        }/*else if(!empty($carteBancaire['nom'])) {
                            echo $carteBancaire['nom'];
                        }*/ ?>" class="pl-2 border-4 border-vertClair rounded-xl placeholder-gray-500 md:w-100 w-75 ml-0" type="text" name="nom" id="nom" required>

                        <?php if($erreurNom){ ?>
                                <p class="text-rouge"><?php echo "Le nom n'est pas bon";?></p>
                        <?php } ?>
                    </div>
                    <!--<div class="md:w-100 md:ml-5 ml-0">
                        <label for="enregistrerCarte">Enregistrer cette carte pour les prochains paiements?</label>
                        <input type="checkbox" name="enregistrerCarte" id="enregistrerCarte" class="w-5 h-5 mt-1" <?php if(isset($_POST['enregistrerCarte'])) echo 'checked'; ?>>
                    </div> -->
        
                    <!-- <div class="flex flex-row mb-5">
                        <div class="flex flex-col">
                            <label for="codePromo">Code promotionnel :</label>
                            <input placeholder="" value="<?= isset($_POST['codePromo'])? $codePromo : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-50" type="text" name="codePromo" id="codePromo">
                            
                            <?php if($erreurCodePromo){ ?>
                                <p class="text-rouge"><?php echo "Le code promo n'est pas bon";?></p>
                            <?php } ?>
                        </div>
            
                        <div class="flex flex-col ml-6">
                            <label for="carteCadeau">Code carte cadeau :</label>
                            <input placeholder="" value="<?= isset($_POST['carteCadeau'])? $carteCadeau : "" ?>" class="border-4 border-vertClair rounded-2xl placeholder-gray-500 w-50" type="text" name="carteCadeau" id="carteCadeau">
                            
                            <?php if($erreurCarteCadeau){ ?>
                                <p class="text-rouge"><?php echo "La carte cadeau n'est pas valide";?></p>
                            <?php } ?>
                        </div>
                    </div> -->
                </div>
                <div class="flex flex-row justify-center">
                    <button class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5"><a href="../fo/adresse.php">Retour</a></button>
                    <input class="border-vertClair border-2 rounded-xl w-40 h-14 cursor-pointer m-5" type="submit" value="Suivant">
                </div>
            </form>
        </main>
    <?php }else{ ?>
        <main class="text-center min-h-[500px]">
            <div class="mt-10">
                <!---fil d'ariane processus d'achat--->
                <ul class="etapes-processus-achat">
                    <li class="fait" etape="1">Récapitulatif commande</li>
                    <li class="fait" etape="2">Adresse de livraison</li>
                    <li class="fait" etape="3">Paiement</li>
                    <li class="actuel" etape="4">Commande validée</li>
                </ul>

                <h1 class="pl-2 pr-2">Votre achat a bien été validé</h1>
                <div class="flex flex-rox justify-center mt-3">
                    <a href="../../index.php"><button class="border-vertClair border-2 rounded-2xl w-40 h-15 cursor-pointer m-7">Retour à l'accueil</button></a>
                    <a href="liste_commandes.php"><button class="border-vertClair border-2 rounded-2xl w-40 h-15 cursor-pointer m-7">Liste de mes commandes</button></a>
                </div>
                
            </div>
        </main>
    <?php }?>
    <?php include(__DIR__ . '/../../php/structure/footer_front.php');?>
</body>
</html>