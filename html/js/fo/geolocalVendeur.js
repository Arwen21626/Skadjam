// Récup des elts du formulaire
let adresse = document.getElementById("adresse")
let ville = document.getElementById("ville")
let cP = document.getElementById("cP")
let lat = document.getElementById("latitude")
let longi = document.getElementById("longitude")

// Var intermédiaires
let adrTemp = ''
let villeTemp = ''
let cpTemp = ''
let latTemp = ''
let longiTemp =''

let eltsAdr = [[adresse, adrTemp], [ville, villeTemp], [cP, cpTemp]]
let eltsCoord = [[lat, latTemp],[longi, longiTemp]]

let coordonnes = [{}]
let adrComplete = ''
let api = ''

var pointerFonce = L.icon({
    iconUrl: '../../images/logo/pointeurVertFonce.png',
    iconSize: [45, 70],
}); 

var marker = L.marker([0, 0], {
                icon: pointerFonce,
                draggable:true
            })

map.addLayer(marker)

marker.on('drag', function(){
    lat.value = marker.getLatLng().lat
    longi.value = marker.getLatLng().lng
})

eltsCoord.forEach(elt => {
    elt[0].addEventListener('input', function(){
        elt[1] = Number(elt[0].value)
        if (eltsCoord[0][1] != '' && eltsCoord[0][1] != '') {
            marker.setLatLng([eltsCoord[0][1],eltsCoord[1][1]])
        }
    })
});

//47.786835
//-3.4172


// Pour permettre de mettre le point en cliquant sur la carte
// map.on('click', function(){})


// Mise en place des ecouteurs sur les elts de l'adresse
eltsAdr.forEach(elt => {
    // eltsAdr[0][1] = adrTempr etc...
    elt[0].addEventListener('input', function() {
        elt[1] = elt[0].value
        adrComplete = (eltsAdr[0][1]+'+'+eltsAdr[1][1]+'+'+eltsAdr[2][1]).replaceAll(' ','+')
        api = 'https://data.geopf.fr/geocodage/search?q='+adrComplete
    
    })
    elt[0].addEventListener('blur', function(){
        if(api != '' && eltsAdr[0][1] != '' && eltsAdr[1][1] != '' && eltsAdr[2][1] != ''){
            fetch(api)
            .then(response => response.json())
            .then(data =>{
                // Récupération des coordonnées du premier elt du tab renvoyé par l'api
                // car trié par ordre croissant, on prend le plus gros score
                coordonnes.latitude = data.features[0].geometry.coordinates[1]
                coordonnes.longitude = data.features[0].geometry.coordinates[0]

                // marker = L.marker([coordonnes.latitude, coordonnes.longitude], {
                //     icon: pointerFonce,
                //     draggable:true
                // })

                lat.value = coordonnes.latitude
                longi.value = coordonnes.longitude

                marker.setLatLng([coordonnes.latitude,coordonnes.longitude])
                
            })
        }
    })
    
});

// 11 clos des cerisiers