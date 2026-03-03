<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pass A2F</title>
    <?php include __DIR__.'/../../php/structure/head_front.php' ?>
</head>
<body>
    <?php include __DIR__.'/../../php/structure/header_front.php' ?>
    
    <main class=" w-3/4">
        <h2>Authentification à deux facteurs</h2>

        <p>
            Pour sécuriser au mieux votre compte vous pouvez ici activer l'Authentification a deux facteurs.
            Vous aurez besoin de stocker une clé secret dans une application mobile d'Authentification comme Google Authenticator, Proton Authenticator ou d'autre qui génèrerons grace a cette clé un code à 6 chiffres.
        </p>

        Etape pour activer l'authentifiacation a deux facteurs
        <ol class=" list-decimal!">
            <li>Télécharger une des application d'authentifiacation</li>
            <li>Générer la clé secrete en appuyant sur générer</li>
            <li>Copier la clé en appuyant sur copier ou scanner le QR code pour enregistrer la clé</li>
            <li>Tester si l'authentifiacation fonctionne correctement en copiant le code à 6 chiffres généré</li>
            <li>Valider l'activation de l'Authentification a deux facteurs</li>
        </ol>
        
    </main>
    
    <?php include __DIR__.'/../../php/structure/footer_front.php' ?>
</body>
</html>