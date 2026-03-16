function ruptureStock(id){
    let carteProduit = document.getElementById(id)

    let divImage = carteProduit.querySelector(".img")

    let divBandeau = document.createElement("div")
    let texteBandeau = document.createElement("p")
    texteBandeau.textContent = "Hors-stock"

    divBandeau.classList.add("absolute", "inset-0", "flex", "items-center", "justify-center", "z-1")
    texteBandeau.classList.add("bg-rouge", "shadow-lg","text-white", "px-6", "py-2", "w-full", "text-center")

    divBandeau.appendChild(texteBandeau)
    divImage.appendChild(divBandeau)
}
