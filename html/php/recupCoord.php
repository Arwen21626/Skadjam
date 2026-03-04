<?php
include (__DIR__."/../01_premiere_connexion.php");

$coord = [];
foreach ($dbh->query("SELECT v.id_compte as id_compte, raison_sociale, latitude, longitude FROM sae3_skadjam._adresse a
                        INNER JOIN sae3_skadjam._habite h
                            ON a.id_adresse = h.id_adresse
                        INNER JOIN sae3_skadjam._vendeur v
                            ON h.id_compte = v.id_compte"
    , PDO::FETCH_ASSOC) as $row) {
    $coord[] = [
        'latitude' => $row['latitude'],
        'longitude' => $row['longitude'],
        'raison_sociale' => $row['raison_sociale'],
        'id_compte' => $row['id_compte']
    ];
} 
?>
<script>
    const coord = <?php echo json_encode($coord);?>;
</script>