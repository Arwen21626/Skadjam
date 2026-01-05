<?php 
    session_start();
    require_once __DIR__ . "/../../php/verif_role_fo.php";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <?php require __DIR__ . "/../../php/structure/head_front.php"; ?>
    <title>Conditions Générales de Vente</title>
</head>
<body>
    <?php
        require __DIR__ . "/../../php/structure/header_front.php";
        require __DIR__ . "/../../php/structure/navbar_front.php";
    ?>
    <main class="m-10 mt-2 flex flex-col">
        <h2>Conditions générales de vente</h2>
        <h3 class="self-center mt-0 mb-2">En vigueur au 07/11/2025</h3>
        <p>Les présentes conditions de vente sont conclues d’une part par la coopérative COBREC au capital social de 150000€ dont le siège social est situé à Lannion, immatriculée au RCS de Lannion sous le numéro 508 977 303. Ci-après dénommée COBREC et gérant le site Alizon.bzh et, d’autre part, par toute personne physique ou morale souhaitant procéder à un achat via le site internet Alizon.bzh dénommée ci-après « l’Acheteur ».</p>
        <article>
            <h3 class="ml-5">Article 1 : Commande et modalités de paiement</h3>
            <p>Avant toute commande, l’Acheteur doit créer un compte sur le site Alizon.bzh. COBREC La création d’un compte client est nécessaire pour commander. L’Acheteur sélectionne ses produits, confirme son panier, renseigne ses informations, choisit un mode de livraison, accepte les CGV puis valide sa commande. Ce dernier clic forme la conclusion définitive du contrat. Dès validation, l’Acheteur reçoit un bon de commande confirmant l’enregistrement de sa commande. Dès réception du virement, la commande sera traitée et l’Acheteur en sera informé par e-mail. COBREC expédiera les produits au plus tôt deux jours ouvrés après réception du virement correspondant à la commande, sous réserve de provisions.</p>
            <br>
            <p>L’ensemble des données fournies et la confirmation enregistrée vaudront preuve de la transaction.</p>
            <br>
            <p>Si l’Acheteur souhaite contacter COBREC, il peut le faire soit par courrier à l’adresse suivante : 7 Rue Édouard Branly, 22300 Lannion ; soit par email à l’adresse suivante : cobrec@mail.bzh, soit par téléphone au 02 96 46 93 00.</p>
        </article>
        <article>
            <h3 class="ml-5">Article 2 : Rétractation</h3>
            <p>En vertu de l’article L121-20 du Code de la consommation, l’Acheteur dispose d'un délai de quatorze jours ouvrables à compter de la livraison de leur commande pour exercer son droit de rétractation et ainsi faire retour du produit au vendeur pour échange ou remboursement sans pénalité, à l’exception des frais de retour.</p>
        </article>
        <article>
            <h3 class="ml-5">Article 3 : Livraison</h3>
            <p>Les produits sont envoyés via La Poste avec suivi. Les délais sont indicatifs ; en cas de dépassement supérieur à 30 jours, l’Acheteur peut annuler la commande et être remboursé. Les risques sont transférés à l’Acheteur dès la remise au transporteur. À réception, l’Acheteur doit vérifier l’état du colis et signaler tout dommage au transporteur sous trois jours.</p>
        </article>
        <article>
            <h3 class="ml-5">Article 4 : Garantie</h3>
            <p>Tous les produits fournis par COBREC bénéficient de la garantie légale prévue par les articles 1641 et suivants du Code civil. En cas de non-conformité d’un produit vendu, il pourra être retourné et échangé ou remboursé. Toutes les réclamations, demandes d’échange ou de remboursement doivent s’effectuer par voie postale à l’adresse suivante : 7 Rue Édouard Branly, 22300 Lannion, dans un délai de trente jours après livraison.</p>
        </article>
    </main>
    <?php require __DIR__ . "/../../php/structure/footer_front.php"; ?>
</body>
</html>
