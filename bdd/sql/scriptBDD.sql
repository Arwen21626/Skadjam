-- Active: 1772554420945@@127.0.0.1@8888@postgres@sae3_skadjam
DROP SCHEMA IF EXISTS sae3_skadjam CASCADE;
CREATE SCHEMA sae3_skadjam;
SET SCHEMA 'sae3_skadjam';

/*Création des tables*/

CREATE TABLE sae3_skadjam._compte (
    id_compte SERIAL NOT NULL,
    nom_compte CHARACTER VARYING(100) NOT NULL,
    prenom_compte CHARACTER VARYING(100) NOT NULL,
    adresse_mail CHARACTER VARYING(150) NOT NULL UNIQUE,
    mot_de_passe CHARACTER VARYING(100) NOT NULL,
    numero_telephone CHARACTER(12) NOT NULL,
    bloque BOOLEAN NOT NULL,
    code_secret VARCHAR(255),
    tentative INT DEFAULT 0,
    restant TIMESTAMP DEFAULT NULL
);

ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT pk_compte
        PRIMARY KEY (id_compte);


CREATE TABLE sae3_skadjam._notification (
    id_notification SERIAL NOT NULL,
    type_notification CHARACTER VARYING(100) NOT NULL,
    contenu_notification CHARACTER VARYING(300) NOT NULL,
    titre CHARACTER VARYING(100) NOT NULL,
    id_compte INT NOT NULL
);


