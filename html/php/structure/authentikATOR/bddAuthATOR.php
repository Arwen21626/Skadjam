<?php 
class  BddAuthATOR{

    function getSecret($conn, $idClient){
        //print_r("get secret : id_client ".$idClient);
        try {
            $stmt = $conn->prepare("SELECT code_secret, adresse_mail FROM sae3_skadjam._compte WHERE id_compte = ?");
            $stmt->execute([$idClient]);
            $secret = $stmt->fetch(PDO::FETCH_ASSOC);
            //print_r("get secret : secret "+$secret);
            if ($secret === false) {
                return null;
            }
            return $secret;
        }catch (PDOException $e){
            throw $e;
        }
    }

    function addSecret($conn, $idClient, $secret){
        //print_r("add secret : id_client "+$idClient+" secret "+$secret);
        try {
            $stmt = $conn->prepare("UPDATE sae3_skadjam._compte SET code_secret = ? WHERE id_compte = ?");
            $stmt->execute([$secret,$idClient]);
            $nbRow = $stmt->rowCount();
        }catch (PDOException $e) {
            throw $e;
        }
        //print_r("add secret : id_client "+$idClient+" secret "+$secret);
    }

    function getTentative($conn, $idClient){
        try{
            $stmt = $conn->prepare("SELECT tentative FROM sae3_skadjam._compte WHERE id_compte = ?");
            $stmt->execute([$idClient]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;
        }catch (PDOException $e){
            throw $e;
        }
    }

    function addTentative($conn, $idClient){
        try{
            $stmt = $conn->prepare("UPDATE sae3_skadjam._compte SET tentative = tentatuve+1 WHERE id_compte = ?");
            $stmt->execute([$idClient]);
        }catch (PDOException $e){
            throw $e;
        }
    }
    
    function resetTentative($conn, $idClient){
        try{
            $stmt = $conn->prepare("UPDATE sae3_skadjam._compte SET tentative = 0 WHERE id_compte = ?");
            $stmt->execute([$idClient]);
        }catch (PDOException $e){
            throw $e;
        }
    }

    function getTempsRestant($conn, $idClient){
        try{
            $stmt = $conn->prepare("SELECT restant FROM sae3_skadjam._compte WHERE id_compte = ?");
            $stmt->execute([$idClient]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return $res;
        }catch (PDOException $e){
            throw $e;
        }
    }

    function addTempsRestant($conn, $idClient){
        try{
            $stmt = $conn->prepare("UPDATE sae3_skadjam._compte SET restant = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE id_compte = ?");
            $stmt->execute([$idClient]);
        }catch (PDOException $e){
            throw $e;
        }
    }
}
