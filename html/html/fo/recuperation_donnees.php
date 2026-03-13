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
                        adrl.ville as ville_adresse_livraison, 
                    f.numero_facture, f.emetteur as emmeteur_facture

                FROM sae3_skadjam._client cli
                    INNER JOIN sae3_skadjam._commande c ON c.id_client = cli.id_compte
                    INNER JOIN sae3_skadjam._adresse_livraison adrl ON adrl.id_adresse = c.id_adresse
                    INNER JOIN sae3_skadjam._facture f ON f.id_commande = c.id_commande
                WHERE cli.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

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

        // donéees en rapport avec le compte vendeur
        $donneesVendeur = $dbh->query("SELECT c.id_compte as id_vendeur, c.nom_compte as nom_vendeur, c.prenom_compte as prenom_vendeur, c.adresse_mail as email_vendeur, c.numero_telephone as telephone_vendeur, c.bloque as compte_vendeur_bloque, -- c.mot_de_passe, c.code_secret, 
                    v.raison_sociale as raison_social_vendeur, v.siren as siren_vendeur, v.description_vendeur, v.iban as iban_vendeur, v.denomination as denomination_vendeur, 
                    adr.id_adresse as id_adresse_vendeur, adr.adresse_postale as adresse_postal_vendeur, adr.complement_adresse as complement_adresse_vendeur, adr.numero_rue as numero_rue_vendeur, adr.numero_bat as numero_batiement_vendeur, 
                        adr.numero_appart as numero_apart_vendeur, adr.code_interphone as code_interphone_vendeur, adr.code_postal as code_postal_vendeur, adr.ville as ville_vendeur, adr.latitude as latitude_vendeur, adr.longitude as longitude_vendeur, 
                    ph.id_photo as photo_vendeur, ph.url_photo as url_photo_vendeur, ph.description_photo as description_photo_vendeur, ph.alt as alt_photo_vendeur, ph.titre as titre_photo_vendeur
                
                FROM sae3_skadjam._compte c
                    INNER JOIN sae3_skadjam._vendeur v ON c.id_compte = v.id_compte
                    LEFT JOIN sae3_skadjam._habite h ON h.id_compte = c.id_compte
                    LEFT JOIN sae3_skadjam._adresse adr ON adr.id_adresse = h.id_adresse
                    LEFT JOIN sae3_skadjam._presente pr ON pr.id_vendeur = v.id_compte
                    LEFT JOIN sae3_skadjam._photo ph ON ph.id_photo = pr.id_photo
                WHERE c.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les réponses
        $donneesReponses = $dbh->query("SELECT
                    rep.contenu_reponse, rep.id_avis as id_avis_repondu
                
                FROM sae3_skadjam._vendeur v
                    INNER JOIN sae3_skadjam._reponse rep ON rep.id_compte = v.id_compte
                    INNER JOIN sae3_skadjam._avis a ON a.id_avis = rep.id_avis
                WHERE v.id_compte = $idCompte
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les avis postées
        $donneesAvisPostees = $dbh->query("SELECT 
                    a.id_compte as id_client, a.id_avis, a.nb_etoile, a.nb_pouce_haut, a.nb_pouce_bas, a.contenu_commentaire, a.id_produit, a.signaler, 
                    ph.id_photo as photo_avis, ph.url_photo as url_photo_avis, ph.description_photo as description_photo_avis, ph.alt as alt_photo_avis, ph.titre as titre_photo_avis

                FROM sae3_skadjam._avis a
                    LEFT JOIN sae3_skadjam._appuie app ON a.id_avis = app.id_avis
                    LEFT JOIN sae3_skadjam._photo ph ON ph.id_photo = app.id_photo
                WHERE a.id_compte = $idCompte
                
            ", PDO::FETCH_ASSOC);

        // données en rapport avec les avis signaler
        $donneesAvisSignalees = $dbh->query("SELECT cli.id_compte as id_client,
                    sig.id_avis as avis_signaler,
                    asig.id_avis as avis_signaler, asig.contenu_commentaire as contenu_avis_signaler

                FROM sae3_skadjam._client cli
                    INNER JOIN sae3_skadjam._a_signaler sig ON sig.id_compte = cli.id_compte
                    INNER JOIN sae3_skadjam._avis asig ON asig.id_avis = sig.id_avis
                WHERE cli.id_compte = $idCompte

            ", PDO::FETCH_ASSOC);

        // données en rapport avec les produits
        $donneesProduits = $dbh->query("SELECT
                prod.id_produit as id_produit, prod.libelle_produit as libelle_produit, prod.description_produit as description_produit, prod.prix_ht as prix_ht_produit, 
                    prod.prix_ttc as prix_ttc_produit, prod.prix_remise as prix_remise_produit, prod.quantite_unite as quantite_unite_produit, prod.unite as unite_produit,
                    prod.id_categorie as id_categorie_produit, prod.id_tva as id_tva_produi, prod.note_moyenne as note_moyenne_produit, prod.est_masque as est_masque_produit, 
                    prod.quantite_stock as quantite_stock_produit, prod.seuil_alerte as seuil_alert_produit, prod.est_supprime as est_supprime_produit, prod.date_creation as date_creation_produit, 
                ph.id_photo as photo_produit, ph.url_photo as url_photo_produit, ph.description_photo as description_photo_produit, ph.alt as alt_photo_produit, ph.titre as titre_photo_produit, 
                rem.id_remise as id_remise_produit, rem.pourcentage_remise as pourcentage_produit, rem.date_debut_remise as date_debut_remise_produit, rem.date_fin_remise as date_remise_fin_produit, 
                promo.id_promotion, promo.label, promo.id_photo, promo.id_vendeur, promo.date_debut_promotion, promo.date_fin_promotion, promo.periodicite, promo.heure_debut, promo.heure_fin, 
                ph_promo.id_photo as photo_promotion_produit, ph_promo.url_photo as url_photo_promotion_produit, ph_promo.description_photo as description_photo_promotion_produit, 
                    ph_promo.alt as alt_photo_promotion_produit, ph_promo.titre as titre_photo_promotion_produit, 
                cat.id_categorie as categorie_produit, cat.libelle_categorie as libelle_categorie_produit,
                t.id_tva as id_tva_produit, t.nom_tva as nom_tva_produit, t.pourcentage_tva as pourcentage_tva_produit
            
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
        h2, h3, h4, h5, h6{
            font-size: 2em;
        }
        div, section{
            margin-left: 1em;
            border-left: 2px solid black;
        }
    </style>
</head>
<body>
    <?php
    require __DIR__ . "/../../php/structure/header_front.php";
    // require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="min-h-[650x]">
        <h2>Vos informations</h2>

                    
        <?php 
        // informations sur le compte client
        foreach($donneesClient as $donnees){?>
            <h3><?php echo $donnees["id_client"]." - ".$donnees["pseudo_client"]?></h3>

            <section>
                <h4>Votre compte</h4>
                <p>prenom : <?php echo $donnees["prenom_client"]?></p>
                <p>nom : <?php echo $donnees["nom_client"]?></p>
                <p>email : <?php echo $donnees["email_client"]?></p>
                <p>téléphone : <?php echo $donnees["telephone_client"]?></p>
                <p>bloqué : <?php echo $donnees["compte_client_bloque"]?"true": "flase"?></p>
                <p>date naissance : <?php echo $donnees["date_naissance_client"]?></p>
            </section>
            <section>
                <h4>Votre carte bancaire</h4>
                <p>id : <?php echo $donnees["id_carte_client"]?></p>
                <p>nom : <?php echo $donnees["nom_carte_client"]?></p>
                <p>numéro : <?php echo $donnees["numero_carte_client"]?></p>
                <p>cryptogramme : <?php echo $donnees["cryptogramme_carte_client"]?></p>
                <p>expiration : <?php echo $donnees["expiration_carte_client"]?></p>
            </section>
        <?php }?>
        <section>
        <h4>Vos adresses</h4>
        <?php 
        // information sur les adresses du client
        foreach($donneesAdresses as $donnees){?>
            
                
                <p>id : <?php echo $donnees["id_adresse_client"]?></p>
                <p>adresse : <?php echo $donnees["adresse_postal_client"]?></p>
                <p>complement d'adresse : <?php echo $donnees["complement_adresse_client"]?></p>
                <p>nom : <?php echo $donnees["numero_rue_client"]?></p>
                <p>numéro de batiment : <?php echo $donnees["numero_batiement_client"]?></p>
                <p>numéro apartement : <?php echo $donnees["numero_apart_client"]?></p>
                <p>code de l'interphone : <?php echo $donnees["code_interphone_client"]?></p>
                <p>code postale : <?php echo $donnees["code_postal_client"]?></p>
                <p>ville : <?php echo $donnees["ville_client"]?></p>
                <p>latitude : <?php echo $donnees["latitude_client"]?></p>
                <p>longitude : <?php echo $donnees["longitude_client"]?></p>
        <?php }?>
        </section>
        <section>
            <h4>Vos commandes</h4>
        <?php 
        // informations sur les commande passé par le client
        foreach($donneesCommandes as $donnees){?>
                <div>
                    <h5>id : <?php echo $donnees["id_commande"]?></h5>
                    <p>id de suivie : <?php echo $donnees["id_suivie_commande"]?></p>
                    <p>état : <?php echo $donnees["etat_commande"]?></p>
                    <p>date : <?php echo $donnees["date_commande"]?></p>
                    <p>montant total <abbr>TTC</abbr> : <?php echo $donnees["montant_total_ttc_commande"]?></p>
                </div>
                <!-- adresse de livraison -->
                <div>
                    <h5>L'adresse de livraison</h5>
                    <p>id : <?php echo $donnees["id_adresse_livraison"]?></p>
                    <p>nom : <?php echo $donnees["nom_adresse_livraison"]?></p>
                    <p>prenom : <?php echo $donnees["prenom_adresse_livraison"]?></p>
                    <p>adresse postal : <?php echo $donnees["adresse_livraison_postal"]?></p>
                    <p>complement d'adresse : <?php echo $donnees["complement_adresse_livraison"]?></p>
                    <p>numéro de rue : <?php echo $donnees["numero_rue_adresse_livraison"]?></p>
                    <p>numéro de batiment : <?php echo $donnees["numero_batiment_adresse_livraison"]?></p>
                    <p>numéro d'apartement : <?php echo $donnees["numero_apart_adresse_livraison"]?></p>
                    <p>code d'interphone : <?php echo $donnees["code_interphone_adresse_livraison"]?></p>
                    <p>code postal : <?php echo $donnees["code_postal_adresse_livraison"]?></p>
                    <p>ville : <?php echo $donnees["ville_adresse_livraison"]?></p>
                </div>
                <!-- facture -->
                <div>
                    <h5>La facture</h5>
                    <p>id : <?php echo $donnees["numero_facture"]?></p>
                    <p>emmetteur : <?php echo $donnees["emmeteur_facture"]?></p>
                </div>
                <div>
                    <h5>Les produits de la commande</h5>
                <?php // produits commandé
                $donneesDetailsCommandes->execute([$donnees["id_commande"]]);
                
                foreach($donneesDetailsCommandes as $produits){?>
                    <div>
                        <h6><?php echo $produits["id_produit_detail_commande"]." - ".$produits["libelle_produit"]?></h6>
                        <p>montant ht : <?php echo $produits["montant_ht_detail_commande"]?></p>
                        <p>quantite : <?php echo $produits["quantite_detail_commande"]?></p>
                        <p>montant ttc : <?php echo $produits["sous_total_produit_detail_commande"]?></p>
                        <p>quantite par unite : <?php echo $produits["quantite_unite"]?></p>
                        <p>unite : <?php echo $produits["unite"]?></p>
                        <p>categorie : <?php echo $produits["libelle_categorie"]?></p>
                        <p>id du vendeur : <?php echo $produits["id_vendeur"]?></p>
                        <p>raison social du vendeur : <?php echo $produits["raison_sociale"]?></p>
                    </div>
                <?php }?>
                </div>
            <?php }?>
        </section>
        <section>
            <?php 
            foreach($donneesPanier as $donnees){?>
                <h4>Votre panier</h4>
                <p>id : <?php echo $donnees["id_panier"]?></p>
                <p>nombre de produits : <?php echo $donnees["nombre_produit_panier"]?></p>
                <p>montant total ttc : <?php echo $donnees["montant_total_ttc_panier"]?></p>
                <p>date de dernière modification : <?php echo $donnees["date_derniere_modif_panier"]?></p>
                <?php 
                $donneesProduitsPanier->execute([$donnees["id_panier"]]);?>
                <div>
                    <h5>Les produits du panier<h5>
                    <?php foreach($donneesProduitsPanier as $produits){?>
                    <div>
                        <h6><?php echo $produits["id_produit"]." - ".$produits["libelle_produit"]?></h6>
                        <p>quantite : <?php echo $produits["quantite_par_produit"]?></p>
                        <p>quantite par unite : <?php echo $produits["quantite_unite"]?></p>
                        <p>unite : <?php echo $produits["unite"]?></p>
                        <p>raison social du vendeur : <?php echo $produits["raison_sociale"]?></p>
                        <p>categorie : <?php echo $produits["libelle_categorie"]?></p>
                    </div>
                    <?php }?>
                </div>
            <?php }?>
        </section>
        <section>
            <h4>Votre liste des futur achats</h4>
            <?php 
            foreach($donneesFuturAchats as $donnees){
            echo "<pre>";
            print_r($donnees);
            echo "</pre>";?>
                
                <p>id produits : <?php echo $donnees["id_produit"]?></p>
                <p>libelle du produit : <?php echo $donnees["libelle_produit"]?></p>
                <p>montant total ttc : <?php echo $donnees["quantite_unite"]?></p>
                <p>date de dernière modification : <?php echo $donnees["raison_sociale"]?></p>
                <p>date de dernière modification : <?php echo $donnees["libelle_categorie"]?></p>
            <?php }?>
        </section>

    </main>
    <?php // require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
