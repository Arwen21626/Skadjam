let contMap = document.getElementById("contMap")
let mouvMap = document.getElementById("mouvMap")
let mouvMapTel = document.getElementById("mouvMapTel")
let mapy = document.getElementById("map")

// map est créer dans fo/recherche.php

mouvMap.addEventListener('click', function(){
    if (mouvMap.textContent == ">") {
        mouvMap.textContent = "< Ouvrir la carte"
        mapy.classList.add("md:hidden")
        contMap.classList.remove("w-80", "h-54", "w-9/10", "md:w-1/3", "md:h-85", "hidden", "md:bottom-0")
        mouvMap.classList.add("md:w-45", "md:h-6", "md:right-0", "md:rounded-md", "md:bg-vertFonce/90")
        contMap.classList.add("md:bottom-0", "md:sticky", "md:justify-end")
    }
    else{
        mouvMap.textContent = ">"
        mapy.classList.remove("md:hidden")
        contMap.classList.add("w-80", "h-54", "w-9/10", "md:w-1/3", "md:h-85", "hidden", "md:bottom-0")
        mouvMap.classList.remove("md:w-45", "md:h-6", "md:right-0", "md:rounded-md", "md:bg-vertFonce/90")
        contMap.classList.remove("md:bottom-0", "md:sticky", "md:justify-end")
    }
})

mouvMapTel.addEventListener('click', function(){
    if (mouvMapTel.textContent == "Ouvrir la carte") {
        mouvMapTel.textContent = "Fermer la carte"
        contMap.classList.remove("hidden")
        contMap.classList.add("left-5")
    }
    else if (mouvMapTel.textContent == "Fermer la carte") {
        mouvMapTel.textContent = "Ouvrir la carte"
        contMap.classList.remove("left-5")
        contMap.classList.add("hidden")
    }
})