<script>
    // Eventlisteners sur les cookies de session
    let tabFA = <?php echo json_encode($tabFA); ?>;
    let tabPanier = <?php echo json_encode($tabPanier); ?>;

    // Demande au navigateur s'il supporte l'api
    if ('cookieStore' in window) {
        console.log("CookieStore supporté");
    } else {
        console.log("CookieStore non supporté");
    }

    cookieStore.addEventListener("change", async (event) => {

        let cookieFA = await cookieStore.get("tabFA");
        let cookieP = await cookieStore.get("tabPanier");

        if (cookieFA) {
            tabFA = JSON.parse(cookie.value);
        }

        if (cookieP) {
            tabPanier = JSON.parse(cookie.value);
        }
    });
    
</script>