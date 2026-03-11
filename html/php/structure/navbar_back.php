<nav class="sticky top-0 z-10">
    <div class="bg-vertMoyen flex justify-around items-center p-2 text-bleu">

        <!-- Accueil et petit message -->
        <div class="flex flex-row items-center space-x-4 w-1/5">
            <!-- Accueil -->
            <a href="/index.php" class="flex self-start">
                <button class="size-12 bg-no-repeat bg-size-[48px] bg-[url(/images/logo/bootstrap_icon/house.svg)] hover:bg-[url(/images/logo/bootstrap_icon/house-fill.svg)]"></button>
            </a>
            <p>Bonjour <?php echo $raisonSociale ;?> !</p>
        </div>

        <!-- Le reste des boutons -->
         <div class="flex flex-row justify-end space-x- w-4/5">
            <!-- Recherche -->
            <a href="/html/bo/recherche.php">
                <button class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/search.svg)] hover:bg-[url(/images/logo/bootstrap_icon/search-fill.svg)]"></button>
            </a>

            <!-- Créer produit -->
            <a href="/html/bo/creation_produit.php" class="">
                <p>Ajouter un produit</p>
                <button class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/cart.svg)] hover:bg-[url(/images/logo/bootstrap_icon/plus-square-fill.svg)]"></button>
            </a>

            <!-- Autres -->
            <button id="btnAutre" class="size-12 bg-no-repeat bg-size-[auto_48px] bg-[url(/images/logo/bootstrap_icon/list.svg)]"></button>
        </div>

    </div>
</nav>