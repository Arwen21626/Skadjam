<?php
session_start();
require_once __DIR__ . "/../../php/verif_role_bo.php";
require_once __DIR__ . "/../../01_premiere_connexion.php";

$idCompte = $_SESSION["idCompte"];

if ($_SESSION["role"] === "vendeur"){

    try{
        // donéees en rapport avec le compte vendeur
        $donneesVendeur = $dbh->query("SELECT c.id_compte as id_vendeur, c.nom_compte as nom_vendeur, c.prenom_compte as prenom_vendeur, c.adresse_mail as email_vendeur, c.numero_telephone as telephone_vendeur, c.bloque as compte_vendeur_bloque, -- c.mot_de_passe, c.code_secret, 
                    v.raison_sociale as raison_social_vendeur, v.siren as siren_vendeur, v.description_vendeur, v.iban as iban_vendeur, v.denomination as denomination_vendeur, 
                    ph.id_photo as photo_vendeur, ph.url_photo as url_photo_vendeur, ph.description_photo as description_photo_vendeur, ph.alt as alt_photo_vendeur, ph.titre as titre_photo_vendeur
                
                FROM sae3_skadjam._compte c
                    INNER JOIN sae3_skadjam._vendeur v ON c.id_compte = v.id_compte
                    LEFT JOIN sae3_skadjam._presente pr ON pr.id_vendeur = v.id_compte
                    LEFT JOIN sae3_skadjam._photo ph ON ph.id_photo = pr.id_photo
                WHERE c.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);
        
        // données en rapport avec les adresses
        $donneesAdresses = $dbh->query("SELECT
                    adr.id_adresse as id_adresse_vendeur, adr.adresse_postale as adresse_postal_vendeur, adr.complement_adresse as complement_adresse_vendeur, adr.numero_rue as numero_rue_vendeur, adr.numero_bat as numero_batiement_vendeur, 
                    adr.numero_appart as numero_apart_vendeur, adr.code_interphone as code_interphone_vendeur, adr.code_postal as code_postal_vendeur, adr.ville as ville_vendeur, adr.latitude as latitude_vendeur, adr.longitude as longitude_vendeur
            
            FROM sae3_skadjam._compte c
                INNER JOIN sae3_skadjam._habite h ON h.id_compte = c.id_compte
                INNER JOIN sae3_skadjam._adresse adr ON adr.id_adresse = h.id_adresse
            WHERE c.id_compte = $idCompte
        ", PDO::FETCH_ASSOC);

        // données en rapport avec les produits
        $donneesProduits = $dbh->query("SELECT
                prod.id_produit, prod.libelle_produit, prod.description_produit, prod.prix_ht, 
                    prod.prix_ttc, prod.prix_remise, prod.quantite_unite, prod.unite,
                    prod.note_moyenne, prod.est_masque, 
                    prod.quantite_stock, prod.seuil_alerte, prod.est_supprime, prod.date_creation, 
                ph.id_photo as photo_produit, ph.url_photo as url_photo_produit, ph.description_photo as description_photo_produit, ph.alt as alt_photo_produit, ph.titre as titre_photo_produit, 
                rem.id_remise, rem.pourcentage_remise, rem.date_debut_remise, rem.date_fin_remise, 
                promo.id_promotion, promo.label as libelle_promotion, promo.id_photo, promo.id_vendeur, promo.date_debut_promotion, promo.date_fin_promotion, promo.periodicite, promo.heure_debut, promo.heure_fin, 
                ph_promo.id_photo as photo_promotion_produit, ph_promo.url_photo as url_photo_promotion_produit, ph_promo.description_photo as description_photo_promotion_produit, 
                    ph_promo.alt as alt_photo_promotion_produit, ph_promo.titre as titre_photo_promotion_produit, 
                cat.id_categorie, cat.libelle_categorie,
                t.id_tva, t.nom_tva, t.pourcentage_tva
            
                FROM sae3_skadjam._compte c
                    INNER JOIN sae3_skadjam._vendeur v ON v.id_compte = c.id_compte
                    INNER JOIN sae3_skadjam._produit prod ON prod.id_vendeur = v.id_compte
                    LEFT JOIN sae3_skadjam._montre mont ON mont.id_produit = prod.id_produit
                    LEFT JOIN sae3_skadjam._photo ph ON ph.id_photo = mont.id_photo
                    LEFT JOIN sae3_skadjam._reduit red ON red.id_produit = prod.id_produit
                    LEFT JOIN sae3_skadjam._remise rem ON rem.id_remise = red.id_remise
                    LEFT JOIN sae3_skadjam._promu prom ON prom.id_produit = prod.id_produit
                    LEFT JOIN sae3_skadjam._promotion promo ON promo.id_promotion = prom.id_promotion
                    LEFT JOIN sae3_skadjam._photo ph_promo ON ph_promo.id_photo = mont.id_photo
                    LEFT JOIN sae3_skadjam._categorie cat ON cat.id_categorie = prod.id_categorie
                    LEFT JOIN sae3_skadjam._tva t ON t.id_tva = prod.id_tva
                WHERE c.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les factures et les commandes
        $donneesFactures = $dbh->query("SELECT
                    f.numero_facture,
                    cli.pseudo as pseudo_client,
                    c.id_commande, c.id_suivi as id_suivie_commande, c.etat as etat_commande, c.date_commande as date_commande, c.montant_total_ttc as montant_total_ttc_commande

                FROM sae3_skadjam._facture f
                    INNER JOIN sae3_skadjam._commande c ON c.id_commande = f.id_commande
                    INNER JOIN sae3_skadjam._client cli ON c.id_client = cli.id_compte
                WHERE f.emetteur = $idCompte
            ");

        // données en rapport avec les produits commandé
        $donneesDetailsCommandes = $dbh->prepare("SELECT
                    d.montant_ht as montant_ht_detail_commande, d.quantite as quantite_detail_commande, d.sous_total as sous_total_produit_detail_commande, d.id_produit as id_produit_detail_commande, 
                    p.id_produit, p.libelle_produit, p.quantite_unite, p.unite, p.id_categorie, p.id_vendeur,
                    v.raison_sociale,
                    cat.libelle_categorie

                FROM sae3_skadjam._commande c
                    INNER JOIN sae3_skadjam._details d ON d.id_commande = c.id_commande
                    INNER JOIN sae3_skadjam._produit p ON p.id_produit = d.id_produit
                    INNER JOIN sae3_skadjam._vendeur v ON p.id_vendeur = v.id_compte
                    INNER JOIN sae3_skadjam._categorie cat ON cat.id_categorie = p.id_categorie
                WHERE c.id_commande = ? AND p.id_vendeur = ?
            ");

        
        // données en rapport avec les réponses
        $donneesReponsesPostes = $dbh->query("SELECT
                    rep.contenu_reponse, 
                    a.contenu_commentaire as contenu_avis_repondu, 
                    cli.pseudo as pseudo_client, 
                    p.libelle_produit, p.id_produit
                
                FROM sae3_skadjam._vendeur v
                    INNER JOIN sae3_skadjam._reponse rep ON rep.id_compte = v.id_compte
                    INNER JOIN sae3_skadjam._avis a ON a.id_avis = rep.id_avis
                    INNER JOIN sae3_skadjam._client cli ON a.id_compte = cli.id_compte
                    INNER JOIN sae3_skadjam._produit p ON a.id_produit = p.id_produit
                WHERE v.id_compte = $idCompte
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
<?php require __DIR__ . "/../../php/structure/head_back.php"; ?>
<head>
    <title>Mes données</title>
    <style>
        h5, h6{
            font-family: 'MavenPro';
            src: url("/font/MavenPro/MavenPro-Regular.ttf");
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
    require __DIR__ . "/../../php/structure/header_back.php";
    require __DIR__ . "/../../php/structure/navbar_back.php";
    ?>
    <main class="min-h-[650x] m-5">
        <h2>Vos informations</h2>
        <!---boutons imprimer--->
        <button class="imprimer fixed bottom-15 md:top-70 md:right-5 border-vertFonce bg-white border-2 rounded-xl w-35 h-10 px-7 cursor-pointer">Imprimer</button>

        <?php 
        // informations sur le compte vendeur
        foreach($donneesVendeur as $donnees){?>
            <section class="md:grid grid-cols-4 mb-5 gap-3">
                <h3 class="text-center m-2 col-span-4">Votre compte</h3>
                <p>nom : <?php echo $donnees["nom_vendeur"]?></p>
                <p>prenom : <?php echo $donnees["prenom_vendeur"]?></p>
                <p>email : <?php echo $donnees["email_vendeur"]?></p>
                <p>téléphone : <?php echo $donnees["telephone_vendeur"]?></p>
                <p>bloqué : <?php echo $donnees["compte_vendeur_bloque"]?"true": "false"?></p>
                <p>raison social : <?php echo $donnees["raison_social_vendeur"]?></p> 
                <p>siren : <?php echo $donnees["siren_vendeur"]?></p> 
                <?php if($donnees["description_vendeur"] != ""){?>
                    <p>description : <?php echo $donnees["description_vendeur"]?></p> 
                <?php }?>
                <p>iban : <?php echo $donnees["iban_vendeur"]?></p> 
                <p>denomination : <?php echo $donnees["denomination_vendeur"]?></p> 
                <?php if($donnees["url_photo_vendeur"] != ""){?>
                <img src="<?php $donnees["url_photo_vendeur"]?>" alt="<?php $donnees["alt_photo_vendeur"]?>" title="<?php $donnees["titre_photo_vendeur"]?>">
                <?php } if($donnees["description_photo_vendeur"] != ""){?>
                <p>description de l'image : <?php echo $donnees["description_photo_vendeur"]?></p> 
                <?php }?>
            </section>
        <?php }?>
        <section class="md:grid grid-cols-4 mb-5">
            <h3 class="text-center m-2 col-span-4 md:mt-10">Vos adresses</h3>
            <?php 
            // informations sur les adresses du vendeur
            foreach($donneesAdresses as $donnees){?>
                <div class=" mb-5 md:grid grid-cols-4 col-span-4 gap-3">
                    <h4 class=" col-span-4 font-bold text-center"><?php echo $donnees["numero_rue_vendeur"].($donnees["complement_adresse_vendeur"] !== ""?" ".$donnees["complement_adresse_vendeur"]:"")." ".$donnees["adresse_postal_vendeur"]?></h4>
                    <p>ville : <?php echo $donnees["ville_vendeur"]?></p>
                    <p>code postale : <?php echo $donnees["code_postal_vendeur"]?></p>

                    <?php if($donnees["latitude_vendeur"] != ""){?>
                        <p>latitude : <?php echo $donnees["latitude_vendeur"]?></p>
                    <?php }if($donnees["longitude_vendeur"] != ""){?>
                        <p>longitude : <?php echo $donnees["longitude_vendeur"]?></p>
                    <?php }if($donnees["numero_batiement_vendeur"] != ""){?>
                        <p>numéro de batiment : <?php echo $donnees["numero_batiement_vendeur"]?></p>
                    <?php }if($donnees["numero_apart_vendeur"] != ""){?>
                        <p>numéro apartement : <?php echo $donnees["numero_apart_vendeur"]?></p>
                    <?php }if($donnees["code_interphone_vendeur"] != ""){?>
                        <p>code de l'interphone : <?php echo $donnees["code_interphone_vendeur"]?></p>
                    <?php }?>
                </div>
            <?php }?>
        </section>
        <section class=" mb-5">
            <h3 class="text-center m-2 md:mt-10">Vos commandes</h3>
            <?php 
            // informations sur les commandes du vendeur
            foreach($donneesFactures as $donnees){?>
                <div class="md:grid grid-cols-4 mb-5 gap-3">
                    <h4 class=" ml-10 m-2 font-bold col-span-4 md:text-center"> Commande <?php echo $donnees["id_commande"]?></h4>
                    <?php if($donnees["id_suivie_commande"] != ""){?>
                        <p>id de suivie : <?php echo $donnees["id_suivie_commande"]?></p>
                    <?php }?>
                    <p>état : <?php echo $donnees["etat_commande"]?></p>
                    <p>date : <?php echo $donnees["date_commande"]?></p>
                    <p>montant total <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $donnees["montant_total_ttc_commande"]?></p>
                    <p>facture numéro : <?php echo $donnees["numero_facture"]?></p>
                    <p>client : <?php echo $donnees["pseudo_client"]?></p>
                
                    <div class=" col-span-4 mb-5">
                        <h5 class=" md:text-[23px] text-[18px] ml-5 m-2 font-bold md:text-center">Les produits de la commande</h5>
                    <?php // produits commandé
                    $donneesDetailsCommandes->execute([$donnees["id_commande"], $idCompte]);
                    
                    foreach($donneesDetailsCommandes as $produits){?>
                        <div class=" md:grid grid-cols-3 mb-5 gap-3">
                            <h6 class=" md:text-[23px] text-[18px] ml-2 mt-1 font-bold col-span-3 md:text-center"><?php echo $produits["id_produit"]." - ".$produits["libelle_produit"]?></h6>
                            <p>montant <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $produits["sous_total_produit_detail_commande"]?></p>
                            <p>montant <abbr title="Hors Taxes">HT</abbr> : <?php echo $produits["montant_ht_detail_commande"]?></p>
                            <p>quantite : <?php echo $produits["quantite_detail_commande"]?></p>
                        </div>
                    <?php }?>
                    </div>
                </div>
            <?php }?>
        </section>
        <!-- les reponses poster par le vendeur -->
        <section class=" mb-5">
            <h3 class="text-center m-2">Vos réponses aux avis postés</h3>
            <?php 
            foreach($donneesReponsesPostes as $donnees){?>
                <div class=" md:grid grid-cols-4 mb-5 gap-3">
                    <h4 class=" ml-10 m-2 font-bold col-span-4 md:text-center">Sur le produit : <?php echo $donnees["id_produit"]." - ". $donnees["libelle_produit"]?></h4>
                    <p class=" col-span-4">commentaire : <?php echo $donnees["contenu_avis_repondu"]?></p>
                    <p class=" col-span-4">par : <?php echo $donnees["pseudo_client"]?></p>
                    <p class=" col-span-4">reponse : <?php echo $donnees["contenu_reponse"]?></p>
                </div>
            <?php }?>
        </section>
        <!-- les produits du vendeur -->
        <section class=" mb-5">
            <h3 class="text-center m-2">Vos produits</h3>
        <?php foreach($donneesProduits as $donnees){?>
            <div class=" md:grid grid-cols-3 mb-5 gap-3">
                <div class=" col-span-3 flex justify-self-center">
                    <img class="w-16 h-16 inline-block" src="<?php echo $donnees["url_photo_produit"]?>" alt="<?php echo $donnees["alt_photo_produit"]?>" title="<?php echo $donnees["titre_photo_produit"]?>">
                    <h4 class=" ml-10 m-2 font-bold"><?php echo $donnees["id_produit"]." - ".$donnees["libelle_produit"]?></h4>
                </div>
                
                <div class="flex col-span-3">
                    <p>visible par les clients : <?php echo $donnees["est_masque"]?"true":"false"?></p>
                    <p class="ml-8">supprimé : <?php echo $donnees["est_supprime"]?"true":"false"?></p>
                </div>
                
                <p class=" col-span-3">description : <?php echo $donnees["description_produit"]?></p>
                <p>unité : <?php echo $donnees["unite"]?></p>
                <p>quantité par unité : <?php echo $donnees["quantite_unite"]?></p>
                <p>categorie : <?php echo $donnees["libelle_categorie"]?></p>
                <p>note moyenne : <?php echo true?$donnees["note_moyenne"]:"non noté"?></p>

                <p>prix <abbr title="Hors Taxes">HT</abbr> : <?php echo $donnees["prix_ht"]?></p>
                <p>prix <abbr title="Toutes Taxes Comprises">TTC</abbr> : <?php echo $donnees["prix_ttc"]?></p>
                <p>quatité en stock : <?php echo $donnees["quantite_stock"]?></p>
                <p>seuil d'alerte : <?php echo $donnees["seuil_alert"]!=""?$donnees["seuil_alert"]:0?></p>
                <p>date de création : <?php echo $donnees["date_creation"]?></p>
                
                <!-- tva -->
                <div>
                    <p>nom de la TVA : <?php echo $donnees["nom_tva"]?></p>
                    <p>pourcentage de TVA : <?php echo $donnees["pourcentage_tva"]?></p>
                </div>

                <!-- promotion -->
                <div> 
                    <?php if ($donnees["libelle_promotion"] != ""){?>
                        <p>libelle promotion : <?php echo $donnees["libelle_promotion"]?></p>
                        <p>debut de promotion : le <?php echo $donnees["date_debut_promotion"]?> à <?php echo $donnees["heure_debut"]?> </p>
                    <?php } if ($donnees["date_fin_promotion"] != ""){?>
                        <p>fin de promotion : le <?php echo $donnees["date_fin_promotion"]?> à <?php echo $donnees["heure_fin"]?> </p>
                    <?php } if ($donnees["periodicite"] != ""){?>
                        <p>periodicite : <?php echo $donnees["periodicite"]?></p>
                    <?php } if ($donnees["url_photo_promotion"] != ""){?>
                        <img src="<?php echo $donnees["url_photo_promotion"]?>" alt="<?php echo $donnees["alt_photo_promotion"]?>" title="<?php echo $donnees["titre_photo_promotion"]?>">
                    <?php } if ($donnees["description_photo_promotion"] != ""){?>
                        <p class=" col-span-3">description de la photo de promotion : <?php echo $donnees["description_photo_promotion"]?></p>
                    <?php }?>
                </div> 

                <!-- remise -->
                <div>
                    <?php if ($donnees["pourcentage_remise"] != ""){?>
                        <p>prix avec remise : <?php echo $donnees["prix_remise"]?></p>
                        <p>pourcentage de remise : <?php echo $donnees["pourcentage_remise"]?></p>
                    <?php } if ($donnees["date_debut_remise"] != ""){?>
                        <p>date de début de remise : <?php echo $donnees["date_debut_remise"]?></p>
                    <?php } if ($donnees["date_fin_remise"] != ""){?>
                        <p>date de fin de remise : <?php echo $donnees["date_fin_remise"]?></p>
                    <?php }?>
                </div>
        </div>
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
            hideFrame.src = "./recuperation_donnees_vendeur.php";
            document.body.appendChild(hideFrame); // ajoute dans le body le iframe pour l'impression
        }

        btnImprimmer[0].addEventListener("click", () => {affichagePageImpression()});

    </script>

    </main>
    <?php require __DIR__ . "/../../php/structure/footer_back.php"; ?>
</body>
</html>
