# Documentation des cas d'utilisation - Serveur Delivraptor

## Connexion au service et authentification : CONN

### Utilisation

**_Côté client_**

Requête de connexion envoyée au serveur:
COMMANDE : ``` CONN <username> <password> ```

**_Réponse du serveur_**

* Succès : ``` CONNECTION SUCCESS ```
* Erreur identification : ``` CONNECTION DENIED ```
* Erreur interne : ``` ERR SERVER ```

### Précondition

Le client doit être connecté au socket avec l'utilisation de :
_fonction php_
``` fsockopen(<ip_serveur>, <port_serveur>)```

### Scénario nominal

- (1) Le client demande au serveur de s'authentifier : `CONN`
    - (2) Le serveur vérifie la commande reçue
    - (3) Si la syntaxe de la commande est correcte
        - Alors le serveur poursuit le traitement
        - Sinon il retourne une erreur
    - (4) Le serveur vérifie les identifiants et mots de passe
    - (5) Si les identifiants correspondent à un utilisateur autorisé
        - Alors le client est connecté, le serveur envoie un feedback
        - Sinon le serveur refuse la connexion
- (6) Fin (A)

### Postcondition
Fin (A) : Le client est connecté

## Demande de création de bordereau  : ADD

**_Côté client_**

Requête de connexion envoyée au serveur:
COMMANDE : ``` ADD <nom_expediteur> <id_commande> ```

**_Réponse du serveur_**

``` 
CMD BORD
SIZE <n>
<id_suivi>
END 
```
_Exemple_

``` 
CMD BORD
SIZE <12>
ALI17256345
END 
```

### Précondition

Le client doit être connecté au socket et identifié :
_fonction php_
``` fsockopen(<ip_serveur>, <port_serveur>)```
_requête au serveur_
``` CONN <username> <password> ```

### Scénario nominal

- (1) Le client envoie une demande de bordereau : ``` ADD ```
    - (2) Le serveur vérifie la commande reçue
    - (3) Si la syntaxe de la commande est correcte
        - Alors le serveur poursuit le traitement
        - Sinon le programme s'arrête
    - (4) Le serveur vérifie en BDD si le numéro de commande existe
    - (5) Le serveur crée un numéro de suivi avec les informations reçues
    - (6) Les informations sont enregistrées dans la base de données du serveur
    - (7) Le serveur envoie le numéro de suivi au client
- (8) Fin (A)

### Scénario "commande déjà en BDD"

- (5) Le serveur récupère le numéro de suivi en BDD
- (6) Le serveur envoie le numéro de suivi au client
- (7) Fin (A)

### Postcondition
Fin (A) : Le client peut recevoir son numéro de suivi

## Demander l'état d'une commande : ETA

**_Côté client_**

Requête de connexion envoyée au serveur:
COMMANDE : ``` ETA <id_suivi> ```

**_Réponse du serveur_**

- Cas normal
``` 
CMD ETA
SIZE <n>
<etat> <id_suivi>
END 
```

- Cas livraison refusée
``` 
CMD ETA
SIZE <n>
REFU <id_suivi> msg:<raison>
END 
```

- Cas livraison absent
``` 
CMD ETA
SIZE <n>
LVRAB <id_suivi>
END 
```
_Exemple_

``` 
CMD ETA
SIZE <n>
REFU ALI17256345 msg:Le colis est trop abîmé
END 
```

### Précondition

Le client doit être connecté au socket et identifié :
_fonction php_
``` fsockopen(<ip_serveur>, <port_serveur>)```
_requête au serveur_
``` CONN <username> <password> ```

### Scénario nominal

- (1) Le client envoie une demande d'état : ``` ETA ```
    - (2) Le serveur vérifie la commande reçue
    - (3) Si la syntaxe de la commande est correcte
        - Alors le serveur poursuit le traitement
        - Sinon le programme s'arrête
    - (4) Le serveur récupère l'état de la commande en BDD
    - (5) Le serveur vérifie l'état de la commande
    - (6) L'état de la commande est envoyé au client
- (7) Fin (A)

### Scénario "état REFU"

- (6) Le serveur récupère la raison du refus en BDD
- (7) L'état et la raison sont envoyés au client
- (8) Fin (B)

### Postcondition
Fin (A) : Le client reçoit l'état de la commande
Fin (B) : Le client reçoit l'état de la commande et la raison du refus

## Demander l'image associée à une commande : IMG

**_Côté client_**

Requête de connexion envoyée au serveur:
COMMANDE : ``` IMG <id_suivi> ```

**_Réponse du serveur_**

``` 
CMD IMG
SIZE <n>
<image_binaires>
END 
```

### Précondition

Le client doit être connecté au socket et identifié :
_fonction php_
``` fsockopen(<ip_serveur>, <port_serveur>)```
_requête au serveur_
``` CONN <username> <password> ```

### Scénario nominal

- (1) Le client envoie une demande d'image : ``` IMG ```
    - (2) Le serveur vérifie la commande reçue
    - (3) Si la syntaxe de la commande est correcte
        - Alors le serveur poursuit le traitement
        - Sinon le programme s'arrête
    - (4) Le serveur récupère le chemin de l'image en BDD
    - (5) L'image est ouverte et lue en binaire
    - (6) Le fichier binaire est envoyé au client
- (7) Fin (A)

### Postcondition
Fin (A) : Le client reçoit l'image en binaire

## Faire avancer les commandes dans le processus de livraison : NEXT

**_Côté client_**

Requête de connexion envoyée au serveur:
COMMANDE : ``` NEXT ```

**_Réponse du serveur_**

> Aucune réponse

### Précondition

Le client doit être connecté au socket et identifié :
_fonction php_
``` fsockopen(<ip_serveur>, <port_serveur>)```
_requête au serveur_
``` CONN <username> <password> ```

### Scénario nominal

- (1) Le client envoie une demande : ``` NEXT ```
    - (2) Le serveur vérifie la commande reçue
    - (3) Si la syntaxe de la commande est correcte
        - Alors le serveur poursuit le traitement
        - Sinon le programme s'arrête
    - (4) Le serveur récupère les commandes par état de livraison 
    ``` proche livraison -->> loin livraison ```
    - (5) L'état de la commande passe à l'état qui suit si c'est possible
    - (6) L'état est re-stocké en BDD
- (7) Fin (A)

### Scénario "choix livraison"

- (5) L'état de livraison est choisi aléatoirement
- (6) Le nouvel état est stocké en BDD
- (7) FIN (A)

### Scénario "nouvel état REFU"
- (6) Le serveur choisit aléatoirement une raison de refus
- (7) Le nouvel état et la raison sont stockés en BDD
- (8) Fin (B)

### Scénario "nouvel état LVRAB"
- (6) Le serveur récupère le chemin de la photo de livraison
- (7) Le nouvel état et le chemin de l'image sont stockés en BDD
- (8) Fin (C)

### Postcondition
Fin (A) : Les livraisons ont reçu leur nouvel état  
Fin (B) : Les livraisons refusées ont reçu une raison de refus  
Fin (C) : Les livraisons effectuées en l'absence du destinataire sont associées à une image
