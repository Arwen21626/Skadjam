// Création du pointeur avec ses propriétés
let pointer = L.icon({
    iconUrl: 'pointeurVertFonce.png',
    shadowUrl : 'ombre.png',

    iconSize:     [38, 95], // taille du pointeur
    shadowSize:   [50, 64], // taille de l'ombre
    iconAnchor:   [22, 94], // point of the icon which will correspond to marker's location
    shadowAnchor: [4, 62],  // the same for the shadow
    popupAnchor:  [-3, -76] // point from which the popup should open relative to the iconAnchor
})