let cache = true;
document.querySelectorAll(".bouton-afficher-mdp").forEach(bouton => {
    bouton.addEventListener( 'click', () => {
        const container = bouton.closest(".zone-mdp");
        const imageHover = container.querySelector(".hover");
        const imageNoHover = container.querySelector(".no-hover");
        const inputMdp = container.querySelector(".champ-mdp");

        if(cache){
            inputMdp.type = "text";
            imageNoHover.src = "/../../../images/logo/bootstrap_icon/eye-slash.svg"
            imageHover.src = "/../../../images/logo/bootstrap_icon/eye-slash-fill.svg"
            
        }else{
            inputMdp.type = "password";
            imageNoHover.src = "/../../../images/logo/bootstrap_icon/eye.svg"
            imageHover.src = "/../../../images/logo/bootstrap_icon/eye-fill.svg"
        }
        cache = !cache;
        
    })
})