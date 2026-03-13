<script>
    // Eventlisteners sur les cookies de session
    let tabFA = <?php echo json_encode($tabFA); ?>;
    console.log(tabFA)

    // Demande au navigateur s'il supporte l'api
    if ('cookieStore' in window) {
        console.log("CookieStore supporté");
    } else {
        console.log("CookieStore non supporté");
    }

    cookieStore.addEventListener("change", async (event) => {

    const cookie = await cookieStore.get("tabFA");

    if (cookie) {
        tabFA = JSON.parse(cookie.value);
        console.log("tabFA mis à jour :", tabFA);
    }

});
    
</script>