let btnAutre = document.getElementById("btnAutre")
let menuBurger = document.getElementById("menuBurger")

// Action lors du click sur le profil
btnAutre.addEventListener("click", function(){
    if (menuBurger.classList.contains("hidden")) {
        menuBurger.classList.remove("hidden")
        btnAutre.classList.remove("bg-[url(/images/logo/bootstrap_icon/list.svg)]")
        btnAutre.classList.add("bg-[url(/images/logo/bootstrap_icon/three-dots-vertical.svg)]")
    }
    else{
        menuBurger.classList.add("hidden")
        btnAutre.classList.add("bg-[url(/images/logo/bootstrap_icon/list.svg)]")
        btnAutre.classList.remove("bg-[url(/images/logo/bootstrap_icon/three-dots-vertical.svg)]")
    }
})