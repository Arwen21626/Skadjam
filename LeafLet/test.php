<!-- Make sure you put this AFTER Leaflet's CSS -->
<?php 
include (__DIR__."/recupCoord.php");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css" />  
    <title>Map</title>
</head>
<style>
    #map {width: 700px; height: 500px; resize:both;}
</style>
<body>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.4.1/dist/leaflet.markercluster.js"></script>

    <div id="map"></div>

    <script>
        var nub;
        var map = L.map('map').setView([48.1, -3], 7.5);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        }).addTo(map)

        var markers = L.markerClusterGroup({
            iconCreateFunction: function(cluster) {
                var count = cluster.getChildCount();
                var color = count < 5 ? '#86D0CC' : count < 10 ? '#588A87' : '#365452';
                var textColor = count < 5 ? '#000000' : count < 10 ? '#FFFFFF' : '#FFFFFF';
                return L.divIcon({
                    html: '<div style="background:' + color + '; display:flex; align-items:center; justify-content:center; border-radius: 20px; width: 40px; height: 40px; border:solid #365452 0.5px; color: ' + textColor + '"><b>' + count + '</b></div>',
                    className: 'custom-cluster',
                    iconSize: L.point(40, 40),  
                })
            }
        })

        
        var pointer = L.icon({
            iconUrl: 'pointeurVertFonce.png',
            iconSize: [45, 70]
        });

        coord.forEach(function(element) {
            markers.addLayer(
                L.marker([element.latitude, element.longitude], { icon: pointer }).bindPopup(element.raison_sociale),
                num = element.id_compte
            )
        })

        map.addLayer(markers)
        
    </script>

</body>
</html>