<?php
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    require(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];
    $avisAjoute = (isset($_GET['avisAjouter']) && $_GET['avisAjouter'] === "1");
    $avisSupprime = (isset($_GET['avisSupprimer']) && $_GET['avisSupprimer'] === "1");
    $panierAjoute = (isset($_GET['panierAjouter']) && $_GET['panierAjouter'] === "1");

    if (!isset($_GET["idProduit"])) {
        header("location:/404.php");
    }

    // Requête pour récupérer les infos du produit
    $produit = "vide";
    
    foreach($dbh->query("SELECT id_categorie, id_vendeur, libelle_produit, prix_ttc,prix_remise, 
                            quantite_stock, description_produit, 
                            note_moyenne, pourcentage_remise
                         FROM sae3_skadjam._produit pr
                            left join sae3_skadjam._reduit rd
                                on rd.id_produit = pr.id_produit
                            left join sae3_skadjam._remise r
                                on r.id_remise = rd.id_remise
                         WHERE pr.id_produit = $idProd 
                            AND pr.est_supprime = false 
                            AND pr.est_masque = false"
                        , PDO::FETCH_ASSOC) as $row){
        $produit = $row;
    }

    if ($produit === "vide") 
    {
        header("location:/404.php");
    }
    else
    {
        // Requête pour récupérer les infos de la catégorie du produit
        foreach ($dbh->query("SELECT libelle_categorie
                                        FROM sae3_skadjam._categorie ca
                                        WHERE ca.id_categorie =" . $produit["id_categorie"], 
                                        PDO::FETCH_ASSOC) as $row) {
            $categorie = $row;
        };

        // Requête pour récupérer les infos du vendeur du produit
        foreach ($dbh->query("  SELECT *
                                FROM sae3_skadjam._vendeur ven
                                WHERE ven.id_compte =" . $produit["id_vendeur"], 
                                        PDO::FETCH_ASSOC) as $row) {
            $vendeur = $row;
        };

        // Requête pour récupérer l'url de la photo
        foreach ($dbh->query("  SELECT url_photo, alt, titre
                                FROM sae3_skadjam._montre
                                INNER JOIN sae3_skadjam._photo
                                ON _photo.id_photo = _montre.id_photo
                                WHERE _montre.id_produit =" . $idProd, 
                                        PDO::FETCH_ASSOC) as $row) {
            $infoPhoto = $row;
        };
        
        // Définition des variables PHP pour récupérer chaque donnée nécessaire
        $libelleProd = $produit["libelle_produit"]; // Nom du produit
        $libelleCat = $categorie["libelle_categorie"]; //Libellé de la catégorie
        $prixTTC = str_replace(".", ",", $produit["prix_ttc"]); // Prix du produit
        $prixRemise = str_replace(".",",", $produit["prix_remise"]); // Prix du produit remiser
        $produitStock = $produit["quantite_stock"]; // Récupère le stock du produit pour savoir si il est disponible ou non
        $nomVendeur = $vendeur["raison_sociale"];
        $produitDesc = $produit["description_produit"];
        $noteMoy = $produit["note_moyenne"];
        $pourcentage = $produit['pourcentage_remise'];

        // Définition du lien vers lequel est renvoyé le client en cliquant sur le bouton ajouter au panier
        // Si il est connecté : le produit est ajouté à son panier
        //Si il n'est pas connecté : le visiteur est renvoyé sur la page de connexion

        $lienBtnAjouterPanier = "/php/ajouter_panier.php";

        // signalement d'un avis
        if (isset($_GET['signal']) && $_GET['signal'] === "true"){
            $idAvis = $_GET['idAvis'];
            $idCompte = $_SESSION['idCompte'];
            
            $updateAvis = $dbh->prepare("UPDATE sae3_skadjam._avis SET signaler = 'true' WHERE id_avis = ?");
            $updateAvis->execute([$idAvis]);

            // si un client ou un vendeur signale un commentaire
            if (!($_SESSION['role'] === 'visiteur')){
                $insertAsignaler = $dbh->prepare("INSERT INTO sae3_skadjam._a_signaler VALUES (?, ?)");
                $insertAsignaler->execute([$idAvis, $idCompte]);
            }
            //si un visiteur signale un commentaire
            else{
                $_SESSION['avis'][$idAvis] = "signaler";
            }
        }
    }
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require(__DIR__ . "/../../php/structure/head_front.php") ?>
    <title> <?php echo $produit["libelle_produit"] ?></title>
