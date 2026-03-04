WbImport  -file=../csv/tva.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._tva
          -header=true
          -fileColumns=$wb_skip$,nom_tva,pourcentage_tva
          ;
          
WbImport  -file=../csv/categories.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._categorie
          -header=true
          -fileColumns=$wb_skip$,libelle_categorie
          ;

WbImport  -file=../csv/compte.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._compte
          -header=true
          -fileColumns=$wb_skip$,nom_compte,prenom_compte, adresse_mail, mot_de_passe,numero_telephone, bloque
          ;
          
WbImport  -file=../csv/vendeur.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._vendeur
          -header=true
          -fileColumns=id_compte,raison_sociale,siren,$wb_skip$,iban,denomination
          ;
          
WbImport  -file=../csv/client.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._client
          -header=true
          -fileColumns=id_compte,pseudo,date_naissance,$wb_skip$
          ;
          
WbImport  -file=../csv/adresse.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._adresse
          -header=true
          -fileColumns=$wb_skip$,adresse_postale,$wb_skip$,numero_rue,$wb_skip$,$wb_skip$,$wb_skip$,code_postal,ville,latitude,longitude
          ;
          
WbImport  -file=../csv/habite.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._habite
          -header=true
          -fileColumns=id_adresse,id_compte
          ;
                  
WbImport  -file=../csv/produits.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._produit
          -header=true
          -fileColumns=$wb_skip$,id_vendeur,id_categorie,libelle_produit,description_produit,id_tva,prix_ht,prix_ttc,$wb_skip$,$wb_skip$,est_masque, $wb_skip$, quantite_stock,$wb_skip$,quantite_unite,unite
          ;
          
WbImport  -file=../csv/photos_produits.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._photo
          -header=true
          -fileColumns=$wb_skip$,url_photo,$wb_skip$,alt, titre
          ;
          
WbImport  -file=../csv/montre.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._montre
          -header=true
          -fileColumns=id_photo,id_produit
          ;
        
WbImport  -file=../csv/avis.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._avis
          -header=true
          -fileColumns=$wb_skip$,nb_etoile,$wb_skip$,$wb_skip$,contenu_commentaire,id_produit,id_compte
          ;
          
WbImport  -file=../csv/promotion.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._promotion
          -header=true
          -fileColumns=$wb_skip$, label, date_debut_promotion, $wb_skip$, $wb_skip$, heure_debut, $wb_skip$, id_vendeur, $wb_skip$,
;
WbImport  -file=../csv/promu.csv
          -schema=sae3_skadjam
          -delimiter=';'
          -table=sae3_skadjam._promu
          -header=true
          -fileColumns=id_promotion,id_produit
          ;
