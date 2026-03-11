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