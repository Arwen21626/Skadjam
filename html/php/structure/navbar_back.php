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

<nav class="fixed left-0 right-0 bottom-0 w-full z-10 md:sticky md:top-0 flex flex-col items-end">

    <div class="flex justify-around md:justify-between md:space-x-4 items-center p-1 bg-beige w-full order-2 md:order-1">
        <!-- Accueil et petit message -->
        <div class="flex flex-row justify-around md:items-center md:space-x-4 w-1/5">
            <!-- Accueil -->
            <a href="/index.php" class="md:flex md:self-start">
                <button class="size-12 bg-no-repeat bg-size-[48px] bg-[url(/images/logo/bootstrap_icon/house.svg)]"></button>
            </a>

            <?php if ($_SESSION["role"] === "client"){?>
                <p class="hidden md:block">Bonjour <?php echo $pseudo ;?> !</p>
            <?php } ?>
        </div>
        
        <!-- Le reste des boutons -->
        <div class="flex flex-row justify-around md:justify-end md:space-x- w-4/5">
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
    <div id="menuBurger" class="bg-beige hidden md:absolute md:top-14 flex flex-col items-center m-2 p-2 mb-0 w-40 rounded-t-2xl md:mt-0 md:w-60 md:rounded-b-2xl md:rounded-t-none order-1 md:order-2">
        <ul class="space-y-3 md:space-y-3">
                <li><a href=<?php echo $urlProfil ?>>Profil</a></li>
                <hr>
                <li><a href="">Futurs achats</a></li>
                <hr>
                <li><a href="">Commandes</a></li>
                <hr>
                <li><a href="../deconnexion.php">Se déconnecter</a></li>
        </ul>
    </div>
    <?php }
    else{ ?>
    <div id="menuBurger" class="bg-beige hidden flex flex-col items-center m-2 p-2 mb-0 w-35 rounded-t-2xl md:mt-0 md:w-60 md:rounded-b-2xl md:rounded-t-none order-1 md:order-2">
        <ul class="space-y-3 md:space-y-2">
            <li><a href="">Futurs achats</a></li>
            <hr>
            <li><a href="../../html/fo/connexion.php">Se connecter</a></li>
        </ul>
    </div>
    <?php } ?>
</nav>

<script>
    let btnAutre = document.getElementById("btnAutre")
    let menuBurger = document.getElementById("menuBurger")

    // Action lors du click sur le profil
    btnAutre.addEventListener("click", function(){
        if (menuBurger.classList.contains("hidden")) {
            menuBurger.classList.remove("hidden")
        }
        else{
            menuBurger.classList.add("hidden")
        }
    })
</script>