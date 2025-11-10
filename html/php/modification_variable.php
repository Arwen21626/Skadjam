<?php 
function formatPrenom($nom){
    // renvoi un prenom ou un nom avec le format : une majuscule suivie de miniscules

    $ret = trim($nom);
    $ret = strtoupper(substr($ret,0,1)) . strtolower(substr($ret,1));
    return $ret;
}

function formatTel($tel){
    // renvoi un numéro de téléphone avec le format : +33 suivie de 9 chiffres

    $tel = str_replace(" ", "", trim($tel));
    return "+33". substr($tel, 1);
}

function formatDatePourBDD($date){
    // reçois une date au format : aaaa-mm-jj
    // et renvoi une date au format : jj/mm/aaaa 

    $dateBonFormat = explode('-', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
}

function formatDatePourInput($date){
    // reçois une date au format : jj/mm/aaaa 
    // et renvoi une date au format : aaaa-mm-jj

    $dateBonFormat = explode('/', $date);
    return "$dateBonFormat[2]/$dateBonFormat[1]/$dateBonFormat[0]";
}

function numRue($adresse){
    //recois une adresse complete
    //renvoi le numéro et le complement
    if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
        $numero = trim($matches[1]); 

        return $numero;
    }

}

function formatAdresse($adresse){
    //recois une adresse complete
    //renvoi la rue
    if (preg_match('/^(\d+\s*[A-Za-z]*)[, ]*(.+)$/u', $adresse, $matches)) {
    $rue = trim($matches[2]);

    return $rue;
    }
}

function formatNum($num){
    //recois le numéro et le complement
    //renvoi le numero
    $ret = "";
    foreach ($num as $char){
        if ($char>=0){
            $ret = $ret . $char;
        }
    }
    return $ret;
}

function formatCompNum($num){
    //recois le numéro et le complement
    //renvoi le complément
    $ret = "";
    foreach ($num as $char){
        if (preg_match("/^[a-zA-Z]$/", $char)){
            $ret = $ret . $char;
        }
    }
    return $ret;
}
?>
