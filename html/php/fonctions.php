<?php 
function affichageNote($note){
    // fonction qui affiche une note avec des étoiles
    //affichage d'une note nulle
    ?><section class="flex flex-nowrap items-center"><?php
    if ($note == null){ ?>
        <p><?php echo htmlentities('non noté'); ?></p>
    <?php } 

    else {
        $entierPrec = intval($note);
        $entierSuiv = $entierPrec+1;
        $moitie = $entierPrec+0.5;
        $noteFinale;
        $nbEtoilesVides;

        //note arrondie à l'entier précédent
        if($note < $entierPrec+0.3){
            $noteFinale = $entierPrec;
        }

        //note arrondie à 0.5
        else if(($note < $moitie) || ($note < $entierPrec+0.8)){
            $noteFinale = $moitie;
            $nbEtoilesVides = 5-$entierPrec-1;
            //affichage d'une note et demie
            //boucle pour étoiles pleines
            for($i=0; $i<$entierPrec; $i++){?>
                <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
            <?php } ?>
            <!--demie étoile-->
            <img src="../../images/logo/bootstrap_icon/star-half.svg" alt="demie étoile">
            <!--boucle pour étoiles vides-->
            <?php for($i=0; $i<$nbEtoilesVides; $i++){?>
                <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
            <?php }
        }
        
        //note arrondie à l'entier suivant
        else{
            $noteFinale = $entierSuiv;
        }

        //affichage d'une note entière :
        if($noteFinale != $moitie){
            $nbEtoilesVides = 5-$noteFinale;
            //boucle pour étoiles pleines
            for($i=0; $i<$noteFinale; $i++){?>
                <img src="../../images/logo/bootstrap_icon/star-fill.svg" alt="étoile pleine">
            <?php }
            //boucle pour étoiles vides
            for($i=0; $i<$nbEtoilesVides; $i++){?>
                <img src="../../images/logo/bootstrap_icon/star.svg" alt="étoile vide">
            <?php }
        }
    } 
    ?></section><?php
}