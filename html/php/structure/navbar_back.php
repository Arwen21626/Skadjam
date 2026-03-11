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
<nav class="sticky top-0 z-10 bg-vertMoyen flex justify-around items-center p-2 text-bleu">
    <!-- Accueil -->
    <div class="flex flex-col justify-center items-center">

        <a class="text-bleu hover:text-beige" href="/html/bo/index_vendeur.php">
            <div class="size-12 bg-no-repeat bg-size-[auto_48px]
                        bg-[url(/images/logo/bootstrap_icon/house.svg)]
                        hover:bg-[url(/images/logo/bootstrap_icon/house-fill.svg)]">
            </div>
        </a>

        <a class="text-bleu hover:text-beige" href="/html/bo/index_vendeur.php">Accueil</a>
    </div>

    <!-- Commandes -->
    <div class="flex flex-col justify-center items-center">

        <a class="text-bleu hover:text-beige" href="/html/bo/liste_commandes.php">
            <div class="size-12 bg-no-repeat bg-size-[auto_48px]
                        bg-[url(/images/logo/bootstrap_icon/truck.svg)]
                        hover:bg-[url(/images/logo/bootstrap_icon/truck-fill.svg)]">
            </div>
        </a>

        <a class="text-bleu hover:text-beige" href="/html/bo/liste_commandes.php">Commandes</a>
    </div>

    <!-- Recherche -->
    <div class="md:flex md:flex-col md:justify-center md:items-center">

        <a href="/html/bo/recherche.php">
            <div class="size-12 bg-no-repeat bg-size-[auto_48px] 
            bg-[url(/images/logo/bootstrap_icon/search.svg)] 
            hover:bg-[url(/images/logo/bootstrap_icon/search-selected.svg)]">
            </div>
        </a>

        <a class="text-bleu hover:text-rouge hidden md:inline-block" href="/html/bo/recherche.php">Recherche</a>
    </div>


    <!-- Créer produit -->
    <div class="flex flex-col justify-center items-center">
        
        <a class="text-bleu hover:text-beige" href="/html/bo/creation_produit.php">
            <div class="size-12 bg-no-repeat bg-size-[auto_48px]
                        bg-[url(/images/logo/bootstrap_icon/plus-square.svg)]
                        hover:bg-[url(/images/logo/bootstrap_icon/plus-square-fill.svg)]">
            </div>
        </a>

        <a class="text-bleu hover:text-beige" href="/html/bo/creation_produit.php">Créer un produit</a>
    </div>

    <!-- Profil -->
    <!-- A changer plus tard en menu burger avec la dernière icône -->
    <div class="flex flex-col justify-center items-center">

        <a class="text-bleu hover:text-beige" href="/html/bo/profil_vendeur.php">
            <div class="size-12 bg-no-repeat bg-size-[auto_48px]
                        bg-[url(/images/logo/bootstrap_icon/person.svg)]
                        hover:bg-[url(/images/logo/bootstrap_icon/person-fill.svg)]">
            </div>
        </a>

        <a class="text-bleu hover:text-beige" href="/html/bo/profil_vendeur.php">Profil</a>
    </div>
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