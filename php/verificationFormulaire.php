<?php

function verifNomPrenom($nom){
    if (strlen($nom) > 100 || !preg_match("/^[A-Za-z -]+$/", $nom)){
        return false;
    }
    else{
        return true;
    }
}

function verifTelephone($tel){
    if (!preg_match("/^\+33[0-9]{9}$/", $tel)){
        return false;
    }
    else{
        return true;
    }
}

function verifMail($mail){
    if (strlen($mail) > 150 || !preg_match("/^[A-Za-z0-9.]+@[A-Za-z]+.[A-Za-z]+$/", $mail)){
        return false;
    }
    else{
        return true;
    }
}

function verifMotDePasse($mdp){
    if (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[-@_#$.£!?%*+:;,&~|^])[^\s<>]{10,}$/", $mdp)){
        return false;
    }
    else{
        return true;
    }
}