CREATE TABLE sae3_skadjam._adresse_livraison (
    id_adresse SERIAL NOT NULL,
    nom CHARACTER VARYING(100) NOT NULL,
    prenom CHARACTER VARYING(100) NOT NULL,
    adresse_postale CHARACTER VARYING(100) NOT NULL,
    complement_adresse CHARACTER VARYING(200),
    numero_rue NUMERIC(5) NOT NULL,
    numero_bat CHARACTER VARYING(10),
    numero_appart CHARACTER VARYING(10),
    code_interphone CHARACTER VARYING(10),
    code_postal NUMERIC(5) NOT NULL,
    ville CHARACTER VARYING(100) NOT NULL,
    sauvegarde BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE sae3_skadjam._adresse (
    id_adresse SERIAL NOT NULL,
    adresse_postale CHARACTER VARYING(100) NOT NULL,
    complement_adresse CHARACTER VARYING(200),
    numero_rue NUMERIC(5) NOT NULL,
    numero_bat CHARACTER VARYING(10),
    numero_appart CHARACTER VARYING(10),
    code_interphone CHARACTER VARYING(10),
    code_postal NUMERIC(5) NOT NULL,
    ville CHARACTER VARYING(100) NOT NULL,
    latitude CHARACTER VARYING(25) DEFAULT NULL,
    longitude CHARACTER VARYING(25) DEFAULT NULL
);


CREATE TABLE sae3_skadjam._habite(
    id_adresse INT NOT NULL,
    id_compte INT NOT NULL
);


CREATE TABLE sae3_skadjam._client (
    id_compte INT PRIMARY KEY REFERENCES sae3_skadjam._compte(id_compte),
    pseudo CHARACTER VARYING(30) NOT NULL,
    date_naissance CHARACTER VARYING(12) NOT NULL,
    id_panier SERIAL NOT NULL UNIQUE
);


CREATE TABLE sae3_skadjam._vendeur (
    id_compte INT PRIMARY KEY REFERENCES sae3_skadjam._compte(id_compte),
    raison_sociale CHARACTER VARYING(100) NOT NULL,
    siren NUMERIC(9) NOT NULL,
    description_vendeur CHARACTER VARYING(500),
    iban CHARACTER VARYING(34) NOT NULL,
    denomination CHARACTER VARYING(75) NOT NULL
);


CREATE TABLE sae3_skadjam._categorie (
    id_categorie SERIAL NOT NULL,
    libelle_categorie CHARACTER VARYING(100) NOT NULL
);


CREATE TABLE sae3_skadjam._carte_bancaire (
    id_carte_bancaire SERIAL NOT NULL,
    numero_carte CHARACTER VARYING(100) NOT NULL,
    cryptogramme CHARACTER VARYING(100) NOT NULL,
    nom CHARACTER VARYING(100) NOT NULL,
    expiration CHARACTER VARYING(5) NOT NULL,
    id_client INT NOT NULL
);


CREATE TABLE sae3_skadjam._futur_achat (
    id_client INT NOT NULL,
    id_produit INT NOT NULL
);


CREATE TABLE sae3_skadjam._produit (
    id_produit SERIAL NOT NULL,
    libelle_produit CHARACTER VARYING(100) NOT NULL,
    description_produit CHARACTER VARYING(300),
    prix_ht NUMERIC (10,2) NOT NULL,
    prix_ttc NUMERIC(10,2) NOT NULL,
    est_masque BOOLEAN NOT NULL,
    prix_remise NUMERIC(6,2) NOT NULL DEFAULT 0.0,
    quantite_stock NUMERIC(9) NOT NULL,
    seuil_alerte NUMERIC(9),
    quantite_unite NUMERIC(9),
    unite CHARACTER VARYING(5) NOT NULL,
    id_categorie INT NOT NULL,
    id_vendeur INT NOT NULL,
    id_tva INT NOT NULL,
    note_moyenne NUMERIC(2,1),
    est_supprime BOOLEAN NOT NULL DEFAULT FALSE,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE sae3_skadjam._tva(
    id_tva SERIAL NOT NULL,
    nom_tva CHARACTER VARYING(50) NOT NULL,
    pourcentage_tva NUMERIC(4,3) NOT NULL
);


CREATE TABLE sae3_skadjam._remise (
    id_remise SERIAL NOT NULL,
    pourcentage_remise NUMERIC(3,2) NOT NULL,
    date_debut_remise CHARACTER VARYING(12) NOT NULL,
    date_fin_remise CHARACTER VARYING(12)
);

CREATE TABLE sae3_skadjam._reduit (
    id_remise INT NOT NULL,
    id_produit INT NOT NULL
);


CREATE TABLE sae3_skadjam._promotion (
    id_promotion SERIAL NOT NULL,
    label CHARACTER VARYING(20),
    date_debut_promotion CHARACTER VARYING(12) NOT NULL,
    date_fin_promotion CHARACTER VARYING(12),
    periodicite NUMERIC(5),
    heure_debut CHARACTER VARYING(5) NOT NULL,
    heure_fin CHARACTER VARYING(5),
    id_vendeur INT NOT NULL,
    id_photo INT
);


CREATE TABLE sae3_skadjam._promu (
    id_promotion INT NOT NULL,
    id_produit INT NOT NULL
);


CREATE TABLE sae3_skadjam._photo (
    id_photo SERIAL NOT NULL,
    url_photo CHARACTER VARYING(200) NOT NULL,
    description_photo CHARACTER VARYING(300),
    alt CHARACTER VARYING(100) NOT NULL,
    titre CHARACTER VARYING(100) NOT NULL
);


CREATE TABLE sae3_skadjam._montre (
    id_photo INT NOT NULL,
    id_produit INT NOT NULL
);


CREATE TABLE sae3_skadjam._presente (
    id_photo INT NOT NULL,
    id_vendeur INT NOT NULL
);


CREATE TABLE sae3_skadjam._panier (
    id_panier INT NOT NULL,
    nb_produit_total NUMERIC(9) NOT NULL,
    montant_total_ttc NUMERIC(10,2) NOT NULL,
    date_derniere_modif CHARACTER VARYING(12),
    id_client INT NOT NULL
);


CREATE TABLE sae3_skadjam._donne (
    id_panier INT NOT NULL,
    id_commande INT NOT NULL
);


CREATE TABLE sae3_skadjam._facture ( 
    numero_facture SERIAL NOT NULL,
    emetteur INT NOT NULL,
    id_commande INT NOT NULL
);

CREATE TABLE sae3_skadjam._commande (
    id_commande SERIAL NOT NULL,
    id_suivi CHARACTER VARYING(16),
    id_adresse INTEGER NOT NULL,
    etat CHARACTER VARYING(30) NOT NULL,
    date_commande CHARACTER (12) NOT NULL,
    montant_total_ttc NUMERIC(10,2) NOT NULL,
    id_client INT NOT NULL
);


CREATE TABLE sae3_skadjam._details (
    montant_ht NUMERIC(10,2) NOT NULL,
    quantite NUMERIC(5) NOT NULL,
    sous_total NUMERIC(10,2) NOT NULL,
    id_commande INT NOT NULL,
    id_produit INT NOT NULL
);

CREATE TABLE sae3_skadjam._contient (
    id_produit INT NOT NULL,
    id_panier INT NOT NULL,
    quantite_par_produit NUMERIC(5) NOT NULL
);

CREATE TABLE sae3_skadjam._avis (
    id_avis SERIAL NOT NULL,
    nb_etoile INT NOT NULL,
    nb_pouce_haut INT NOT NULL DEFAULT 0,
    nb_pouce_bas INT NOT NULL DEFAULT 0,
    contenu_commentaire VARCHAR(500),
    id_produit INT NOT NULL,
    id_compte INT NOT NULL,
    signaler BOOLEAN NOT NULL DEFAULT FALSE
);

CREATE TABLE sae3_skadjam._pouces (
    id_pouce SERIAL NOT NULL,
    id_avis INT NOT NULL,
    id_compte INT NOT NULL,
    pouce SMALLINT NOT NULL,
    UNIQUE(id_avis, id_compte)
);

CREATE TABLE sae3_skadjam._reponse (
    contenu_reponse VARCHAR(500) NOT NULL,
    id_compte INT NOT NULL,
    id_avis INT NOT NULL
);

CREATE TABLE sae3_skadjam._a_signaler (
    id_avis INT NOT NULL,
    id_compte INT NOT NULL
);

CREATE TABLE sae3_skadjam._appuie (
    id_photo INT NOT NULL,
    id_avis INT NOT NULL
);

/*Contraintes de clés primaires*/

ALTER TABLE sae3_skadjam._notification
    ADD CONSTRAINT pk_notification
        PRIMARY KEY (id_notification);

ALTER TABLE sae3_skadjam._adresse
    ADD CONSTRAINT pk_adresse
        PRIMARY KEY (id_adresse);

ALTER TABLE sae3_skadjam._adresse_livraison
    ADD CONSTRAINT pk_adresse_livraison
        PRIMARY KEY (id_adresse);

ALTER TABLE sae3_skadjam._carte_bancaire
    ADD CONSTRAINT pk_carte_bancaire
        PRIMARY KEY (id_carte_bancaire);

ALTER TABLE sae3_skadjam._categorie
    ADD CONSTRAINT pk_categorie
        PRIMARY KEY (id_categorie);

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT pk_produit
        PRIMARY KEY (id_produit);

ALTER TABLE sae3_skadjam._futur_achat
    ADD CONSTRAINT pk_futur_achat
        PRIMARY KEY (id_produit, id_client);

ALTER TABLE sae3_skadjam._remise
    ADD CONSTRAINT pk_remise
        PRIMARY KEY (id_remise);

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT pk_promotion
        PRIMARY KEY (id_promotion);

ALTER TABLE sae3_skadjam._photo
    ADD CONSTRAINT pk_photo
        PRIMARY KEY (id_photo);

ALTER TABLE sae3_skadjam._tva
    ADD CONSTRAINT pk_tva
        PRIMARY KEY (id_tva);

ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT pk_panier
        PRIMARY KEY (id_panier);

ALTER TABLE sae3_skadjam._contient
    ADD CONSTRAINT pk_contient
        PRIMARY KEY (id_panier, id_produit);

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT pk_commande
        PRIMARY KEY (id_commande);

ALTER TABLE sae3_skadjam._facture
    ADD CONSTRAINT pk_facture
        PRIMARY KEY (numero_facture);

ALTER TABLE sae3_skadjam._details
    ADD CONSTRAINT pk_details
        PRIMARY KEY (id_produit, id_commande);

ALTER TABLE sae3_skadjam._habite
    ADD CONSTRAINT pk_habite
        PRIMARY KEY (id_compte, id_adresse);

ALTER TABLE sae3_skadjam._donne
    ADD CONSTRAINT pk_donne
        PRIMARY KEY (id_panier, id_commande);

ALTER TABLE sae3_skadjam._montre
    ADD CONSTRAINT pk_montre
        PRIMARY KEY (id_photo, id_produit);

ALTER TABLE sae3_skadjam._promu
    ADD CONSTRAINT pk_promu
        PRIMARY KEY (id_promotion, id_produit);

ALTER TABLE sae3_skadjam._reduit
    ADD CONSTRAINT pk_reduit
        PRIMARY KEY (id_remise, id_produit);

ALTER TABLE sae3_skadjam._presente
    ADD CONSTRAINT pk_presente
        PRIMARY KEY (id_photo, id_vendeur);

ALTER TABLE sae3_skadjam._avis
    ADD CONSTRAINT pk_avis
        PRIMARY KEY (id_avis);
        
ALTER TABLE sae3_skadjam._pouces
    ADD CONSTRAINT pk_pouces
        PRIMARY KEY (id_pouce);

ALTER TABLE sae3_skadjam._reponse
    ADD CONSTRAINT pk_reponse
        PRIMARY KEY (id_avis);
        
ALTER TABLE sae3_skadjam._appuie
    ADD CONSTRAINT pk_appuie
        PRIMARY KEY (id_photo, id_avis);
        
ALTER TABLE sae3_skadjam._a_signaler
    ADD CONSTRAINT pk_a_signaler
        PRIMARY KEY (id_avis, id_compte);

/*Contraintes clés étrangères*/

ALTER TABLE sae3_skadjam._notification
    ADD CONSTRAINT fk_notification_compte
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._compte(id_compte);

ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT fk_panier_client_id_compte
        FOREIGN KEY (id_client)
            REFERENCES sae3_skadjam._client(id_compte);
            
ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT fk_panier_client_id_panier
        FOREIGN KEY (id_panier)
            REFERENCES sae3_skadjam._client(id_panier);

ALTER TABLE sae3_skadjam._carte_bancaire
    ADD CONSTRAINT fk_carte_bancaire_client
        FOREIGN KEY (id_client)
            REFERENCES sae3_skadjam._client(id_compte);

ALTER TABLE sae3_skadjam._habite
    ADD CONSTRAINT fk_habite_compte
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._compte(id_compte);

ALTER TABLE sae3_skadjam._habite
    ADD CONSTRAINT fk_habite_adresse
        FOREIGN KEY (id_adresse)
            REFERENCES sae3_skadjam._adresse(id_adresse);

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT fk_produit_vendeur
        FOREIGN KEY (id_vendeur)
            REFERENCES sae3_skadjam._vendeur(id_compte);

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT fk_produit_categorie
        FOREIGN KEY (id_categorie)
            REFERENCES sae3_skadjam._categorie(id_categorie);

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT fk_produit_tva
        FOREIGN KEY (id_tva)
            REFERENCES sae3_skadjam._tva(id_tva);

ALTER TABLE sae3_skadjam._reduit
    ADD CONSTRAINT fk_reduit_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._reduit
    ADD CONSTRAINT fk_reduit_remise
        FOREIGN KEY (id_remise)
            REFERENCES sae3_skadjam._remise(id_remise);

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT fk_promotion_vendeur
        FOREIGN KEY (id_vendeur)
            REFERENCES sae3_skadjam._vendeur(id_compte);

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT fk_promotion_photo
        FOREIGN KEY (id_photo)
            REFERENCES sae3_skadjam._photo(id_photo);

ALTER TABLE sae3_skadjam._promu
    ADD CONSTRAINT fk_promu_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._promu
    ADD CONSTRAINT fk_promu_promotion
        FOREIGN KEY (id_promotion)
            REFERENCES sae3_skadjam._promotion(id_promotion);

ALTER TABLE sae3_skadjam._montre
    ADD CONSTRAINT fk_montre_photo
        FOREIGN KEY (id_photo)
            REFERENCES sae3_skadjam._photo(id_photo);

ALTER TABLE sae3_skadjam._montre
    ADD CONSTRAINT fk_montre_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._futur_achat
    ADD CONSTRAINT fk_futur_achat_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._futur_achat
    ADD CONSTRAINT fk_futur_achat_client
        FOREIGN KEY (id_client)
            REFERENCES sae3_skadjam._client(id_compte);

ALTER TABLE sae3_skadjam._contient
    ADD CONSTRAINT fk_contient_panier
        FOREIGN KEY (id_panier)
            REFERENCES sae3_skadjam._panier(id_panier);

ALTER TABLE sae3_skadjam._contient
    ADD CONSTRAINT fk_contient_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._presente
    ADD CONSTRAINT fk_presente_photo
        FOREIGN KEY (id_photo)
            REFERENCES sae3_skadjam._photo(id_photo);

ALTER TABLE sae3_skadjam._presente
    ADD CONSTRAINT fk_presente_vendeur
        FOREIGN KEY (id_vendeur)
            REFERENCES sae3_skadjam._vendeur(id_compte);

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT fk_commande_client
        FOREIGN KEY (id_client)
            REFERENCES sae3_skadjam._client(id_compte);

--ALTER TABLE sae3_skadjam._commande
--    ADD CONSTRAINT fk_commande_facture
--        FOREIGN KEY (id_facture)
--            REFERENCES sae3_skadjam._facture(numero_facture);

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT fk_commande_adresse
        FOREIGN KEY (id_adresse)
            REFERENCES sae3_skadjam._adresse_livraison(id_adresse);

ALTER TABLE sae3_skadjam._donne
    ADD CONSTRAINT fk_donne_panier
        FOREIGN KEY (id_panier)
            REFERENCES sae3_skadjam._panier(id_panier);

ALTER TABLE sae3_skadjam._donne
    ADD CONSTRAINT fk_donne_commande
        FOREIGN KEY (id_commande)
            REFERENCES sae3_skadjam._commande(id_commande);

ALTER TABLE sae3_skadjam._details
    ADD CONSTRAINT fk_details_commande
        FOREIGN KEY (id_commande)
            REFERENCES sae3_skadjam._commande(id_commande);

ALTER TABLE sae3_skadjam._details
    ADD CONSTRAINT fk_details_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._facture
    ADD CONSTRAINT fk_facture_commande
        FOREIGN KEY (id_commande)
            REFERENCES sae3_skadjam._commande(id_commande);

ALTER TABLE sae3_skadjam._facture
    ADD CONSTRAINT fk_facture_vendeur
        FOREIGN KEY (emetteur)
            REFERENCES sae3_skadjam._vendeur(id_compte);
            
ALTER TABLE sae3_skadjam._appuie
    ADD CONSTRAINT fk_appuie_avis
        FOREIGN KEY (id_avis)
            REFERENCES sae3_skadjam._avis(id_avis);

ALTER TABLE sae3_skadjam._appuie
    ADD CONSTRAINT fk_appuie_photo
        FOREIGN KEY (id_photo)
            REFERENCES sae3_skadjam._photo(id_photo);

ALTER TABLE sae3_skadjam._avis
    ADD CONSTRAINT fk_avis_produit
        FOREIGN KEY (id_produit)
            REFERENCES sae3_skadjam._produit(id_produit);

ALTER TABLE sae3_skadjam._avis
    ADD CONSTRAINT fk_avis_client
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._client(id_compte);
            
ALTER TABLE sae3_skadjam._pouces
    ADD CONSTRAINT fk_pouces_avis
        FOREIGN KEY (id_avis)
            REFERENCES sae3_skadjam._avis(id_avis);

ALTER TABLE sae3_skadjam._pouces
    ADD CONSTRAINT fk_pouces_client
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._client(id_compte);
            
ALTER TABLE sae3_skadjam._reponse
    ADD CONSTRAINT fk_reponse_vendeur
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._vendeur(id_compte);

ALTER TABLE sae3_skadjam._reponse
    ADD CONSTRAINT fk_reponse_avis
        FOREIGN KEY (id_avis)
            REFERENCES sae3_skadjam._avis(id_avis);
            
ALTER TABLE sae3_skadjam._a_signaler
    ADD CONSTRAINT fk_a_signaler_avis
        FOREIGN KEY (id_avis)
            REFERENCES sae3_skadjam._avis(id_avis);

ALTER TABLE sae3_skadjam._a_signaler
    ADD CONSTRAINT fk_a_signaler_compte
        FOREIGN KEY (id_compte)
            REFERENCES sae3_skadjam._compte(id_compte);
            

/*Contraintes de vérifications*/


ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT ch_compte_num_telephone
        CHECK (numero_telephone LIKE '+33%');


ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT ch_compte_adresse_mail
        CHECK (adresse_mail LIKE '%@%.%');

ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT ch_compte_nom
        CHECK (nom_compte ~ '[a-zA-Z -]{1,}');

ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT ch_compte_prenom
        CHECK (prenom_compte ~ '[A-Z][a-z -]{0,}');

ALTER TABLE sae3_skadjam._notification
    ADD CONSTRAINT ch_notification_type
        CHECK (type_notification IN ('Gestion de stock', 'Avis', 'Commande', 'Suivi livraison', 'Signalement', 'Alerte produit', 'Autre'));

ALTER TABLE sae3_skadjam._adresse
    ADD CONSTRAINT ch_adresse_code_postal
        CHECK (01000<code_postal AND code_postal<99999);

ALTER TABLE sae3_skadjam._adresse
    ADD CONSTRAINT ch_adresse_ville
        CHECK (ville ~ '[a-zA-Z -]{1,}');

ALTER TABLE sae3_skadjam._adresse_livraison
    ADD CONSTRAINT ch_adresse_livraison_code_postal
        CHECK (01000<code_postal AND code_postal<99999);

ALTER TABLE sae3_skadjam._adresse_livraison
    ADD CONSTRAINT ch_adresse_livraison_ville
        CHECK (ville ~ '[a-zA-Z -]{1,}');
        
ALTER TABLE sae3_skadjam._client
    ADD CONSTRAINT ch_client_date_naissance
        CHECK (date_naissance ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._categorie
    ADD CONSTRAINT ch_categorie
        CHECK (libelle_categorie ~ '[a-zA-Z -]{1,}');

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT ch_commande_date_commande
        CHECK (date_commande ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT ch_commande_etat
        CHECK (etat IN ('En attente', 'Expédié', 'En cours de livraison', 'Livré', 'Inconnu'));

ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT ch_panier_date_derniere_modif
        CHECK (date_derniere_modif ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_libelle_produit
        CHECK (libelle_produit ~ '[a-zA-Z0-9 -]{1,}');

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_unite
        CHECK (unite IN ('Piece', 'Litre', 'cl', 'g', 'kg', 'S', 'M', 'L', 'XL', 'XXL', 'm', 'cm'));

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_raison_social
        CHECK (raison_sociale ~ '[a-zA-Z0-9 -]{1,}');

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_siren
        CHECK (000000000<siren AND siren<=999999999);

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_iban
        CHECK (iban LIKE 'FR%');

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_denomination
        CHECK (denomination ~ '[a-zA-Z0-9 -]{1,}');

ALTER TABLE sae3_skadjam._remise
    ADD CONSTRAINT ch_remise_date_debut
        CHECK (date_debut_remise ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT ch_promotion_date_debut
        CHECK (date_debut_promotion ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT ch_promotion_date_fin
        CHECK (date_fin_promotion ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT ch_promotion_heure_debut
        CHECK (heure_debut ~ '([01][0-9]|2[0-3])'); -- modèle 24 heure par journée

ALTER TABLE sae3_skadjam._promotion
    ADD CONSTRAINT ch_promotion_heure_fin
        CHECK (heure_fin ~ '([01][0-9]|2[0-3])'); -- modèle 24 heure par journée



/*Fonctions*/

CREATE OR REPLACE FUNCTION calcul_note_moyenne()
RETURNS TRIGGER AS $$
DECLARE 
  moyenne_notes REAL := 0;
BEGIN
  PERFORM * FROM sae3_skadjam._avis;
  IF FOUND THEN
    UPDATE sae3_skadjam._produit SET note_moyenne = (SELECT AVG(nb_etoile) FROM sae3_skadjam._avis WHERE id_produit = new.id_produit) WHERE id_produit = new.id_produit;
  END IF;
  RETURN new;
END;
$$ language plpgsql;

CREATE TRIGGER trig_produit_note_moyenne
  AFTER INSERT OR UPDATE OR DELETE ON sae3_skadjam._avis
  FOR EACH ROW
  EXECUTE FUNCTION calcul_note_moyenne();

--calcule du prix remiser
CREATE OR REPLACE FUNCTION calculer_prix_remiser()
RETURNS TRIGGER AS $$
DECLARE
  prix_remiser NUMERIC(10,2) := 0.0;
  id_prod sae3_skadjam._produit.id_produit%TYPE;
BEGIN                
  SELECT p.id_produit INTO id_prod FROM sae3_skadjam._produit p
    JOIN sae3_skadjam._reduit r ON r.id_produit = p.id_produit
    WHERE r.id_remise = new.id_remise;
  SELECT (p.prix_ttc * (1-new.pourcentage_remise)) AS prix_avec_remise INTO prix_remiser 
    FROM sae3_skadjam._produit p
    WHERE p.id_produit = id_prod;

  UPDATE sae3_skadjam._produit SET prix_remise = prix_remiser WHERE id_produit = id_prod;
  RETURN new;
END;
$$ language plpgsql;

CREATE OR REPLACE TRIGGER tg_calculer_prix_remiser_remise
  AFTER INSERT OR UPDATE ON sae3_skadjam._remise
  FOR EACH ROW
  EXECUTE FUNCTION calculer_prix_remiser();

  
--après création de la commande
CREATE OR REPLACE FUNCTION creer_commande_et_facture()
RETURNS TRIGGER AS $$
BEGIN
  WITH tab_commande AS
    (SELECT c.id_panier, c.quantite_par_produit, p.id_produit, p.prix_ht, p.prix_remise, p.prix_ttc, p.id_vendeur
    FROM sae3_skadjam._produit p
      INNER JOIN sae3_skadjam._contient c
      ON p.id_produit = c.id_produit
      WHERE c.id_panier = NEW.id_panier)
        
  INSERT INTO sae3_skadjam._details (montant_ht, quantite, sous_total, id_commande, id_produit)
    (SELECT prix_ht, quantite_par_produit, prix_remise * quantite_par_produit as sous_total , new.id_commande, id_produit
    FROM tab_commande);
  
  INSERT INTO sae3_skadjam._facture (emetteur, id_commande)
    (SELECT DISTINCT p.id_vendeur, new.id_commande
    FROM sae3_skadjam._commande c
      INNER JOIN sae3_skadjam._details d ON c.id_commande = d.id_commande
      INNER JOIN sae3_skadjam._produit p ON p.id_produit = d.id_produit
    WHERE d.id_commande = new.id_commande);
    
    UPDATE sae3_skadjam._commande SET montant_total_ttc = (SELECT sum(sous_total) FROM sae3_skadjam._details WHERE id_commande = new.id_commande) WHERE id_commande = new.id_commande;
    
  RETURN new;
END;
$$ language plpgsql;

CREATE OR REPLACE TRIGGER trig_creer_commande_et_facture
  AFTER INSERT ON sae3_skadjam._donne
  FOR EACH ROW
  EXECUTE FUNCTION creer_commande_et_facture();


-- calcule prix remiser à la création d'un produit 
CREATE OR REPLACE FUNCTION calculer_prix_remiser_produit()
RETURNS TRIGGER AS $$
DECLARE
  prix_remiser NUMERIC(10,2);
  prix_avec_taxe NUMERIC(10,2);
BEGIN
  SELECT prix_ttc INTO prix_remiser FROM sae3_skadjam._produit WHERE id_produit = new.id_produit;

  UPDATE sae3_skadjam._produit SET prix_remise = prix_remiser WHERE id_produit = new.id_produit;
  RETURN new;
END;
$$ language plpgsql;

CREATE OR REPLACE TRIGGER tg_calculer_prix_remiser_produit
  AFTER INSERT ON sae3_skadjam._produit
  FOR EACH ROW
  EXECUTE FUNCTION calculer_prix_remiser_produit();
  
-- calcule du prix ttc quand une remise est supprimer
CREATE OR REPLACE FUNCTION calculer_delete_reduit()
RETURNS TRIGGER AS $$
DECLARE
  prix_remiser NUMERIC(10,2) := 4.0;
BEGIN
  SELECT prix_ttc INTO prix_remiser FROM sae3_skadjam._produit p WHERE id_produit = old.id_produit;

  UPDATE sae3_skadjam._produit SET prix_remise = prix_remiser WHERE id_produit = old.id_produit;
  RETURN old;
END;
$$ language plpgsql;

CREATE OR REPLACE TRIGGER tg_calculer_delete_reduit
  AFTER DELETE ON sae3_skadjam._reduit
  FOR EACH ROW
  EXECUTE FUNCTION calculer_delete_reduit();
  
-- mise a jour de prix_ttc et prix_remise quand _produit est modifier
CREATE OR REPLACE FUNCTION calculer_prix_remiser_update_produit()
RETURNS TRIGGER AS $$
DECLARE
  coef_tva NUMERIC(6,4);
  prix_ttc_calc NUMERIC(10,2);
  prix_remise_calc NUMERIC(10,2);
BEGIN
  -- TVA
  SELECT 1 + t.pourcentage_tva
    INTO coef_tva
    FROM sae3_skadjam._tva t
    WHERE t.id_tva = NEW.id_tva;

  prix_ttc_calc := NEW.prix_ht * coef_tva;

  -- Prix remiser
  PERFORM id_produit FROM sae3_skadjam._reduit WHERE id_produit = new.id_produit;
  IF FOUND THEN
    SELECT prix_ttc_calc * (1 - r.pourcentage_remise)
      INTO prix_remise_calc
      FROM sae3_skadjam._reduit rd
      JOIN sae3_skadjam._remise r ON r.id_remise = rd.id_remise
      WHERE rd.id_produit = NEW.id_produit;
  ELSE 
    prix_remise_calc := prix_ttc_calc;
  END IF;

  NEW.prix_ttc := prix_ttc_calc;
  NEW.prix_remise := prix_remise_calc;

  RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER tg_calculer_prix_remiser_update_produit
BEFORE UPDATE OF prix_ht ON sae3_skadjam._produit
FOR EACH ROW
EXECUTE FUNCTION calculer_prix_remiser_update_produit();


-- supprime la prommotion des produit supprimer
CREATE OR REPLACE FUNCTION supprimer_promo_a_la_suppression_de_produit()
RETURNS TRIGGER AS $$
DECLARE
  id_produit_supprimer INTEGER;
BEGIN
  PERFORM * FROM sae3_skadjam._promu WHERE id_produit = new.id_produit;
  
  IF FOUND AND new.est_supprime != old.est_supprime THEN
    SELECT id_promotion INTO id_produit_supprimer FROM sae3_skadjam._promu WHERE id_produit = new.id_produit;
    DELETE FROM sae3_skadjam._promu WHERE id_produit = new.id_produit;
    DELETE FROM sae3_skadjam._promotion WHERE id_promotion = id_produit_supprimer;
    
  END IF;
  RETURN new;
END;
$$ language plpgsql;

CREATE TRIGGER trig_supprimer_promo_a_la_suppression_de_produit
  AFTER UPDATE ON sae3_skadjam._produit
  FOR EACH ROW
  EXECUTE FUNCTION supprimer_promo_a_la_suppression_de_produit();
