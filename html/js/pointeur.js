var marqueur = false
var dernierMarqueur = null


function marqueurId(id){
    let marqueurTrouve = null

    markers.eachLayer(function(layer){
        if(layer.options.id_compte == id){
            marqueurTrouve = layer  
        }
    })

    return marqueurTrouve
}

function selectionnerVendeur(id){

    let marker = marqueurId(id)

    if(!marker) return

    // changer icone
    marker.setIcon(marqueurFonce)

    // cocher checkbox
    document.getElementById(id).checked = true

    // mémoriser le dernier marqueur
    dernierMarqueur = marker

    // recentrer la carte sur le marqueur
    map.setView(marker.getLatLng(), 13)
}

function deselectionnerVendeur(id){

    let marker = marqueurId(id)

    if(!marker) return

    marker.setIcon(marqueurClair)
    document.getElementById(id).checked = false
    dernierMarqueur = null
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

tabVendeur.forEach(vendeur => {

    if(vendeur.raison_sociale != 'Anonyme'){

        document.getElementById(vendeur.id_compte).addEventListener("change", function (){
            
            if(document.getElementById(vendeur.id_compte).checked){

                if(dernierMarqueur){
                    deselectionnerVendeur(dernierMarqueur.options.id_compte)
                }
                selectionnerVendeur(vendeur.id_compte)
            }else{
                deselectionnerVendeur(vendeur.id_compte)
            }
            
            console.log(checkedVendeurs)
            tab = filtre()
            mettreAJourListe()
        })
    }
})


markers.on("click", function(e){

    let id = e.layer.options.id_compte

    if(dernierMarqueur && dernierMarqueur != e.layer){
        deselectionnerVendeur(dernierMarqueur.options.id_compte)
    }

    if(dernierMarqueur == e.layer){
        deselectionnerVendeur(id)
    }else{
        selectionnerVendeur(id)
    }


    // Filtre vendeur
    // Si le vendeur est déjà coché, on le décoche
    if(id == checkedVendeurs[0]){
        checkedVendeurs.splice(checkedVendeurs.indexOf(id), 1)
        document.getElementById(id).checked = false

    // Sinon, on le coche
    } else {
        document.getElementById(id).checked = true
        checkedVendeurs.splice(checkedVendeurs.indexOf(id), 1)
        checkedVendeurs.push(id)
    }

    tab = filtre()
    mettreAJourListe()

})

map.addLayer(markers)