</head>
<body>
    <?php require(__DIR__ . "/../../php/structure/header_front.php"); ?>
    <?php require(__DIR__ . "/../../php/structure/navbar_front.php"); ?>

    <div id="popup-overlay" class="right-12 md:right-40">
        <!---popup ajout d'un produit dans le panier--->
        <?php if ($panierAjoute): ?>
            <div id="popup-ajouter-panier" class="popup p-4 border-vertFonce shadow-xl">
                <p>Le produit a bien été ajouté à votre panier !</p>
                <div class="flex justify-around mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                    <a href="/html/fo/panier.php" class="a-button pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">Voir le panier</a>
                </div>
            </div>
        <?php endif; ?>

        <!---popup ajout d'un avis--->
        <?php if ($avisAjoute): ?>
            <div id="popup-ajouter-avis" class="popup p-4 border-vertFonce shadow-xl">
                <p>Votre avis a bien été ajouté !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
        <?php endif; ?>

        <!---popup suppression avis--->
        <?php if ($avisSupprime): ?>
            <div id="popup-supprimer-avis" class="popup p-4 border-vertFonce shadow-xl">
                <p>Votre avis a bien été supprimé !</p>
                <div class="flex justify-center mt-2">
                    <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <main class="p-4 md:pl-8 pr-8">
        <!-- Section Description -->
        <section class="flex flex-col ">
            <article class="p-2 md:pb-8"> <!-- Titrage -->
                <h3> <?php echo $libelleProd; ?></h3>
                <p class="ml-4">Catégorie : <?php echo $libelleCat; ?></p>
                <div class="ml-10"> <?php echo affichageNote($noteMoy); ?> </div>
            </article>
            
            <article class="md:flex md:flex-row md:justify-around">
                <img src="<?php echo $infoPhoto["url_photo"]; ?>"  alt=<?php echo $infoPhoto["alt"] ?> title=<?php echo $infoPhoto["titre"] ?>
                class="size-1/2 border
                       md:size-1/4">

                <div class="p-2 flex flex-col items-start md:items-center">
                    <div class="flex md:flex-col md:mb-4">
                        <h3 class="text-center pr-2 self-center <?php echo ($pourcentage !== NULL)?'line-through':'';?>"> <?php echo $prixTTC ?>€</h3>
                        <h3 class="text-center pr-2 self-center <?php echo ($pourcentage !== NULL)?'':'hidden';?>"> <?php echo $prixRemise ?>€</h3>

                        <p class="text-center pl-2 mt-1 self-center<?php echo ($produitStock > 0) ? '' : ' text-rouge font-bold'; ?>">
                            <?php
                                if ($produitStock > 0) { // Le stock est supérieur à 0, le produit est disponible
                                    echo "Disponible"; 
                                }
                                else { // Pour 
                                    echo "Indisponible";
                                }
                            ?>
                        </p>
                    </div>
                    
                    <div class="flex md:flex-col">
                        <p class="text-center pr-1 md:p-0">Vendu par</p>
                        <p class="text-center font-medium pl-1 md:p-0"><?php echo $nomVendeur ?></p>
                    </div>
                    
                    <form method="post" action="<?php echo $lienBtnAjouterPanier ?>" >
                        <input type="hidden" name="idProduit" value="<?php echo $idProd ?>">
                        <input type="hidden" name="pageDeRetour" value="details">
                        <button class="bg-beige rounded-2xl w-40 h-14 mt-4 cursor-pointer hover:text-rouge"
                        type="submit">
                            Ajouter au panier
                        </button>
                    </form>
                </div>
            </article>
        </section>

        <!-- Section Description détaillée -->
        <section class="md:mt-4">
            <h3>Description détaillée</h3>
            <p class="pt-2 pb-4">
                <?php echo $produitDesc; ?>
            </p>
        </section>

        <!-- Section avis -->
        <section>
            <h3>Avis</h3>
            <div class="flex flex-col-reverse items-center mt-0 md:items-start md:justify-between md:mt-10 md:flex-row">
                <?php 
                // tableau contenant tous les avis
                $avis = [];
                foreach($dbh->query("SELECT * FROM sae3_skadjam._avis a 
                                    INNER JOIN sae3_skadjam._client c 
                                        ON a.id_compte = c.id_compte 
                                    WHERE id_produit = $idProd", PDO::FETCH_ASSOC) as $row){
                    $avis[] = $row;
                }
                
                if($avis == null){?>
                    <p class=" md:ml-24">Aucun avis associé à ce produit.</p>
                <?php }
                else{?>

                <!-- Commentaire -->
                
                <section class=" md:ml-32">
                    <?php foreach($avis as $row){
                        if ($row['contenu_commentaire'] != ''){
                            $idAvis = $row['id_avis'];
                            $stmt = $dbh->prepare("SELECT id_avis, raison_sociale, contenu_reponse FROM sae3_skadjam._reponse r
                                                    INNER JOIN sae3_skadjam._vendeur v
                                                        ON r.id_compte = v.id_compte 
                                                    WHERE id_avis = ?");
                            $stmt->execute([$idAvis]);
                            $reponse = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $aReponse = (isset($reponse['id_avis']))?true:false;
                            ?>
                            <section class=" bg-bleu m-4 p-4 md:w-4xl w-100 <?php echo $aReponse?'mb-0 rounded-t-2xl':'rounded-2xl'?>">

                                <div class="flex justify-between items-center py-2">
                                    <h4>
                                        <?php echo $row['pseudo'];?>
                                    </h4>

                                    <div class="">
                                        <?php echo affichageNote($row['nb_etoile']);?>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center gap-1">
                                            <p><?php echo $row['nb_pouce_haut'] ?? 0; ?></p>
                                            <img class="w-6 h-6" src="../../images/logo/bootstrap_icon/hand-thumbs-up.svg" 
                                                    alt="icône pouce vers le haut si vous avez aimé l'avis" 
                                                    title="J'aime cet avis">
                                        </div>
                                    
                                        <div class="flex items-center gap-1">
                                        <p><?php echo $row['nb_pouce_bas'] ?? 0; ?></p>
                                        <img class="w-6 h-6" src="../../images/logo/bootstrap_icon/hand-thumbs-down.svg" 
                                                alt="icône pouce vers le bas si vous n'avez pas aimé l'avis" 
                                                title="Je n'aime pas cet avis">
                                        </div>
                                    </div>
                                    

                                    

                                    <?php // savoir si l'utilisateur à déjà signaler l'avis il ne faut pas qu'il puisse le resignaler
                                    $aSignaler = false;
                                    //pour le visiteur
                                    if ($_SESSION['role'] === 'visiteur'){
                                        if (isset($_SESSION['avis'][$row['id_avis']])){
                                            $aSignaler = true;
                                        }
                                    }
                                    // pour les personne connecter à un compte client ou vendeur
                                    elseif($_SESSION['role'] === 'client' || $_SESSION['role'] === 'vendeur'){
                                        $idAvis = $row['id_avis'];
                                        $idCompte = $_SESSION['idCompte'];
                                        foreach($dbh->query("SELECT id_avis, id_compte 
                                                                FROM sae3_skadjam._a_signaler s 
                                                                WHERE id_compte = $idCompte AND id_avis = $idAvis
                                                            UNION
                                                            SELECT id_avis, id_compte 
                                                                FROM sae3_skadjam._avis a 
                                                                WHERE id_compte = $idCompte  AND id_avis = $idAvis;", PDO::FETCH_ASSOC) as $avisSignalable){
                                            $aSignaler = true;
                                        }
                                    }
                                    // affichage ou pas du boutton signaler
                                    if (!$aSignaler){?>
                                        <a class="text-black" href="./details_produit.php?idProduit=<?php echo $idProd;?>&signal=true&idAvis=<?php echo $row['id_avis']?>">Signaler</a>
                                    <?php }?>
                                </div>
                                <p><?php echo $row['contenu_commentaire'];?></p>     
                            </section>
                            <?php if(isset($reponse["id_avis"])){?>
                            <section class=" bg-beige m-4 mt-0 p-4 md:w-4xl w-100 rounded-b-2xl">
                                <div class="grid grid-cols-4 md:grid-cols-5 justify-items-end w-auto">
                                    <h4 class="mr-4 col-span-2 md:col-span-3 justify-self-start">
                                        <?php echo $reponse['raison_sociale']; ?>
                                    </h4>
                                </div>
                                <p><?php echo $reponse['contenu_reponse'];?></p>     
                            </section>
                            <?php }?>
                        <?php }
                    }?>
                </section>
                <?php }?>

                <div class="md:sticky md:top-48 h-full flex flex-nowrap flex-col items-center md:items-start" >
                    <!-- Ajouter un avis -->
                    <button class="bg-beige rounded-2xl w-48 h-14 mt-4 mb-4 md:mr-16 hover:text-rouge">
                        
                        <?php if (($_SESSION['role'] === 'client')){
                            // si le client est connecter
                            
                            // savoir si le client a déjà donner son avis sur le produit
                            $dejaAvis = false;
                            foreach($avis as $row){
                                if ($_SESSION['idCompte'] == $row['id_compte']){
                                    $dejaAvis = true;
                                }
                            }
                            // redirections
                            if(!$dejaAvis){?>
                                <a href="ajouter_avis.php?idProduit=<?php echo $idProd;?>">Ajouter un avis</a>
                            <?php }
                            else{?>
                                <a href="ajouter_avis.php?idProduit=<?php echo $idProd;?>">Modifier mon avis</a>
                            <?php }
                        } else{
                            // si le client n'est pas connecter?>
                            <a href="connexion.php?idProduit=<?php echo $idProd;?>">Ajouter un avis</a>
                        <?php }?>
                    </button>

                    <!-- Supression d'un avis -->
                    <?php if ($_SESSION['role'] === 'client' && $dejaAvis){ // on peut supprimer un avis que si on a déjà mit un ?>
                        <a href="./<?php echo "ajouter_avis.php?idProduit=".$idProd."&supr=true"; ?>"><button class="bg-beige rounded-2xl w-48 h-14 mb-4 md:mr-16 hover:text-rouge">Supprimer mon avis</button></a>
                    <?php }?>

                    <?php if($avis != null){?>
                    <!-- Notes -->
                    <section class="md:mr-16 p-5 bg-beige rounded-2xl h-80 w-48 flex flex-col justify-center">
                        <h4>Notes - <?php echo count($avis);?></h4>
                        <table>
                            <tbody>
                                <?php for ($i = 0; $i <= 5; $i++){
                                    $compteur = 5- $i;?>
                                    <tr class="flex justify-around items-center">
                                        <td class=" mr-1 mt-2 mb-2" ><?php echo $compteur;?></td>
                                        <td class=" mr-4 ml-1 mt-2 mb-2"><img src="../../images/logo/bootstrap_icon/star-fill.svg"></td>
                                        <?php 
                                            foreach($dbh->query("SELECT COUNT(nb_etoile) AS nbre_notes 
                                                                    FROM sae3_skadjam._avis 
                                                                    WHERE id_produit = $idProd 
                                                                        AND nb_etoile = $compteur"
                                                                , PDO::FETCH_ASSOC) as $row){;?>
                                            <td class=" mr-1 mt-2 mb-2"><?php echo $row["nbre_notes"];?></td>
                                            <td class=" mr-4 ml-1 mt-2 mb-2"><?php echo ($row["nbre_notes"]<=1)?'note':'notes'?></td>
                                        <?php }?>
                                    </tr>
                                <?php }?>
                            </tbody>
                        </table>
                    </section>
                    <?php }?>
                </div>
            </div>
        </section>
    </main>

    <?php require(__DIR__ . "/../../php/structure/footer_front.php") ?>
</body>

<script type="module">
    import * as Popup from "../../js/popup.js";

    const popupElement1 = document.getElementById("popup-ajouter-panier");
    const popupElement2 = document.getElementById("popup-ajouter-avis");
    const popupElement3 = document.getElementById("popup-supprimer-avis");

    if (popupElement1) {
        const btnClosePopUp1 = popupElement1.querySelector("button");

        btnClosePopUp1?.addEventListener("click", () => {
            Popup.closePopup("popup-ajouter-panier");
        });

        Popup.showPopUp("popup-ajouter-panier", 5000, "panierAjouter");
    }

    if (popupElement2) {
        const btnClosePopUp2 = popupElement2.querySelector("button");

        btnClosePopUp2?.addEventListener("click", () => {
            Popup.closePopup("popup-ajouter-avis");
        });

        Popup.showPopUp("popup-ajouter-avis", 5000, "avisAjouter");
    }

    if (popupElement3) {
        const btnClosePopUp3 = popupElement3.querySelector("button");

        btnClosePopUp3?.addEventListener("click", () => {
            Popup.closePopup("popup-supprimer-avis");
        });

        Popup.showPopUp("popup-supprimer-avis", 5000, "avisSupprimer");
    }


</script>

</html>