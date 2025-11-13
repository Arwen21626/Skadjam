<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection client</title>

    <link rel="stylesheet" type="text/css" href="../../css/fo/general_front.css">
    
    <?php //phpinfo();?>
    <style>

    main section{
        display: flex;
        flex-direction: column;
        align-items: center;
        
    }

    main section article{
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    div a img{
        float: right;
    }

    #mdp{
        display: flex;
        flex-direction: column;
        align-items: end;
        margin-bottom: 30px;
        
        width: auto;
    }

    input{
        border-radius: 15px;
        border:  5px solid #86D8CC;
        box-shadow: 4px;
        height: 80px;
        width: 600px;
        margin-bottom: 50px;
    }

    button[type="submit"], button[type="reset"]{
        border-radius: 15px;
        width: 400px;
        height: 90px;
        border: 5px solid #86D8CC;
        background: rgba(0,0,0,0);
        font-size: 30px;

    }

    .souligne{
        text-decoration: underline;
    }

    button:hover{
        cursor: pointer;
    }   

        
    </style>

</head>
<body>
    <?php 
    include ("../../php/header_front.php"); 
    require_once("../../php/verification_formulaire.php");
    ?>

    <?php
    
    if(isset($_POST['mail']) && isset($_POST['mdp'])){
        try{
            echo "<pre>";
            echo 'mot de passe : '.$_POST['mdp']."\n";
            echo "<pre>";

            if(verifMotDePasse($_POST['mdp'])){
                echo "<pre>";
                echo "mot de passe bon";
                echo "<pre>";
            }
            else{
                echo 'mot de passe pas bon';
            }
        }
        catch(PDOException $e){
            print "Erreur dans l'envoie des données dans la base de données.";
            die();
        }
    }
    else{

    ?>
    <main>
        <div><a href="./index.php"><img width="60px" height="60px" src="../../images/logo/bootstrap_icon/x-large.svg" ></a></div>

        <section>
            <h3>Connexion</h3>

            <form action="./connection_client.php" method="post">

                <label for="mail">Adresse mail : </label><br>
                <input type="text" name="mail" id="name" required>

                <br>

                <label for="mdp">Mot de passe : </label><br>
                <input type="text" name="mdp" id="name" required>

                <br>

                <a class="souligne" id="mdp" href="./reinitialiser_mdp.php">Mot de passe oublié ?</a>

                <article>
                    <button type="submit">Se connecter</button>
                </article>
                
            </form>

            <p>Pas encore client ? <a class="souligne" href="./creation_compte_client.php">Créer un compte</a></p>
        </section>
    </main>

    <?php
    }
    include ("../../php/footer_front.php");
    ?>
</body>
</html>