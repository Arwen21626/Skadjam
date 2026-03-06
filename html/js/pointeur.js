var marqueur = false
var dernierMarqueur = null
var coche = []

var map = L.map('map').setView([48, -3], 7)

// Fonction qui permet de changer la couleur
function changeCouleur(){
    var dernierMarqueur = null
    let urlActuelle = e.layer.options.icon.options.iconUrl
    let couleur

    // changement de la couleur
    if (urlActuelle.includes("pointeurVertClair.png")) {
        couleur = e.layer.setIcon(marqueurFonce)
        dernierMarqueur = e.layer
    } else {
        couleur = e.layer.setIcon(marqueurClair)
        dernierMarqueur = null
    }

    return couleur
}


L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
}).addTo(map)

var markers = L.markerClusterGroup({
    showCoverageOnHover: false,
    iconCreateFunction: function(cluster) {
        var count = cluster.getChildCount()
        var color = count < 5 ? '#86D0CC' : count < 10 ? '#588A87' : '#365452'
        var textColor = count < 5 ? '#000000' : count < 10 ? '#FFFFFF' : '#FFFFFF'
        return L.divIcon({
            html: '<div style="background:' + color + '; display:flex; align-items:center; justify-content:center; border-radius: 20px; width: 40px; height: 40px; border:solid #365452 0.5px; color: ' + textColor + '"><b>' + count + '</b></div>',
            className: 'custom-cluster',
        })
    }
})

var marqueurFonce = L.icon({
    iconUrl: '../../images/logo/pointeurVertFonce.png',
    iconSize: [45, 70],
});

var marqueurClair = L.icon({
    iconUrl: '../../images/logo/pointeurVertClair.png',
    iconSize: [45, 70],
});

marqueur = marqueurClair;

coord.forEach(function(element) {
    markers.addLayer(
        L.marker([element.latitude, element.longitude], {
            icon: marqueur,
            id_compte: element.id_compte 
        }).bindPopup(element.raison_sociale),
    )
})

// console.log(tabVendeur)
// console.log(tabVendeur[0].raison_sociale)

tabVendeur.forEach(vendeur => {
    
    if(vendeur.raison_sociale != 'Anonyme'){
        document.getElementById(vendeur.id_compte).addEventListener("change", function (){
            // console.log(document.getElementById(vendeur.id_compte))
            coche.push([vendeur.id_compte, document.getElementById(vendeur.id_compte).checked])
        })
    }

    for (let i = 0; i < coche.length; i++) {
        console.log(coche[i][0])
        if(coche[i][1] == true){
            e.layer.setIcon(marqueurFonce)
        }else{
            e.layer.setIcon(marqueurClair)
        }
    }
});


markers.on("click", function(e) {

    let urlActuelle = e.layer.options.icon.options.iconUrl;
    let idCompte = e.layer.options.id_compte;

    // Remettre l'ancien marqueur en clair si on clique sur un autre
    if (dernierMarqueur && dernierMarqueur !== e.layer) {
        dernierMarqueur.setIcon(marqueurClair);
        document.getElementById(dernierMarqueur.options.id_compte).checked = false
    }

    // changement de la couleur
    if (urlActuelle.includes("pointeurVertClair.png")) {
        e.layer.setIcon(marqueurFonce)
        dernierMarqueur = e.layer
    } else {
        e.layer.setIcon(marqueurClair)
        dernierMarqueur = null
    }

    if(document.getElementById(idCompte).checked == false){
        e.layer.setIcon(marqueurFonce)
    }else{
        e.layer.setIcon(marqueurClair)
    }

    map.flyTo(e.layer.getLatLng(), map.getZoom());


    // Filtre vendeur

    // Si le vendeur est déjà coché, on le décoche
    if(idCompte == checkedVendeurs[0]){
        checkedVendeurs.splice(checkedVendeurs.indexOf(idCompte), 1)
        document.getElementById(idCompte).checked = false

    // Sinon, on le coche
    } else {
        document.getElementById(idCompte).checked = true
        checkedVendeurs.splice(checkedVendeurs.indexOf(idCompte), 1)
        checkedVendeurs.push(idCompte)
    }

    if(document.getElementById(idCompte).checked == false){
        checkedVendeurs.splice(checkedVendeurs.indexOf(idCompte), 1)
    }

    tab = filtre()
    mettreAJourListe()
});



map.addLayer(markers)

