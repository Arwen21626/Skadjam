let cache = true;
document.querySelectorAll(".bouton-afficher-mdp").forEach(bouton => {
    bouton.addEventListener( 'click', () => {
        const container = bouton.closest(".zone-mdp");
        const imageHover = container.querySelector(".hover");
        const imageNoHover = container.querySelector(".no-hover");
        const inputMdp = container.querySelector(".champ-mdp");

        if(cache){
            inputMdp.type = "text";
            
        }else{
            inputMdp.type = "password";
        }
        cache = !cache;
    })
})