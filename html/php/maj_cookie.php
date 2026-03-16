<script>
    // Eventlisteners sur les cookies de session
    let tabFA = <?php echo json_encode($tabFA); ?>;

    // Demande au navigateur s'il supporte l'api
    if ('cookieStore' in window) {
        console.log("CookieStore supporté");
    } else {
        console.log("CookieStore non supporté");
    }

    cookieStore.addEventListener("change", async (event) => {

        const cookieFA = await cookieStore.get("tabFA");

        if (cookieFA) {
            tabFA = JSON.parse(cookie.value);
        }
    });
    
</script>