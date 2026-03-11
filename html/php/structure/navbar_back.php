<nav class="sticky top-0 z-10 w-full flex flex-col items-end">
    <div class="bg-vertMoyen w-full flex justify-around items-center p-2 text-bleu">

        <!-- Accueil et petit message -->
        <div class="flex flex-row items-center space-x-4 w-1/5">
            <!-- Accueil -->
            <a href="/html/bo/index_vendeur.php" class="flex self-start">
                <button class="size-12 cursor-pointer bg-no-repeat bg-size-[48px] bg-[url(/images/logo/bootstrap_icon/house.svg)] hover:bg-[url(/images/logo/bootstrap_icon/house-fill.svg)]"></button>
            </a>
            <p>Bonjour <?php echo $_SESSION['raisonSociale'] ;?> !</p>
        </div>

        <!-- Le reste des boutons -->
         <div class="flex flex-row items-center justify-end space-x-4 w-4/5">
            <!-- Recherche -->
            <a href="/html/bo/recherche.php">
                <button class="size-12 cursor-pointer bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/search.svg)] hover:bg-[url(/images/logo/bootstrap_icon/search-selected.svg)]"></button>
            </a>

            <!-- Créer produit -->
            <a href="/html/bo/creation_produit.php" class="flex flex-row items-center space-x-2">
                <button class="flex flex-row justify-around items-center border-2 border-black rounded-2xl m-2 p-1 cursor-pointer">
                    <p class="h-5 flex items-center">Ajouter un produit</p>
                    <img src="/images/logo/bootstrap_icon/plus.svg" alt="Ajouter un produit" class="size-10 self-center">
                </button>
            </a>

            <!-- Autres -->
            <button id="btnAutre" class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/list.svg)] cursor-pointer"></button>
        </div>
    </div>
    <div id="menuBurger" class="bg-vertMoyen hidden absolute top-21 flex flex-col items-center m-2 p-2  mt-0 w-60 rounded-b-2xl rounded-t-none border-2 border-t-0 border-bleu">
        <ul class="space-y-3 md:space-y-3">
                <li class="text-bleu"><a href="/html/bo/profil_vendeur.php">Profil</a></li>
                <hr>
                <li class="text-bleu"><a href="/html/bo/liste_commandes.php">Commandes</a></li>
                <hr>
                <li class="text-bleu"><a href="/html/bo/statistiques.php">Statistiques</a></li>
                <hr>
                <li class="text-bleu"><a href="/php/deconnexion.php">Se déconnecter</a></li>
        </ul>
    </div>
</nav>

<script src="/js/menuBurger.js"></script>