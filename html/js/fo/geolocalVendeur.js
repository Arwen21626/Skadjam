// Récup des elts du formulaire
let adresse = document.getElementById("adresse")
let ville = document.getElementById("ville")
let cP = document.getElementById("cP")
let lat = document.getElementById("latitude")
let longi = document.getElementById("longitude")
let errorMap = document.getElementById("errorMap")

// Var intermédiaires
let adrTemp = ''
let villeTemp = ''
let cpTemp = ''
let latTemp = ''
let longiTemp =''

// Var importantes
let eltsAdr = [[adresse, adrTemp], [ville, villeTemp], [cP, cpTemp]]
let eltsCoord = [[lat, latTemp],[longi, longiTemp]]

let coordonnes = [{}]
let adrComplete = ''
let api = ''

// Créer le style du marqueur
var pointerFonce = L.icon({
    iconUrl: '../../images/logo/pointeurVertFonce.png',
    iconSize: [45, 70],
}); 

// Créer le marqueur
if (lat.value != 0 && longi.value != 0) {
    var marker = L.marker([lat.value, longi.value], {
                icon: pointerFonce,
                draggable:true
    })
}else{
    var marker = L.marker([0, 0], {
                icon: pointerFonce,
                draggable:true
    })
}


map.addLayer(marker)


// Mettre les cordonnées à jour quand on déplace le marqueur
marker.on('drag', function(){
    coordonnes.latitude = marker.getLatLng().lat
    coordonnes.longitude = marker.getLatLng().lng
    api = 'https://data.geopf.fr/geocodage/reverse?lat='+coordonnes.latitude+'&lon='+coordonnes.longitude

    fetch(api)
    .then(response => response.json())
    .then(data =>{
        // Si au moins 1 adresses a été trouvée on affiche le résultat
        if (data.features.length != 0) {
            // Récupération de l'adresse du premier elt du tab renvoyé par l'api
            // car trié par ordre croissant, on prend le plus gros score
            // Puis l'afficher
            ville.value = data.features[0].properties.city 
            adresse.value = data.features[0].properties.name
            cP.value = data.features[0].properties.postcode
            errorMap.classList.add("hidden")

            // Mettre à jour les coordonnées affichées
            lat.value = coordonnes.latitude
            longi.value = coordonnes.longitude
        }
        // Sinon on signale une erreur
        else{
            errorMap.textContent = "Aucune adresse n'a été trouvée. Déplacez le marqueur sur un autre endroit"
        }
        
    })
    //Mettre à jour les coordonnées du point
    marker.setLatLng([coordonnes.latitude,coordonnes.longitude])
})

// Place le point en fonction des coordonnées rentrées par le user
eltsCoord.forEach(elt => {
    elt[0].addEventListener('blur', function(){
        let insert = ''
        let removeLettre = ''
        elt[1] = Number(elt[0].value)
        if (elt[0].value.match(/[0-9\.\,]/)){
            insert = elt[0].value
            console.log("insert if: "+insert)
            if (eltsCoord[0][1] != '' && eltsCoord[1][1] != '') {
                marker.setLatLng([eltsCoord[0][1],eltsCoord[1][1]])
            }
        }
        else{
            removeLettre = insert.replace(/\D/g, "");
            elt[0].value = removeLettre
            errorMap.textContent = "Vous ne pouvez saisir que des nombres. (Ex: 49,12)"
        }
    })
});

// Mettre le point en cliquant sur la carte
function onMapClick(e) {
    // Récupérer les nouvelles coordonnées
    coordonnes.latitude = e.latlng.lat 
    coordonnes.longitude = e.latlng.lng

    // Récupérer l'adresse des nouvelles coord via l'api
    api = 'https://data.geopf.fr/geocodage/reverse?lat='+coordonnes.latitude+'&lon='+coordonnes.longitude
    fetch(api)
    .then(response => response.json())
    .then(data =>{
        if (data.features.length != 0) {
            // Récupération de l'adresse du premier elt du tab renvoyé par l'api
            // car trié par ordre croissant, on prend le plus gros score
            ville.value = data.features[0].properties.city 
            adresse.value = data.features[0].properties.name
            cP.value = data.features[0].properties.postcode
            errorMap.classList.add("hidden")

            // Mettre à jour les coordonnées affichées
            lat.value = coordonnes.latitude
            longi.value = coordonnes.longitude
        }
        else{
            errorMap.textContent = "Aucune adresse n'a été trouvée. Déplacez le marqueur sur un autre endroit"
        }
    })
    //Mettre à jour les coordonnées du point
    marker.setLatLng([coordonnes.latitude,coordonnes.longitude])

    
}
map.on('click', onMapClick);


// Mise en place des ecouteurs sur les elts de l'adresse
eltsAdr.forEach(elt => {
    // eltsAdr[0][1] = adrTempr etc...
    elt[0].addEventListener('input', function() {
        elt[1] = elt[0].value
        adrComplete = (eltsAdr[0][1]+'+'+eltsAdr[1][1]+'+'+eltsAdr[2][1]).replaceAll(' ','+')
        api = 'https://data.geopf.fr/geocodage/search?q='+adrComplete
    
    })
    elt[0].addEventListener('blur', function(){
        // Mettre le point et les coordonnées à l'adresse entrée par le user
        if(api != '' && eltsAdr[0][1] != '' && eltsAdr[1][1] != '' && eltsAdr[2][1] != ''){
            fetch(api)
            .then(response => response.json())
            .then(data =>{
                // Récupération des coordonnées du premier elt du tab renvoyé par l'api
                // car trié par ordre croissant, on prend le plus gros score
                coordonnes.latitude = data.features[0].geometry.coordinates[1]
                coordonnes.longitude = data.features[0].geometry.coordinates[0]

                lat.value = coordonnes.latitude
                longi.value = coordonnes.longitude

                marker.setLatLng([coordonnes.latitude,coordonnes.longitude])
            })
        }
    })
    
});

// 11 clos des cerisiers