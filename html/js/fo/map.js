let contMap = document.getElementById("contMap")
let mouvMap = document.getElementById("mouvMap")
let mouvMapTel = document.getElementById("mouvMapTel")
let mapy = document.getElementById("map")

// map est créer dans fo/recherche.php

mouvMap.addEventListener('click', function(){
    if (mouvMap.textContent == ">") {
        mouvMap.textContent = "< Ouvrir la carte"
        mapy.classList.add("md:hidden")
        contMap.classList.remove("w-80", "h-54", "md:w-1/3", "md:h-92")
    }
    else{
        mouvMap.textContent = ">"
        mapy.classList.remove("md:hidden")
        contMap.classList.add("w-80", "h-54", "md:w-1/3", "md:h-92")
    }
})

mouvMapTel.addEventListener('click', function(){
    if (mouvMapTel.textContent == "Ouvrir la carte") {
        mouvMapTel.textContent = "Fermer la carte"
        contMap.classList.remove("hidden")
    }
    else if (mouvMapTel.textContent == "Fermer la carte") {
        mouvMapTel.textContent = "Ouvrir la carte"
        contMap.classList.add("hidden")
    }
})