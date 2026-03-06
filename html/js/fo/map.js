let contMap = document.getElementById("contMap")
let mouvMap = document.getElementById("mouvMap")
let mapy = document.getElementById("map")
// map est créer dans fo/recherche.php
mouvMap.addEventListener('click', function(){
    if (mouvMap.textContent == "Fermer >") {
        mouvMap.textContent = "< Ouvrir la carte"
        mapy.classList.add("hidden")
        contMap.classList.remove("w-80", "h-54", "md:w-1/3", "md:h-92")
    }
    else{
        mouvMap.textContent = "Fermer >"
        mapy.classList.remove("hidden")
        contMap.classList.add("w-80", "h-54", "md:w-1/3", "md:h-92")
    }
})