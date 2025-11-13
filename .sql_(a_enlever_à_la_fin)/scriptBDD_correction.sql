DROP SCHEMA IF EXISTS sae3_skadjam CASCADE;
CREATE SCHEMA sae3_skadjam;
SET SCHEMA 'sae3_skadjam';

/*Création des tables*/

CREATE TABLE sae3_skadjam._compte (
    id_compte SERIAL NOT NULL,
    nom_compte CHARACTER VARYING(100) NOT NULL,
    prenom_compte CHARACTER VARYING(100) NOT NULL,
    adresse_mail CHARACTER VARYING(150) NOT NULL UNIQUE,
    motDePasse CHARACTER VARYING(100) NOT NULL,
    numero_telephone CHARACTER(12) NOT NULL,
    bloque BOOLEAN NOT NULL
    
);

ALTER TABLE sae3_skadjam._compte
    ADD CONSTRAINT pk_compte
        PRIMARY KEY (id_compte);


CREATE TABLE sae3_skadjam._notification (
    id_notification SERIAL NOT NULL,
    type_notification CHARACTER VARYING(100) NOT NULL,
    contenu_notification CHARACTER VARYING(300) NOT NULL,
    titre CHARACTER VARYING(100) NOT NULL,
    id_compte SERIAL NOT NULL
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
    ville CHARACTER VARYING(100) NOT NULL
);


