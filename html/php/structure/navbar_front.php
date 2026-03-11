<?php 
    if (!isset($_SESSION['role'])) {
        $_SESSION['role'] = "visiteur";
    }

    $urlProfil;

    if ($_SESSION["role"] === "visiteur") 
    {
        $urlProfil = "/" . "html/fo/connexion.php";
    }
    else if ($_SESSION["role"] === "client") 
    {
        $urlProfil = "/" . "html/fo/profil_client.php";
    }
    else if ($_SESSION["role"] === "vendeur")
    {
        $urlProfil = "/" . "html/bo/index_vendeur.php";
    }
?>

<nav class="z-10 fixed left-0 right-0 bottom-0 w-full md:sticky md:top-0 flex flex-col items-end">

    <div class="z-9 flex justify-around md:justify-between md:space-x-4 items-center p-1 bg-beige w-full order-2 md:order-1">
        <!-- Accueil et petit message -->
        <div class="flex flex-row justify-around md:justify-start md:items-center md:space-x-4 w-1/5">
            <!-- Accueil -->
            <a href="/" class="md:flex md:self-start">
                <button class="size-12 bg-no-repeat bg-size-[48px] bg-[url(/images/logo/bootstrap_icon/house.svg)]"></button>
            </a>
            <?php if ($_SESSION["role"] === "client"){?>
                <p class="hidden md:block">Bonjour <?php echo $_SESSION['pseudo'] ;?> !</p>
            <?php } ?>
        </div>
        
        <!-- Le reste des boutons -->
        <div class="flex flex-row justify-around md:justify-end md:space-x-6 w-4/5">
            <!-- Recherche -->
            <a href="/html/fo/recherche.php">
                <button class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/search.svg)]"></button>
            </a>

            <!-- Panier -->
            <a href="/html/fo/panier.php">
                <button class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/cart.svg)]"></button>
            </a>

            <!-- Autres -->
            <button id="btnAutre" class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/list.svg)]"></button>
        </div>
        
    </div>
    <!-- Affichage du menu burger en fonction du role du user -->
    <?php if ($_SESSION['role'] == "client") { ?>
    <div id="menuBurger" class="bg-beige hidden md:absolute md:top-14 flex flex-col items-center m-2 p-2 mb-0 w-40 rounded-t-2xl md:mt-0 md:w-60 md:rounded-b-2xl md:rounded-t-none order-1 md:order-2 border-2 border-b-0 md:border-t-0 md:border-b-2 border-black">
        <ul class="space-y-3 md:space-y-3">
                <li><a href=<?php echo $urlProfil ?>>Profil</a></li>
                <hr>
                <li><a href="/html/fo/futurs_achats.php">Futurs achats</a></li>
                <hr>
                <li><a href="/html/fo/liste_commandes.php">Commandes</a></li>
                <hr>
                <li><a href="/php/deconnexion.php">Se déconnecter</a></li>
        </ul>
    </div>
    <?php }
    else{ ?>
    <div id="menuBurger" class="bg-beige hidden md:absolute md:top-14 flex flex-col items-center m-2 p-2 mb-0 w-40  rounded-t-2xl md:mt-0 md:w-60 md:rounded-b-2xl md:rounded-t-none order-1 md:order-2 border-2 border-b-0 md:border-t-0 md:border-b-2 border-black">
        <ul class="space-y-3 md:space-y-2">
            <li><a href="/html/fo/futurs_achats.php">Futurs achats</a></li>
            <hr>
            <li><a href="/html/fo/connexion.php">Se connecter</a></li>
        </ul>
    </div>
    <?php } ?>
</nav>

<script src="/js/menuBurger.js"></script>