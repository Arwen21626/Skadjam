<nav class="sticky top-0 z-10 w-full flex flex-col items-end">
    <div class="bg-vertMoyen w-full flex justify-around items-center p-2 text-bleu">

        <!-- Accueil et petit message -->
        <div class="flex flex-row items-center space-x-4 w-1/5">
            <!-- Accueil -->
            <a href="/index.php" class="flex self-start">
                <button class="size-12 bg-no-repeat bg-size-[48px] bg-[url(/images/logo/bootstrap_icon/house.svg)] hover:bg-[url(/images/logo/bootstrap_icon/house-fill.svg)]"></button>
            </a>
            <p>Bonjour <?php echo $raisonSociale ;?> !</p>
        </div>

        <!-- Le reste des boutons -->
         <div class="flex flex-row items-center justify-end space-x-4 w-4/5">
            <!-- Recherche -->
            <a href="/html/bo/recherche.php">
                <button class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/search.svg)] hover:bg-[url(/images/logo/bootstrap_icon/search-selected.svg)]"></button>
            </a>

            <!-- Créer produit -->
            <a href="/html/bo/creation_produit.php" class="flex flex-row items-center space-x-2">
                
                <button class="flex flex-row items-center border-2 border-black rounded-2xl m-2 p-2">
                    Ajouter un produit
                    <img src="/images/logo/bootstrap_icon/plus.svg" alt="Ajouter un produit" class="size-12">
                </button>
            </a>

            <!-- Autres -->
            <button id="btnAutre" class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/list.svg)]"></button>
        </div>
    </div>
    <div id="menuBurger" class="bg-vertMoyen hidden absolute top-24 flex flex-col items-center m-2 p-2  mt-0 w-60 rounded-b-2xl rounded-t-none">
        <ul class="space-y-3 md:space-y-3">
                <li><a href="/html/bo/profil_vendeur.php">Profil</a></li>
                <hr>
                <li><a href="/html/bo/commande.php">Commandes</a></li>
                <hr>
                <li><a href="/html/bo/statistiques.php">Statistiques</a></li>
                <hr>
                <li><a href="/php/deconnexion.php">Se déconnecter</a></li>
        </ul>
    </div>
</nav>

<script src="/js/menuBurger.js"></script>