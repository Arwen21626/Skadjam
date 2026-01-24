# Protocole de communication client / serveur

## Vue d’ensemble
Le serveur **Delivraptor** communique avec ses clients via un protocole texte applicatif, au-dessus d’une connexion TCP.
Chaque message est structuré de manière déterministe afin de permettre un parsing simple et robuste côté client et serveur.

---

## Transport
- **Protocole réseau** : TCP
- **Encodage** : texte (ASCII / UTF-8)
- **Mode** : synchrone, orienté requête / réponse

---

## Structure générale d’un message

Chaque message envoyé par le serveur (et attendu dans le même format côté client) respecte la structure suivante :

```
CMD <COMMANDE>
SIZE <TAILLE>
<MESSAGE>
END
```

### Détails des champs

| Champ | Description |
|------|-------------|
| `CMD` | Code de commande protocolaire (voir liste ci-dessous) |
| `SIZE` | Taille en octets du corps `<MESSAGE>` |
| `MESSAGE` | Données textuelles associées à la commande |
| `END` | Marqueur de fin de message |

---

## Commandes supportées

Les commandes sont échangées sous forme de chaînes ASCII. Elles sont envoyées **sur une seule ligne**, sans saut de ligne final côté client PHP.

| Commande | Paramètres | Description |
|---------|------------|-------------|
| `CONN` | `<user> <password>` | Authentification du client |
| `ADD` | `<nom_expéditeur> <num_commande>` | Création d’un bordereau, retourne un numéro de suivi |
| `ETA` | `<num_suivi>` | Retourne l’état actuel de la livraison |
| `IMG` | `<num_suivi>` | Retourne une image (preuve) si disponible |

Toute commande inconnue est considérée comme invalide (`CMD_UNKNOWN`).

---------|-------------|
| `ADD` | Ajout d’une commande / livraison |
| `ETA` | Demande ou réponse d’estimation de livraison |
| `NEXT` | Récupération de la prochaine livraison |
| `BORD` | Transmission d’un bordereau |
| `CONN` | Connexion / authentification client |
| `IMG` | Transmission ou requête d’image |

Toute commande inconnue est considérée comme invalide (`CMD_UNKNOWN`).

---

## Exemple de message

### Requête client
```
ETA ABC123456
```

### Réponse serveur
```
CMD ETA
SIZE 20
LVRSN
END
```

---

## Envoi des données

- Le serveur garantit l’envoi complet des données via une fonction de type `send_all()`.
- Les envois partiels sont gérés automatiquement.
- Les interruptions système (`EINTR`) sont prises en compte.

---

## Parsing côté réception

Le client PHP (`Recupraptor`) applique strictement le parsing suivant :

1. Lecture d’une ligne `CMD <CODE>`
2. Lecture d’une ligne `SIZE <N>`
3. Lecture du corps du message jusqu’au marqueur `END`
   - Si `SIZE < 255` : lecture ligne par ligne (trimée)
   - Sinon : lecture brute (binaire possible)
4. Fermeture immédiate de la connexion TCP

⚠️ **La taille (`SIZE`) n’est pas utilisée pour borner la lecture**, seul `END` fait foi.

---

## Gestion des erreurs

- Commande inconnue → rejet du message
- Taille incohérente → rejet du message
- Absence de `END` → message invalide

---

## Détails par commande

### CONN
**Requête**
```
CONN <user> <password>
```

**Réponses possibles**
- `CONNEXION SUCCESS`
- `CONNEXION DENIED`

---

### ADD
**Requête**
```
ADD <nom_expéditeur> <num_commande>
```

**Réponse**
- Corps du message : numéro de suivi généré (texte)

---

### ETA
**Requête**
```
ETA <num_suivi>
```

**Réponse**
```
<ETAT> [msg: <raison>]
```

États connus :
- `TRTC` : En attente
- `ACHTR`, `ARRTR`, `ACHPR`, `ARRPR`, `ACHCL`, `ARRCL` : Expédié
- `LVRSN` : En cours de livraison
- `LVR` : Livré
- `LVRAB` : Livré absent (déclenche un appel IMG côté client)
- `REFU` : Refusé (avec message)

---

### IMG
**Requête**
```
IMG <num_suivi>
```

**Réponse**
- Corps du message : données binaires JPEG
- Le client écrit directement le contenu dans un fichier `.jpg`

---

## Particularités du client PHP

- Connexion TCP **ouverte et fermée à chaque commande**
- Protocole strictement synchrone
- Absence de `
` explicite à l’envoi des commandes
- Le parsing dépend exclusivement du marqueur `END`

---

## Points d’attention / dette technique

- `SIZE` n’est pas utilisé comme garde-fou réel
- Mélange texte / binaire sur le même protocole
- Pas de checksum ni validation d’intégrité
- Pas de gestion de timeout côté client

---

## Évolutions possibles

- Forcer les fins de ligne (`
`) côté client
- Utiliser `SIZE` pour borner la lecture
- Séparer IMG sur un canal dédié
- Ajouter des codes d’erreur normalisés

