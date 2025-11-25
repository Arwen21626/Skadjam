<?php 
    session_start();
    include(__DIR__ .'/../01_premiere_connexion.php');
    $idCompte = $_SESSION['idCompte'];


    try{
        $id = $_POST['id'];
        $stockActuel = $_POST['stockActuel'];

        if($stockActuel > 0){
            $stmt = $dbh->prepare("
                UPDATE sae3_skadjam._produit 
                SET quantite_stock = quantite_stock - 1 
                WHERE id_produit = $id
            ");

            $stmt->execute();
        } 
        
         header("Location: ../html/bo/stock.php");
        exit;
       
    }

    catch (PDOException $e){
        print "Erreur !: " . $e->getMessage() . "<br/>";
            die();
    }

    
?>