<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_fo.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";

$idCompte = $_SESSION["idCompte"];

if ($_SESSION["role"] === "client"){

    try{
        // données en rapport avec le compte client
        $donneesClient = $dbh->query("SELECT c.id_compte as id_client, c.nom_compte as nom_client, c.prenom_compte as prenom_client, c.adresse_mail as email_client, c.numero_telephone as telephone_client, c.bloque as compte_client_bloque, -- c.mot_de_passe, c.code_secret, 
                    cli.pseudo as pseudo_client, cli.date_naissance as date_naissance_client, 
                    cb.id_carte_bancaire as id_carte_client, cb.numero_carte as numero_carte_client, cb.cryptogramme as cryptogramme_carte_client, cb.nom as nom_carte_client, cb.expiration as expiration_carte_client
                
                FROM sae3_skadjam._compte c
                    INNER JOIN sae3_skadjam._client cli ON c.id_compte = cli.id_compte
                    LEFT JOIN sae3_skadjam._carte_bancaire cb ON cb.id_client = cli.id_compte
                WHERE c.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les adresses
        $donneesAdresses = $dbh->query("SELECT
                    adr.id_adresse as id_adresse_client, adr.adresse_postale as adresse_postal_client, adr.complement_adresse as complement_adresse_client, adr.numero_rue as numero_rue_client, adr.numero_bat as numero_batiement_client, 
                    adr.numero_appart as numero_apart_client, adr.code_interphone as code_interphone_client, adr.code_postal as code_postal_client, adr.ville as ville_client, adr.latitude as latitude_client, adr.longitude as longitude_client
            
            FROM sae3_skadjam._compte c
                INNER JOIN sae3_skadjam._habite h ON h.id_compte = c.id_compte
                INNER JOIN sae3_skadjam._adresse adr ON adr.id_adresse = h.id_adresse
            WHERE c.id_compte = $idCompte
        ", PDO::FETCH_ASSOC);

        // données en rapport avec les commandes
        $donneesCommandes = $dbh->query("SELECT
                    c.id_commande, c.id_suivi as id_suivie_commande, c.etat as etat_commande, c.date_commande as date_commande, c.montant_total_ttc as montant_total_ttc_commande, 
                    adrl.id_adresse as id_adresse_livraison, adrl.nom as nom_adresse_livraison, adrl.prenom as prenom_adresse_livraison, adrl.adresse_postale as adresse_livraison_postal, 
                        adrl.complement_adresse as complement_adresse_livraison, adrl.numero_rue as numero_rue_adresse_livraison, adrl.numero_bat as numero_batiment_adresse_livraison, 
                        adrl.numero_appart as numero_apart_adresse_livraison, adrl.code_interphone as code_interphone_adresse_livraison, adrl.code_postal as code_postal_adresse_livraison, 
                        adrl.ville as ville_adresse_livraison

                FROM sae3_skadjam._client cli
                    INNER JOIN sae3_skadjam._commande c ON c.id_client = cli.id_compte
                    INNER JOIN sae3_skadjam._adresse_livraison adrl ON adrl.id_adresse = c.id_adresse
                WHERE cli.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les factures
        $donneesFactures = $dbh->prepare("SELECT
                    f.numero_facture, 
                    v.raison_sociale

                FROM sae3_skadjam._facture f
                    INNER JOIN sae3_skadjam._vendeur v ON f.emetteur = v.id_compte
                WHERE f.id_commande = ?
            ");

        // données en rapport avec les produits commandé
        $donneesDetailsCommandes = $dbh->prepare("SELECT
                    d.montant_ht as montant_ht_detail_commande, d.quantite as quantite_detail_commande, d.sous_total as sous_total_produit_detail_commande, d.id_produit as id_produit_detail_commande, 
                    p.libelle_produit, p.quantite_unite, p.unite, p.id_categorie, p.id_vendeur,
                    v.raison_sociale,
                    cat.libelle_categorie

                FROM sae3_skadjam._commande c
                    INNER JOIN sae3_skadjam._details d ON d.id_commande = c.id_commande
                    INNER JOIN sae3_skadjam._produit p ON p.id_produit = d.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                WHERE c.id_commande = ?
            ");
   
        // donéees en rapport avec le panier
        $donneesPanier = $dbh->query("SELECT
                    pan.id_panier, pan.nb_produit_total as nombre_produit_panier, pan.montant_total_ttc as montant_total_ttc_panier, pan.date_derniere_modif as date_derniere_modif_panier

                FROM sae3_skadjam._client cli
                    INNER JOIN sae3_skadjam._panier pan ON pan.id_panier = cli.id_panier
                WHERE cli.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les produits du panier
        $donneesProduitsPanier = $dbh->prepare("SELECT
                    cont.quantite_par_produit,
                    p.libelle_produit, p.quantite_unite, p.unite, p.id_produit, 
                    v.raison_sociale,
                    cat.libelle_categorie

                FROM sae3_skadjam._contient cont
                    INNER JOIN sae3_skadjam._produit p ON p.id_produit = cont.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                WHERE cont.id_panier = ?
            ");

        // donéees en rapport avec les futurs achats
        $donneesFuturAchats = $dbh->query("SELECT
                    fa.id_produit, 
                    p.libelle_produit, p.quantite_unite, p.unite, 
                    v.raison_sociale,
                    cat.libelle_categorie

                FROM sae3_skadjam._futur_achat fa
                    INNER JOIN sae3_skadjam._produit p ON p.id_produit = fa.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                WHERE fa.id_client = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les avis postées
        $donneesAvisPostes = $dbh->query("SELECT 
                    a.id_avis, a.nb_etoile, a.nb_pouce_haut, a.nb_pouce_bas, a.contenu_commentaire, a.id_produit, a.signaler, 
                    ph.id_photo as photo_avis, ph.url_photo as url_photo_avis, ph.description_photo as description_photo_avis, ph.alt as alt_photo_avis, ph.titre as titre_photo_avis, 
                    p.libelle_produit, p.quantite_unite, p.unite,
                    v.raison_sociale,
                    cat.libelle_categorie

                FROM sae3_skadjam._avis a
                    INNER JOIN sae3_skadjam._produit p ON a.id_produit = p.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                    LEFT JOIN sae3_skadjam._appuie app ON a.id_avis = app.id_avis
                    LEFT JOIN sae3_skadjam._photo ph ON ph.id_photo = app.id_photo
                WHERE a.id_compte = $idCompte
                
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les avis signaler
        $donneesAvisSignales = $dbh->query("SELECT
                    sig.id_avis as avis_signaler,
                    asig.id_avis as avis_signaler, asig.contenu_commentaire as contenu_avis_signaler, 
                    p.libelle_produit, p.quantite_unite, p.unite, p.id_produit, 
                    v.raison_sociale,
                    cat.libelle_categorie
                FROM sae3_skadjam._client cli
                    INNER JOIN sae3_skadjam._a_signaler sig ON sig.id_compte = cli.id_compte
                    INNER JOIN sae3_skadjam._avis asig ON asig.id_avis = sig.id_avis
                    INNER JOIN sae3_skadjam._produit p ON asig.id_produit = p.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                WHERE cli.id_compte = $idCompte

            ", PDO::FETCH_ASSOC);

    }
    catch(PDOException $e){
        echo "Erreur dans la récupération des données";
        echo $e;
    }
}


?>

<!DOCTYPE html>
<html lang="fr">
<?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
<head>
    <title>Mes données</title>
    <style>
        h5, h6{
            font-family: 'MavenPro';
            src: url("/font/MavenPro/MavenPro-Regular.ttf");
            /* font-size: 25px; */
        }
        @media print { 
            header, footer, nav, button { 
                display: none; 
            } 
        }

    </style>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="min-h-[650x] m-5">
        <h2>Vos informations</h2>
        <!---boutons imprimer--->
        <button class="imprimer fixed bottom-15 md:top-70 md:right-5 border-vertClair bg-white border-2 rounded-xl w-35 h-10 px-7 cursor-pointer">Imprimer</button>

        <?php 
        // informations sur le compte client
        foreach($donneesClient as $donnees){?>
            <section class="md:grid grid-cols-4 mb-5 gap-3">
                <h3 class="text-center m-2 col-span-4">Votre compte</h3>
                <p>pseudo : <?php echo $donnees["pseudo_client"]?></p>
                <p>prenom : <?php echo $donnees["prenom_client"]?></p>
                <p>nom : <?php echo $donnees["nom_client"]?></p>
                <p>email : <?php echo $donnees["email_client"]?></p>
                <p>téléphone : <?php echo $donnees["telephone_client"]?></p>
                <p>bloqué : <?php echo $donnees["compte_client_bloque"]?"true": "false"?></p>
                <p>date naissance : <?php echo $donnees["date_naissance_client"]?></p>
            </section>
            <!-- infroamtion da la carte bancaire -->
            <?php if($donnees["id_carte_client"] !== null){?>
                <section class="md:grid grid-cols-4 mb-5 gap-3">
                    <h3 class="text-center m-2 col-span-4 md:mt-10">Votre carte bancaire</h3>
                    <p>nom : <?php echo $donnees["nom_carte_client"]?></p>
                    <p>numéro : <?php echo $donnees["numero_carte_client"]?></p>
                    <p>cryptogramme : <?php echo $donnees["cryptogramme_carte_client"]?></p>
                    <p>expiration : <?php echo $donnees["expiration_carte_client"]?></p>
                </section>
            <?php }?>
        <?php }?>
        <section class="md:grid grid-cols-4 mb-5">
            <h3 class="text-center m-2 col-span-4 md:mt-10">Vos adresses</h3>
            <?php 
            // informations sur les adresses du client
            foreach($donneesAdresses as $donnees){?>
                <div class=" mb-5 md:grid grid-cols-4 col-span-4 gap-3">
                    <h4 class=" col-span-4 font-bold text-center"><?php echo $donnees["numero_rue_client"].($donnees["complement_adresse_client"] !== ""?" ".$donnees["complement_adresse_client"]:"")." ".$donnees["adresse_postal_client"]?></h4>
                    <p>ville : <?php echo $donnees["ville_client"]?></p>
                    <p>code postale : <?php echo $donnees["code_postal_client"]?></p>

                    <?php if($donnees["latitude_client"] != ""){?>
                        <p>latitude : <?php echo $donnees["latitude_client"]?></p>
                    <?php }if($donnees["longitude_client"] != ""){?>
                        <p>longitude : <?php echo $donnees["longitude_client"]?></p>
                    <?php }if($donnees["numero_batiement_client"] != ""){?>
                        <p>numéro de batiment : <?php echo $donnees["numero_batiement_client"]?></p>
                    <?php }if($donnees["numero_apart_client"] != ""){?>
                        <p>numéro apartement : <?php echo $donnees["numero_apart_client"]?></p>
                    <?php }if($donnees["code_interphone_client"] != ""){?>
                        <p>code de l'interphone : <?php echo $donnees["code_interphone_client"]?></p>
                    <?php }?>
                </div>
            <?php }?>
        </section>
        <section class=" mb-5">
            <h3 class="text-center m-2 md:mt-10">Vos commandes</h3>
            <?php 
            // informations sur les commandes passées par le client
            foreach($donneesCommandes as $donnees){?>
                <div class="md:grid grid-cols-4 mb-5 gap-3">
                    <h4 class=" ml-10 m-2 font-bold col-span-4 md:text-center"> Commande <?php echo $donnees["id_commande"]?></h4>
                    <?php if($donnees["id_suivie_commande"] != ""){?>
                        <p>id de suivie : <?php echo $donnees["id_suivie_commande"]?></p>
                    <?php }?>
                    <p>état : <?php echo $donnees["etat_commande"]?></p>
                    <p>date : <?php echo $donnees["date_commande"]?></p>
                    <p>montant total <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $donnees["montant_total_ttc_commande"]?></p>

                    <!-- adresse de livraison -->
                    <div class=" col-span-4 md:grid grid-cols-4 mb-5 gap-3">
                        <h5 class="md:text-[23px] text-[18px] ml-5 m-2 font-bold col-span-4 md:text-center font-">L'adresse de livraison</h5> 
                        <p>nom : <?php echo $donnees["nom_adresse_livraison"]?></p>
                        <p>prenom : <?php echo $donnees["prenom_adresse_livraison"]?></p>
                        <p>adresse : <?php echo $donnees["numero_rue_adresse_livraison"].($donnees["complement_adresse_livraison"] !== ""?" ".$donnees["complement_adresse_livraison"]:"")." ".$donnees["adresse_livraison_postal"]?></p>
                        <p>code postal : <?php echo $donnees["code_postal_adresse_livraison"]?></p>
                        <p>ville : <?php echo $donnees["ville_adresse_livraison"]?></p>
                    
                        <?php if($donnees["numero_batiment_adresse_livraison"] != ""){?>
                            <p>numéro de batiment : <?php echo $donnees["numero_batiment_adresse_livraison"]?></p>
                        <?php }if($donnees["numero_apart_adresse_livraison"] != ""){?>
                            <p>numéro apartement : <?php echo $donnees["numero_apart_adresse_livraison"]?></p>
                        <?php }if($donnees["code_interphone_adresse_livraison"] != ""){?>
                            <p>code de l'interphone : <?php echo $donnees["code_interphone_adresse_livraison"]?></p>
                        <?php }?>
                    </div>
                    <!-- facture -->
                    <div class=" col-span-4 md:grid grid-cols-4 mb-5 gap-3">
                        <h5 class="md:text-[23px] text-[18px] ml-5 m-2 font-bold md:text-center col-span-4">Les factures de la commande</h5>
                        <?php $donneesFactures->execute([$donnees["id_commande"]]);
                        foreach($donneesFactures as $facture){?>
                            <div>
                                <h6 class=" md:text-[23px] text-[18px] ml-2 mt-1 font-bold md:mb-1">numéro : <?php echo $facture["numero_facture"]?></h6>
                                <p>emmetteur : <?php echo $facture["raison_sociale"]?></p>
                            </div>
                        <?php }?>
                    </div>
                
                    <div class=" col-span-4 mb-5">
                        <h5 class=" md:text-[23px] text-[18px] ml-5 m-2 font-bold md:text-center">Les produits de la commande</h5>
                    <?php // produits commandé
                    $donneesDetailsCommandes->execute([$donnees["id_commande"]]);
                    
                    foreach($donneesDetailsCommandes as $produits){?>
                        <div class=" md:grid grid-cols-4 mb-5 gap-3">
                            <h6 class=" md:text-[23px] text-[18px] ml-2 mt-1 font-bold col-span-4 md:text-center"><?php echo $produits["libelle_produit"]?></h6>
                            <p>montant <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $produits["sous_total_produit_detail_commande"]?></p>
                            <p>montant <abbr title="Hors Taxes">HT</abbr> : <?php echo $produits["montant_ht_detail_commande"]?></p>
                            <p>quantite : <?php echo $produits["quantite_detail_commande"]?></p>
                            <p>categorie : <?php echo $produits["libelle_categorie"]?></p>
                            <p>vendeur : <?php echo $produits["raison_sociale"]?></p>
                        </div>
                    <?php }?>
                    </div>
                </div>
            <?php }?>
        </section>
        <!-- le panier du client -->
        <section class=" mb-5 md:grid grid-cols-4 gap-3">
            <?php 
            foreach($donneesPanier as $donnees){?>
                <h3 class="text-center m-2 col-span-4">Votre panier</h3>
                <p>nombre de produits : <?php echo $donnees["nombre_produit_panier"]?></p>
                <p>montant total <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $donnees["montant_total_ttc_panier"]?></p>
                <p class=" col-span-2">date de dernière modification : <?php echo $donnees["date_derniere_modif_panier"]?></p>
                <?php $donneesProduitsPanier->execute([$donnees["id_panier"]]);?>
                <div class=" col-span-4">
                    <?php if($donnees["nombre_produit_panier"] !== "0"){?>
                        <h4 class=" ml-10 m-2 font-bold md:text-center">Les produits de votre panier</h4>
                        <?php foreach($donneesProduitsPanier as $produits){?>
                            <div class="md:grid grid-cols-4 mb-5 gap-3">
                                <h5 class=" md:text-[23px] text-[18px] ml-5 m-2 font-bold md:text-center col-span-4"><?php echo $produits["libelle_produit"]?></h5>
                                <p>quantite : <?php echo $produits["quantite_par_produit"]?></p>
                                <p>categorie : <?php echo $produits["libelle_categorie"]?></p>
                                <p>vendeur : <?php echo $produits["raison_sociale"]?></p>
                            </div>
                        <?php }
                    }?>
                </div>
            <?php }?>
        </section>
        <!-- la liste des futurs achats du client -->
        <section class=" mb-5">
            <h3 class="text-center m-2">Votre liste des futurs achats</h3>
            <?php 
            $futurAchatsVide = true;
            foreach($donneesFuturAchats as $donnees){
                $futurAchatsVide = false;?>
                <div class="md:grid grid-cols-4 mb-5 gap-3">
                    <h4 class=" ml-10 m-2 font-bold md:text-center col-span-4"><?php echo $donnees["libelle_produit"]?></h4>
                    <p>categorie : <?php echo $donnees["libelle_categorie"]?></p>
                    <p>vendeur : <?php echo $donnees["raison_sociale"]?></p>
                </div>
            <?php }
            if($futurAchatsVide === true){?>
                <p>Votre liste de futurs achats est vide</p>
            <?php }?>
        </section>
        <!-- les avis poster par le client -->
        <section class=" mb-5">
            <h3 class="text-center m-2">Vos avis postés</h3>
            <?php 
            foreach($donneesAvisPostes as $donnees){?>
                <div class=" md:grid grid-cols-4 mb-5 gap-3">
                    <div class=" col-span-4 md:text-center md:grid grid-cols-2">
                        <h4 class=" ml-10 m-2 font-bold col-span-2">Sur le produit : <?php echo $donnees["libelle_produit"]?></h4>
                        <p class=" justify-self-end pr-3">categorie : <?php echo $donnees["libelle_categorie"]?></p>
                        <p class=" justify-self-start pl-3">vendeur : <?php echo $donnees["raison_sociale"]?></p>
                    </div>
                    <?php if($donnees["contenu_commentaire"] != ""){?>
                        <p class=" col-span-4">commentaire : <?php echo $donnees["contenu_commentaire"]?></p>
                    <?php }?>
                    <p>nombre d'etoiles : <?php echo $donnees["nb_etoile"]?></p>
                    <p>nombre de pouces haut : <?php echo $donnees["nb_pouce_haut"] != ""?$donnees["nb_pouce_haut"]:0?></p>
                    <p>nombre de pouces bas : <?php echo $donnees["nb_pouce_bas"] != ""?$donnees["nb_pouce_bas"]:0?></p>
                    <p>signaler : <?php echo $donnees["signaler"]?"true":"false"?></p>
                    
                    <?php if($donnees["url_photo_avis"] != ""){?>
                        <img src="<?php echo $donnees["url_photo_avis"]?>" alt="<?php echo $donnees["alt_photo_avis"]?>" title="<?php echo $donnees["titre_photo_avis"]?>">
                        <p>description de la photo : <?php echo $donnees["description_photo_avis"]?></p>
                    <?php }?>
                </div>
            <?php }?>
        </section>
        <!-- avis signaler par le client -->
        <section class=" mb-5">
            <h3 class="text-center m-2">Les avis qui vous avez signalés</h3>
            <?php 
            $aSignaler = false;
            foreach($donneesAvisSignales as $donnees){
                $aSignaler = true;?>
                <div class=" md:grid grid-cols-2 mb-5 gap-3">
                    <h4 class=" ml-10 m-2 font-bold md:text-center col-span-2" >Sur le produit : <?php echo $donnees["libelle_produit"]?></h4>
                    <p class=" justify-self-end pr-3">categorie : <?php echo $donnees["libelle_categorie"]?></p>
                    <p class=" justify-self-start pl-3">vendeur : <?php echo $donnees["raison_sociale"]?></p>
                    <p class=" col-span-2">commentaire : <?php echo $donnees["contenu_avis_signaler"]?></p>
                </div>
            <?php }
            if ($aSignaler === false){?>
                <p>Vous n'avez signalé aucun avis</p>
            <?php }?>
        </section>

        <script>
        let btnImprimmer = document.getElementsByClassName("imprimer");

        function fermerPageImpression() {
            // fermer la page d'impression
            let iframe = document.getElementsByTagName("iframe")[0];
            let body = document.getElementsByTagName("body")[0];
            body.removeChild(iframe); 
        }

        function gestionPageImpression() {
            // définie quand est ce qu'on peut fermer la page d'impression
            // et définie un iframe de type impression
            this.contentWindow.onbeforeunload = fermerPageImpression;
            this.contentWindow.onafterprint = fermerPageImpression;
            this.contentWindow.print(); // indique que c'est une page qui permet d'imprimmer
        }
        function affichagePageImpression(){
            const hideFrame = document.createElement("iframe"); // création d'un iframe
            hideFrame.onload = gestionPageImpression;
            hideFrame.src = "./recuperation_donnees_client.php";
            document.body.appendChild(hideFrame); // ajoute dans le body le iframe pour l'impression
        }

        btnImprimmer[0].addEventListener("click", () => {affichagePageImpression()});

    </script>

    </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