CREATE TABLE sae3_skadjam._habite(
    id_adresse SERIAL NOT NULL,
    id_compte SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._client (
    id_compte SERIAL PRIMARY KEY REFERENCES sae3_skadjam._compte(id_compte),
    pseudo CHARACTER VARYING(30) NOT NULL,
    date_naissance CHARACTER VARYING(12) NOT NULL,
    id_panier SERIAL NOT NULL UNIQUE
);


CREATE TABLE sae3_skadjam._vendeur (
    id_compte SERIAL PRIMARY KEY REFERENCES sae3_skadjam._compte(id_compte),
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
    numero_carte NUMERIC(16) NOT NULL,
    cryptogramme NUMERIC(3) NOT NULL,
    nom CHARACTER VARYING(100) NOT NULL,
    expiration CHARACTER VARYING(5) NOT NULL,
    id_client SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._futur_achat (
    id_client SERIAL NOT NULL,
    id_produit SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._produit (
    id_produit SERIAL NOT NULL,
    libelle_produit CHARACTER VARYING(100) NOT NULL,
    description_produit CHARACTER VARYING(300),
    prix_ht NUMERIC (10,2) NOT NULL,
    prix_ttc NUMERIC(10,2) NOT NULL,
    est_masque BOOLEAN NOT NULL,
    prix_remise NUMERIC(6,2),
    quantite_stock NUMERIC(9) NOT NULL,
    seuil_alerte NUMERIC(9),
    quantite_unite NUMERIC(9),
    unite CHARACTER VARYING(5) NOT NULL,
    id_categorie SERIAL NOT NULL,
    id_vendeur SERIAL NOT NULL,
    id_tva SERIAL NOT NULL,
    note_moyenne NUMERIC(2,1)
);

CREATE TABLE sae3_skadjam._tva(
    id_tva SERIAL NOT NULL,
    nom_tva CHARACTER VARYING(50) NOT NULL,
    pourcentage_tva NUMERIC(1,2) NOT NULL
);


CREATE TABLE sae3_skadjam._remise (
    id_remise SERIAL NOT NULL,
    pourcentage_remise NUMERIC(1,2) NOT NULL,
    date_debut_remise CHARACTER VARYING(12) NOT NULL,
    date_fin_remise CHARACTER VARYING(12)
);

CREATE TABLE sae3_skadjam._reduit (
    id_remise SERIAL NOT NULL,
    id_produit SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._promotion (
    id_promotion SERIAL NOT NULL,
    date_debut_promotion CHARACTER VARYING(12) NOT NULL,
    date_fin_promotion CHARACTER VARYING(12),
    periodicite NUMERIC(5),
    heure_debut CHARACTER VARYING(5) NOT NULL,
    heure_fin CHARACTER VARYING(5),
    id_vendeur SERIAL NOT NULL,
    id_photo SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._promu (
    id_promotion SERIAL NOT NULL,
    id_produit SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._photo (
    id_photo SERIAL NOT NULL,
    url_photo CHARACTER VARYING(200) NOT NULL,
    description_photo CHARACTER VARYING(300),
    alt CHARACTER VARYING(100) NOT NULL,
    titre CHARACTER VARYING(100) NOT NULL
);


CREATE TABLE sae3_skadjam._montre (
    id_photo SERIAL NOT NULL,
    id_produit SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._presente (
    id_photo SERIAL NOT NULL,
    id_vendeur SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._panier (
    id_panier SERIAL NOT NULL,
    nb_produit_total NUMERIC(9) NOT NULL,
    montant_total_ttc NUMERIC(10,2) NOT NULL,
    date_derniere_modif CHARACTER VARYING(12) NOT NULL,
    id_client SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._donne (
    id_panier SERIAL NOT NULL,
    id_commande SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._facture ( 
    numero_facture SERIAL NOT NULL,
    montant_ht NUMERIC(10,2) NOT NULL,
    emetteur CHARACTER(6) DEFAULT 'Alizon', 
    destinataire SERIAL NOT NULL,
    date_commande CHARACTER VARYING(12) NOT NULL,
    id_commande SERIAL NOT NULL
);

CREATE TABLE sae3_skadjam._commande (
    id_commande SERIAL NOT NULL,
    etat CHARACTER VARYING(30) NOT NULL,
    date_commande CHARACTER (12) NOT NULL,
    montant_total_ttc NUMERIC(10,2) NOT NULL,
    id_client SERIAL NOT NULL,
    id_facture SERIAL NOT NULL
);


CREATE TABLE sae3_skadjam._details (
    montant_ht NUMERIC(10,2) NOT NULL,
    tva NUMERIC(1,2) NOT NULL,
    quantite NUMERIC(5) NOT NULL,
    sous_total NUMERIC(10,2) NOT NULL,
    id_commande SERIAL NOT NULL,
    id_produit SERIAL NOT NULL
);

CREATE TABLE sae3_skadjam._contient (
    id_produit SERIAL NOT NULL,
    id_panier SERIAL NOT NULL,
    quantite_par_produit NUMERIC(5) NOT NULL
);

/*Contraintes de clés primaires*/

ALTER TABLE sae3_skadjam._notification
    ADD CONSTRAINT pk_notification
        PRIMARY KEY (id_notification);

ALTER TABLE sae3_skadjam._adresse
    ADD CONSTRAINT pk_adresse
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

ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT fk_commande_facture
        FOREIGN KEY (id_facture)
            REFERENCES sae3_skadjam._facture(numero_facture);

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
    ADD CONSTRAINT fk_facture_client
        FOREIGN KEY (destinataire)
            REFERENCES sae3_skadjam._client(id_compte);


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
        CHECK (etat IN ('En attente', 'Préparé', 'Expédié', 'En cours de livraison', 'Livré'));

/*
ALTER TABLE sae3_skadjam._commande
    ADD CONSTRAINT ch_commande_montant_total_ttc
        CHECK (montant_total_ttc = SUM(sae3_skadjam._details.sous_total));

ALTER TABLE sae3_skadjam._facture
     ADD CONSTRAINT ch_facture_montant_ht
        CHECK (montant_ht = SUM(sae3_skadjam._details.montant_ht));
*/

ALTER TABLE sae3_skadjam._details
    ADD CONSTRAINT ch_details_sous_total
        CHECK (sous_total = (montant_ht*(1+tva))*quantite);

/*
ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT ch_panier_nb_produit_total
        CHECK (nb_produit_total = SUM(sae3_skadjam._contient.quantite_par_produit));

ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT ch_panier_montant_total_ttc
        CHECK (montant_total_ttc = SUM(sae3_skadjam._produit.prix_ttc));
*/

ALTER TABLE sae3_skadjam._panier
    ADD CONSTRAINT ch_panier_date_derniere_modif
        CHECK (date_derniere_modif ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_libelle_produit
        CHECK (libelle_produit ~ '[a-zA-Z -]{1,}');

/*
ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_prix_ttc
        CHECK (prix_ttc = prix_ht*(1+tva));

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_prix_remise
        CHECK (prix_remise = prix_ttc * (1-sae3_skadjam._remise.pourcentage_remise));
*/

ALTER TABLE sae3_skadjam._produit
    ADD CONSTRAINT ch_produit_unite
        CHECK (unite IN ('piece', 'L', 'cl', 'g', 'kg', 'S', 'M', 'L', 'XL', 'XXL', 'm', 'cm'));

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_raison_social
        CHECK (raison_sociale ~ '[a-zA-Z0-9 -]{1,}');


ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_siren
        CHECK (000000000<=siren AND siren<=999999999);

ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_iban
        CHECK (iban LIKE 'FR%');


ALTER TABLE sae3_skadjam._vendeur
    ADD CONSTRAINT ch_vendeur_denomination
        CHECK (denomination ~ '[a-zA-Z0-9 -]{1,}');

ALTER TABLE sae3_skadjam._remise
    ADD CONSTRAINT ch_remise_date_debut
        CHECK (date_debut_remise ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

ALTER TABLE sae3_skadjam._remise
    ADD CONSTRAINT ch_remise_date_fin
        CHECK (date_fin_remise ~ '([0-2][0-9]|3[01])/(0[0-9]|1[0-2])/[0-9]{4}');

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

CREATE OR REPLACE FUNCTION calcul_commande_montant_ttc()
RETURNS TRIGGER AS $$
DECLARE 
  montant_total_ttc REAL := 0;
BEGIN
  montant_total_ttc := SUM(sous_total) FROM sae3_skadjam._details;
  RETURN montant_total_ttc;
END;
$$ language plpgsql;

CREATE TRIGGER trig_commmande
  BEFORE INSERT ON sae3_skadjam._commande
  FOR EACH ROW
  EXECUTE FUNCTION calcul_commande_montant_ttc();
  


CREATE OR REPLACE FUNCTION calcul_facture_montant_ht()
RETURNS TRIGGER AS $$
DECLARE 
  montant_ht REAL := 0;
BEGIN
  montant_ht := SUM(montant_ht) FROM sae3_skadjam._details;
  RETURN montant_ht;
END;
$$ language plpgsql;

CREATE TRIGGER trig_facture
  BEFORE INSERT ON sae3_skadjam._facture
  FOR EACH ROW
  EXECUTE FUNCTION calcul_facture_montant_ht();
  


CREATE OR REPLACE FUNCTION calcul_details_sous_total()
RETURNS TRIGGER AS $$
DECLARE 
  sous_total REAL := 0;
BEGIN
  sous_total := (montant_ht*(1+tva))*quantite FROM sae3_skadjam._details;
  RETURN sous_total;
END;
$$ language plpgsql;



CREATE OR REPLACE FUNCTION details_montant_ht()
RETURNS TRIGGER AS $$
DECLARE 
  montant_ht REAL := prix_ht 
  FROM sae3_skadjam._produit
    INNER JOIN sae3_skadjam._details
      ON sae3_skadjam._details.id_produit = sae3_skadjam._produit.id_produit;
BEGIN
  RETURN montant_ht;
END;
$$ language plpgsql;

CREATE OR REPLACE FUNCTION details_tva()
RETURNS TRIGGER AS $$
DECLARE 
  tva REAL := pourcentage_tva
  FROM sae3_skadjam._details
    INNER JOIN sae3_skadjam._produit 
      ON sae3_skadjam._details.id_produit = sae3_skadjam._produit.id_produit
     INNER JOIN sae3_skadjam._tva
      ON sae3_skadjam._tva.id_tva = sae3_skadjam._produit.id_tva;
BEGIN
  RETURN tva;
END;
$$ language plpgsql;

CREATE TRIGGER trig_details_calcul_sous_total
  BEFORE INSERT ON sae3_skadjam._details
  FOR EACH ROW
  EXECUTE FUNCTION calcul_details_sous_total();

CREATE TRIGGER trig_details_montant_ht
  AFTER INSERT ON sae3_skadjam._details
  FOR EACH ROW
  EXECUTE FUNCTION details_montant_ht();
  
CREATE TRIGGER trig_details_tva
  AFTER INSERT ON sae3_skadjam._details
  FOR EACH ROW
  EXECUTE FUNCTION details_tva();
  


CREATE OR REPLACE FUNCTION calcul_panier_produit_total()
RETURNS TRIGGER AS $$
DECLARE 
  nb_prod INTEGER := 0;
BEGIN
  nb_prod := SUM(quantite_par_produit) FROM sae3_skadjam._contient;
  RETURN nb_prod;
END;
$$ language plpgsql;

CREATE TRIGGER trig_panier_nb_produit
  BEFORE INSERT ON sae3_skadjam._commande
  FOR EACH ROW
  EXECUTE FUNCTION calcul_panier_produit_total();
  


CREATE OR REPLACE FUNCTION calcul_panier_montant_total_ttc()
RETURNS TRIGGER AS $$
DECLARE 
  montant_total REAL := 0;
BEGIN
  montant_total := SUM(prix_ttc) FROM sae3_skadjam._produit;
  RETURN montant_total;
END;
$$ language plpgsql;

CREATE TRIGGER trig_panier_montant_total_ttc
  BEFORE INSERT ON sae3_skadjam._panier
  FOR EACH ROW
  EXECUTE FUNCTION calcul_panier_montant_total_ttc();
  

CREATE OR REPLACE FUNCTION calcul_produit_prix_ttc()
RETURNS TRIGGER AS $$
DECLARE 
  prix_ttc REAL := 0;
BEGIN
  prix_ttc := prix_ht*(1+pourcentage_tva)
  FROM sae3_skadjam._produit
    INNER JOIN sae3_skadjam._tva
      ON sae3_skadjam._tva.id_tva = sae3_skadjam._produit.id_tva;
  RETURN prix_ttc;
END;
$$ language plpgsql;

CREATE TRIGGER trig_produit_montant_ttc
  AFTER INSERT ON sae3_skadjam._panier
  FOR EACH ROW
  EXECUTE FUNCTION calcul_produit_prix_ttc();
  

CREATE OR REPLACE FUNCTION calcul_produit_prix_remise()
RETURNS TRIGGER AS $$
DECLARE 
  prix_remise REAL := 0;
BEGIN
  prix_remise := prix_ttc *(1-sae3_skadjam._remise.pourcentage_remise)
  FROM sae3_skadjam._produit 
    INNER JOIN sae3_skadjam._reduit 
      ON sae3_skadjam._reduit.id_produit = sae3_skadjam._produit.id_produit
     INNER JOIN sae3_skadjam._remise
      ON  sae3_skadjam._reduit.id_remise = sae3_skadjam._remise.id_remise;
  RETURN prix_remise;
END;
$$ language plpgsql;

CREATE TRIGGER trig_produit_montant_ttc
  AFTER INSERT ON sae3_skadjam._produit
  FOR EACH ROW
  EXECUTE FUNCTION calcul_produit_prix_remise();
 
