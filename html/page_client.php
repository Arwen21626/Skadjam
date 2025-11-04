<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon compte</title>
</head>
<body>
    
    <h2>Mon compte</h2>
    <article>
        <section>
            <h3><?php echo $nom ?></h3>
            <h3><?php echo $prenom ?></h3>
        </section>
        <h3><?php echo $telephone ?></h3>
        <h3><?php echo $mail ?></h3>
    </article>
    <button>Modifier</button>
    <button>Se déconnecter</button>
</body>
</html>