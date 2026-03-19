<?php
    session_start();
    require_once(__DIR__ . "/../../php/verif_role_fo.php");
    require_once(__DIR__ . "/../../01_premiere_connexion.php");
    require(__DIR__ . "/../../php/fonctions.php");

    $idProd = $_GET["idProduit"];
    $idCompte = $_SESSION['idCompte'] ?? 0;
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
    error_log(print_r($produit, true));

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

        // Requête pour récupérer la date de fin de la promotion
        $stmt = $dbh->prepare("SELECT date_fin_promotion
                    FROM sae3_skadjam._promotion pn
                    NATURAL JOIN sae3_skadjam._promu pu
                    WHERE pu.id_produit = ?");
        $stmt->execute([$idProd]);
        $dateFinPromotion = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
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

        // nombre de ce produit dans le panier
        $nbInPanier = 0;
        if ($_SESSION['role'] === 'visiteur' && $_SESSION['panier']['nb_produit_total']>0){
            foreach ($_SESSION['panier']['contient'] as $prod){
                error_log("produit compare : ".$prod['id']."===".$idProd);
                if ($prod['id'] === $idProd){
                    $nbInPanier = $prod['quantite_par_produit'];
                    break;
                }
            }
        }
        if ($_SESSION['role'] === 'client'){
            $stmt = $dbh->prepare("SELECT quantite_par_produit FROM sae3_skadjam._contient con INNER JOIN sae3_skadjam._client cli ON con.id_panier = cli.id_panier WHERE con.id_produit = ? AND cli.id_compte = ?");
            $stmt->execute([$idProd, $idCompte]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                $nbInPanier = $result['quantite_par_produit'];
            }
        }
        
        // est dans FA
        $inFA = false;
        if ($_SESSION['role'] === 'visiteur' && !empty($_SESSION['futurAchat'])){
            if (isset($_SESSION['futurAchat'][$idProd])){
                $inFA = true;
            }
        }
        if ($_SESSION['role'] === 'client'){
            $stmt = $dbh->prepare("SELECT id_produit FROM sae3_skadjam._futur_achat WHERE id_produit = ? AND id_client = ?");
            $stmt->execute([$idProd, $idCompte]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result){
                $inFA = true;
            }
        }
        

        // Définition du lien vers lequel est renvoyé le client en cliquant sur le bouton ajouter au panier
        // Si il est connecté : le produit est ajouté à son panier
        // Si il n'est pas connecté : le visiteur est renvoyé sur la page de connexion

        $lienBtnAjouterPanier = "/php/ajouter_panier.php";

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

        <!---popup être connecté pour laisser un pouce--->
        <div id="popup-non-connecte-pouce" class="popup p-4 border-rouge shadow-xl hidden">
            <p>Vous devez être connecté pour laisser un avis 👍👎 !</p>
            <div class="flex justify-center mt-2 gap-4">
                <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                <a href="connexion.php" class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">
                    Se connecter
                </a>
            </div>
        </div>

        <!---popup être connecté pour signaler--->
        <div id="popup-non-connecte-signaler" class="popup p-4 border-rouge shadow-xl hidden">
            <p>Vous devez être connecté pour pouvoir signaler un avis !</p>
            <div class="flex justify-center mt-2 gap-4">
                <button class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">OK</button>
                <a href="connexion.php" class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">
                    Se connecter
                </a>
            </div>
        </div>

        <!-- Popup confirmation signalement-->
        <div id="popup-confirm-signal" class="popup p-4 border-rouge shadow-xl">
            <p>Voulez-vous vraiment signaler cet avis ?</p>
            <div class="flex justify-center mt-2 gap-4">
                <button id="confirm-signal" class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">
                    Oui
                </button>
                <button id="cancel-signal" class="pl-2 pr-2 border-2 border-vertClair rounded-sm cursor-pointer">
                    Non
                </button>
            </div>
        </div>

    </div>

    <main class="p-4 md:pl-8 pr-8">
        <!-- Section Description -->
        <section class="flex flex-col items-center md:items-stretch">
            <article class="p-2 md:pb-8"> <!-- Titrage -->
                <div class="flex flex-col md:flex-row">
                    <h3> <?php echo $libelleProd; ?></h3>
                    <?php if(!empty($dateFinPromotion[0])){ ?>
                    <h4 class="md:relative md:top-1 md:ml-5">promotion se termine le <?= $dateFinPromotion[0]; ?></h4>
                    <?php } ?>
                </div>
                <p class="ml-4">Catégorie : <?php echo $libelleCat; ?></p>
                <div class="ml-10"> <?php echo affichageNote($noteMoy); ?> </div>
            </article>
            
            <article class="flex flex-col items-center md:items-stretch md:flex-row md:justify-around">
                <img src="<?php echo $infoPhoto["url_photo"]; ?>"  alt=<?php echo $infoPhoto["alt"] ?> title=<?php echo $infoPhoto["titre"] ?>
                class="size-1/2 border
                       md:size-1/4">

                <div class="p-2 flex flex-col items-center">
                    <div class="flex flex-col md:mb-4">
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
                    </div><br>
                    <div class="border-2 border-vertClair mt-2 rounded-2xl flex-col items-center p-2 text-center">
                        <p id="nbInPanier">Panier : <?= $nbInPanier?></p>
                        <div id="input-number" class=" flex flex-row items-center">
                            <button class=" cursor-pointer" onclick="suppProduit()" onmouseenter="enterBtn('btn-dash')" onmouseleave="leaveBtn('btn-dash')">
                                <img id="btn-dash" src="../../images/logo/bootstrap_icon//dash-square.svg" alt="plus-button" width="30px">
                            </button>
                            <input type="text" id="nb-produit" value="1" min="1" size="10" class="ml-2 mr-2 text-center border-3 border-vertClair">
                            <button class=" cursor-pointer" onclick="addProduit()" onmouseenter="enterBtn('btn-plus')" onmouseleave="leaveBtn('btn-plus')">
                                <img id="btn-plus" src="../../images/logo/bootstrap_icon/plus-square.svg" alt="plus-button" width="30px">
                            </button>
                        </div>
                        <form method="post" action="<?php echo $lienBtnAjouterPanier ?>" >
                            <input type="hidden" name="idProduit" value="<?php echo $idProd ?>">
                            <input type="hidden" name="pageDeRetour" value="details">
                            <input type="hidden" name="nbProduit" id="champ-nb-produit" value="1">
                            <button class="bg-beige rounded-2xl w-40 h-14 mt-4 cursor-pointer hover:text-rouge"
                            type="submit">
                                Ajouter au panier
                            </button>
                        </form>
                    </div>
                    <div>
                        <a class=" block text-center bg-beige rounded-2xl w-40 h-14 mt-4 cursor-pointer hover:text-rouge" href="../../php/traitementFAPanier.php?idProduit=<?= $idProd ?>&ajout=fa&vientDe=detailProd"><?= (isset($inFA) && $inFA)? "Supprimer des futurs achats":"Ajouter aux futurs achats" ?></a>
                    </div>
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
                $sql = "SELECT a.*, c.pseudo,
                        (SELECT pouce 
                        FROM sae3_skadjam._pouces p 
                        WHERE p.id_avis = a.id_avis 
                        AND p.id_compte = :idCompte) AS pouce_utilisateur
                        FROM sae3_skadjam._avis a 
                        INNER JOIN sae3_skadjam._client c 
                            ON a.id_compte = c.id_compte 
                        WHERE id_produit = :idProd";

                $stmt = $dbh->prepare($sql);
                $stmt->execute([
                    'idCompte' => $idCompte,
                    'idProd' => $idProd
                ]);

                $avis = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                if($avis == null){?>
                    <p class=" md:ml-24">Aucun avis associé à ce produit.</p>
                <?php }
                else{?>

                <!-- Commentaire -->
                
                <section class=" md:ml-32">
                    <?php foreach($avis as $row){
                        if ($row['contenu_commentaire'] != ''){
                            $monAvis = ($row['id_compte'] == $idCompte);

                            // Vérifier si l'utilisateur a déjà signalé l'avis ou si c'est son propre avis
                            $aSignaler = false;
                            $avisSignalable = false;

                            if (!$monAvis && isset($_SESSION['idCompte'])) {
                                $stmtSignal = $dbh->prepare("
                                    SELECT 1 
                                    FROM sae3_skadjam._a_signaler 
                                    WHERE id_compte = :idCompte AND id_avis = :idAvis
                                ");
                                $stmtSignal->execute([
                                    'idCompte' => $_SESSION['idCompte'],
                                    'idAvis' => $row['id_avis']
                                ]);
                                $aSignaler = $stmtSignal->fetch() ? true : false;
                            }

                            // Déterminer quel bouton afficher
                            if ($monAvis) {
                                // Pas de bouton si c'est mon avis
                                $btnSignal = '';
                            } elseif ($aSignaler) {
                                // Avis déjà signalé → bouton plein et désactivé
                                $btnSignal = '<img class="md:w-6 md:h-6 w-5 h-5" 
                                                    src="../../images/logo/bootstrap_icon/exclamation-triangle-fill.svg" 
                                                    alt="Avis déjà signalé" 
                                                    title="Vous avez déjà signalé cet avis">';
                            } else {
                                // Avis signalable → bouton cliquable
                                if (isset($_SESSION['role']) && $_SESSION['role'] !== 'visiteur') {
                                    // Utilisateur connecté → comportement normal
                                $btnSignal = '<button class="btn-signal cursor-pointer hover:scale-110 transition" 
                                                        data-id="' . $row['id_avis'] . '">
                                                <img class="md:w-6 md:h-6 w-5 h-5"
                                                    src="../../images/logo/bootstrap_icon/exclamation-triangle.svg" 
                                                    alt="Signaler" 
                                                    title="Signaler l\'avis">
                                            </button>';
                                }

                                else {
                                    // Visiteur non connecté → affiche la popup de connexion
                                    $btnSignal = '<button class="btn-signal-visiteur cursor-pointer hover:scale-110 transition">
                                                    <img class="md:w-6 md:h-6 w-5 h-5"
                                                        src="../../images/logo/bootstrap_icon/exclamation-triangle.svg" 
                                                        alt="Signaler" 
                                                        title="Signaler l\'avis">
                                                </button>';
                                }
                            }

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
                                    <!---pseudonyme--->
                                    <h4><?php echo $row['pseudo'];?></h4>

                                    <div class="flex items-center md:gap-4 gap-2">
                                        <!---note de l'avis--->
                                        <?php echo affichageNote($row['nb_etoile']);?>

                                        <div class="flex items-center md:gap-1">
                                            <!---pouce haut--->
                                            <span id="like-count-<?= $row['id_avis'] ?>">
                                                <?= $row['nb_pouce_haut'] ?>
                                            </span>
                                            <img class="vote-btn like md:w-6 md:h-6 w-5 h-5 cursor-pointer hover:scale-110 transition" 
                                                    src="<?= ($row['pouce_utilisateur'] === 1) ? '../../images/logo/bootstrap_icon/hand-thumbs-up-fill.svg' : '../../images/logo/bootstrap_icon/hand-thumbs-up.svg' ?>"
                                                    alt="icône pouce vers le haut si vous avez aimé l'avis" 
                                                    title="J'aime cet avis"
                                                    data-id="<?= $row['id_avis'] ?>"
                                                    data-type="1">
                                        </div>
                                    
                                        <!---pouce bas--->
                                        <div class="flex items-center md:gap-1">
                                            <span id="dislike-count-<?= $row['id_avis'] ?>">
                                                <?= $row['nb_pouce_bas'] ?>
                                            </span>
                                            <img class="vote-btn dislike md:w-6 md:h-6 w-5 h-5 cursor-pointer hover:scale-110 transition" 
                                                    src="<?= ($row['pouce_utilisateur'] === -1) ? '../../images/logo/bootstrap_icon/hand-thumbs-down-fill.svg' : '../../images/logo/bootstrap_icon/hand-thumbs-down.svg' ?>"
                                                    alt="icône pouce vers le bas si vous n'avez pas aimé l'avis" 
                                                    title="Je n'aime pas cet avis"
                                                    data-id="<?= $row['id_avis'] ?>"
                                                    data-type="-1">
                                        </div>
                                    </div>

                                    <!-- Bouton signaler -->
                                    <div>
                                        <?php echo $btnSignal; ?>
                                    </div>

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
    <script>
        const maxProduit = <?= $produit['quantite_stock'] ?>;
        const inputNbProduit = document.getElementById("nb-produit")
        const champNbProduit = document.getElementById("champ-nb-produit")
        const reg = /^[0]*[1-9][0-9]*[.,]?[0-9]*$/

        function addProduit(){
            if (inputNbProduit.value<maxProduit){
                inputNbProduit.value++
                champNbProduit.value = inputNbProduit.value
            }
        }

        function suppProduit(){
            if (inputNbProduit.value>1){
                inputNbProduit.value--
                champNbProduit.value = inputNbProduit.value
            }
        }
    
        inputNbProduit.addEventListener("change", () => {
            if (!reg.test(inputNbProduit.value)){
                inputNbProduit.value = 1
                console.log("attention")
            }

            inputNbProduit.value = parseInt(inputNbProduit.value)

            if (/^0/.test(inputNbProduit.value)){
                let chaineNbProduit = ""
                let fin = false
                let lengthInput = inputNbProduit.value.length
                for (i=0;i<lengthInput; i++){
                    if (inputNbProduit.value[i] != 0 || fin){
                        if (!fin){fin=true}
                        chaineNbProduit = chaineNbProduit + inputNbProduit.value[i]
                    }
                }
                inputNbProduit.value = chaineNbProduit
            }

            if (inputNbProduit.value > maxProduit){
                inputNbProduit.value = maxProduit
            }
            champNbProduit.value = inputNbProduit.value
        })

    
        function enterBtn(id){
            let imgBtn = document.getElementById(id)
            if (id === 'btn-dash'){imgBtn.src = "../../images/logo/bootstrap_icon/dash-square-fill.svg"}
            if (id === 'btn-plus'){imgBtn.src = "../../images/logo/bootstrap_icon/plus-square-fill.svg"}
        }

        function leaveBtn(id){
            let imgBtn = document.getElementById(id)
            if (id === 'btn-dash'){imgBtn.src = "../../images/logo/bootstrap_icon/dash-square.svg"}
            if (id === 'btn-plus'){imgBtn.src = "../../images/logo/bootstrap_icon/plus-square.svg"}
        }
    </script>
</body>

<script type="module">
    import * as Popup from "../../js/popup.js";

    const estConnecte = <?= (isset($_SESSION['role']) && $_SESSION['role'] !== 'visiteur') ? 'true' : 'false' ?>;
    const popupElement1 = document.getElementById("popup-ajouter-panier");
    const popupElement2 = document.getElementById("popup-ajouter-avis");
    const popupElement3 = document.getElementById("popup-supprimer-avis");
    const popupElement4 = document.getElementById("popup-non-connecte-signaler");
       
    //affichage popup ajouter au panier
    if (popupElement1) {
        const btnClosePopUp1 = popupElement1.querySelector("button");

        btnClosePopUp1?.addEventListener("click", () => {
            Popup.closePopup("popup-ajouter-panier");
        });

        Popup.showPopUp("popup-ajouter-panier", 5000, "panierAjouter");
    }

    //affichage popup ajouter un avis
    if (popupElement2) {
        const btnClosePopUp2 = popupElement2.querySelector("button");

        btnClosePopUp2?.addEventListener("click", () => {
            Popup.closePopup("popup-ajouter-avis");
        });

        Popup.showPopUp("popup-ajouter-avis", 5000, "avisAjouter");
    }

    //affichage popup supprimer un aivs
    if (popupElement3) {
        const btnClosePopUp3 = popupElement3.querySelector("button");

        btnClosePopUp3?.addEventListener("click", () => {
            Popup.closePopup("popup-supprimer-avis");
        });

        Popup.showPopUp("popup-supprimer-avis", 5000, "avisSupprimer");
    }

    //Affichage du compte de pouces haut(s)/bas
    document.querySelectorAll(".vote-btn").forEach(button => {
        button.addEventListener("click", () => {
            const avisId = button.dataset.id;
            const voteType = parseInt(button.dataset.type);

            //affichage de la popup si c'est un visiteur tentant de mettre un pouce
            if (!estConnecte) {
                Popup.showPopUp("popup-non-connecte-pouce", 5000);   
                return;
            }

            //ajout/retrait du pouce dans vote_pouce.php
            fetch("/php/vote_pouce.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: "id=" + avisId + "&type=" + voteType
            })
            .then(res => res.json())
            .then(data => {

                if(data.error){
                    console.error(data.error);
                    return;
                }

                const likeCount = document.getElementById("like-count-" + avisId);
                const dislikeCount = document.getElementById("dislike-count-" + avisId);
                likeCount.textContent = data.likes;
                dislikeCount.textContent = data.dislikes;

                const likeBtn = document.querySelector(`.vote-btn.like[data-id='${avisId}']`);
                const dislikeBtn = document.querySelector(`.vote-btn.dislike[data-id='${avisId}']`);

                //Reset les images
                likeBtn.src = "../../images/logo/bootstrap_icon/hand-thumbs-up.svg";
                dislikeBtn.src = "../../images/logo/bootstrap_icon/hand-thumbs-down.svg";

                //Activer le pouce correspondant seulement si l’utilisateur a voté
                if(data.pouce_utilisateur === 1){
                    likeBtn.src = "../../images/logo/bootstrap_icon/hand-thumbs-up-fill.svg";
                } else if(data.pouce_utilisateur === -1){
                    dislikeBtn.src = "../../images/logo/bootstrap_icon/hand-thumbs-down-fill.svg";
                }
            })
            .catch(err => console.error(err));
        });
    });


    // Gestion du signalement
    let avisIdToSignal = null;

    // Bouton signalement pour les visiteurs
    document.querySelectorAll(".btn-signal-visiteur").forEach(btn => {
        btn.addEventListener("click", () => {
            // Affiche la popup "Vous devez être connecté pour signaler"
            if (popupElement4) {
                const btnClosePopUp4 = popupElement4.querySelector("button");

                btnClosePopUp4?.addEventListener("click", () => {
                    Popup.closePopup("popup-non-connecte-signaler");
                });

                Popup.showPopUp("popup-non-connecte-signaler", 5000);
            }
        });
    });

    // Affichage de la popup de confirmation
    document.querySelectorAll(".btn-signal").forEach(btn => {
        btn.addEventListener("click", () => {
            avisIdToSignal = btn.dataset.id;
            Popup.showPopUp("popup-confirm-signal", 3000);
        });
    });

    // Annuler le signalement
    const cancelBtn = document.getElementById("cancel-signal");
    cancelBtn?.addEventListener("click", () => {
        Popup.closePopup("popup-confirm-signal");
        avisIdToSignal = null;
    });

    // Confirmer le signalement
    const confirmBtn = document.getElementById("confirm-signal");
    confirmBtn?.addEventListener("click", () => {
        if (!avisIdToSignal) return;

        fetch("/php/signal_avis.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "idAvis=" + avisIdToSignal
        })
        .then(res => res.json())
        .then(data => {
            if (data.error) {
                console.error(data.error);
                return;
            }

            // Fermer la popup de confirmation
            Popup.closePopup("popup-confirm-signal");

            // Mettre à jour le bouton signalement dans la page
            const btn = document.querySelector(`.btn-signal[data-id='${avisIdToSignal}']`);
            if (btn) {
                // Remplacer le bouton cliquable par l'image remplie
                btn.outerHTML = `<img class="md:w-6 md:h-6 w-5 h-5" 
                                        src="../../images/logo/bootstrap_icon/exclamation-triangle-fill.svg" 
                                        alt="Avis déjà signalé" 
                                        title="Vous avez déjà signalé cet avis">`;
            }

            avisIdToSignal = null;
        })
        .catch(err => console.error(err));
    });


    
</script>

</html>