var marqueur = false
var dernierMarqueur = null
checkedVendeurs = []


function marqueurId(id){
    let marqueurTrouve = null

    // Quand on clique sur un marqueur, on vérifie avec l'id si c'est le bon et on le renvoie
    markers.eachLayer(function(layer){
        if(layer.options.id_compte === id){
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

    // changer icone
    marker.setIcon(marqueurClair)

    // On dé-selectionne la checkbox du vendeur
    let checkbox = document.getElementById(id)
    if(checkbox){
        checkbox.checked = false
    }

    // On enlève le marqueur selectionné
    let index = checkedVendeurs.indexOf(id)
    if(index !== -1){
        checkedVendeurs.splice(index,1)
    }

    // On reset le dernierMarqueur
    dernierMarqueur = null
}

function deselectionAll(){

    // Dé-sélectionne tout les vendeurs coché
    checkedVendeurs.forEach(id => {
        deselectionnerVendeur(Number(id))
    })

    // Dé-sélectionne le marqueur sur la map
    checkedVendeurs = []

}

L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap'
}).addTo(map)

var markers = L.markerClusterGroup({
    // Enlève l'hexagone bleu avec les cluster 
    showCoverageOnHover: false,

    //création des cluster
    iconCreateFunction: function(cluster) {
        var count = cluster.getChildCount()
        var color = count < 5 ? '#86D0CC' : count <= 10 ? '#588A87' : '#365452'
        var textColor = count < 5 ? '#000000' : count <= 10 ? '#FFFFFF' : '#FFFFFF'
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

coord.forEach(function(element) {
    // Création des marqueurs avec les coordonnées de la BDD
    markers.addLayer(
        L.marker([element.latitude, element.longitude], {
            icon: marqueurClair,
            id_compte: element.id_compte 
        }).bindPopup(element.raison_sociale),
    )
})

tabVendeur.forEach(vendeur => {

    if(vendeur.raison_sociale != 'Anonyme'){

        let id = Number(vendeur.id_compte)

        document.getElementById(id).addEventListener("change", function (){

            // Si on coche un vendeur avec les checkbox alors on dé-selectionne l'ancien et on met à jour la recherche et la map
            if(this.checked){

                deselectionAll()
                selectionnerVendeur(id)
                checkedVendeurs = [id]

            // Si on coche le même vendeur déjà selectionné alors on l'enlève et le marqueur associé s'enlève aussi
            }else{

                deselectionAll()
            }
            
            // On met à jour la recherche
            tab = filtre()
            mettreAJourListe()
        })
    }
})

markers.on("click", function(e){

    let id = Number(e.layer.options.id_compte)

    // Si on re-sélectionne le marqueur alors il se retire et la checkbox associé s'enlève
    if(dernierMarqueur === e.layer){
        deselectionAll()
        

    // Sinon on dé-sélectionne l'ancien marqueur et la checkbox associé
    }else{
        deselectionAll()
        selectionnerVendeur(id)
        checkedVendeurs = [id]
    }

    // On met à jour la recherche
    tab = filtre()
    mettreAJourListe()

})

// On ajoute à la map
map.addLayer(markers)