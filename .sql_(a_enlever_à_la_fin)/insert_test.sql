SET SCHEMA 'sae3_skadjam';

INSERT INTO _categorie (libelle_categorie) VALUES ('Alimentaire');

INSERT INTO _tva (nom_tva, pourcentage_tva) VALUES ('Plus petite', 0.05);

INSERT INTO _compte (nom_compte, prenom_compte, adresse_mail, motDePasse, numero_telephone, bloque)
VALUES
    ('blbl', 'Blbl', 'adresse_mail@compte.1', 'mot_de_passe_1', '+33123456789', false),
    ('blbl', 'Senior', 'adresse_mail@compte.2', 'mot_de_passe_2', '+33123456789', false),
    ('blbl', 'Junior', 'adresse_mail@compte.3', 'mot_de_passe_3', '+33123456789', false);

INSERT INTO _vendeur (id_compte,raison_sociale, siren, iban, denomination)
VALUES
    (1, 'raison sociale 1', 000000001, 'FR14 2001 0101 1505 0001 3M02 606', 'deno1'),
    (2, 'raison sociale 2', 000000002, 'FR24 2002 0202 2505 0002 3M02 606', 'deno2'),
    (3, 'raison sociale 3', 000000003, 'FR34 2003 0303 3505 0003 3M02 606', 'deno3');
