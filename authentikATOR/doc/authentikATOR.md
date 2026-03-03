# AuthentikATOR

## Déroulement initialisation auth TOTP

1. se rendre dans la secction sécurité et mdp du compte
2. selectionner A2F TOTP
3. ouverture de la page des explications TOTP fonctionnement
4. generation de la clé secrete et du QR code
5. verification du fonctionnement avec validation du code
6. validation explicite de l'utilisateur pour Auth TOTP
7. finalisation et mise en place du process

### Schéma init

```
    créer cle --> créer QRcode --> verifier clé généré --> valider --> encoder --> enregistrer --> activer
```

## Déroulement auth TOTP

1. page de connexion classique id/mdp
2. si valide arrive sur page connexion TOTP
3. générer clé 
4. verifier clé
3. si valide connexion au compte

### Schéma connexion
```
    identifier id/password --> générer clé --> verifier clé --> connexion si valide
```
## recap dev ordre
1. creation clé secrete
2. creation qr code
3. verification code généré
4. encodage de la clé
5. enregistrement en base
6. activation TOTP

## Outils

-librairie PHP/OTP : [https://github.com/Spomky-Labs/otphp/releases/tag/11.4.2](https://github.com/Spomky-Labs/otphp/releases/tag/11.4.2)

-bibliotheque js : AJAX