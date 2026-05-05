<?php

require_once __DIR__.'/../db.php';
$sql = "SELECT * FROM items";
$stmt = $_db->prepare( $sql);
$stmt->execute();
$items = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="/static/mixhtml.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>


    <link rel="stylesheet" href="/static/app.css">
    <title>Document</title>
</head>
<body>

    <main>
        <div id="map"></div>


            <script>


            // Create custom icon
            var house_icon = L.icon({
                // iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconUrl: 'http://127.0.0.1/static/house.svg',
                iconSize: [24, 24],
                iconAnchor: [16, 16],
                popupAnchor: [0, -24]
            });
            var apartment_icon = L.icon({
                // iconUrl: 'https://cdn-icons-png.flaticon.com/512/684/684908.png',
                iconUrl: 'http://127.0.0.1/static/apartment.svg',
                iconSize: [24, 24],
                iconAnchor: [16, 16],
                popupAnchor: [0, -24]
            });

            // Initialize the map
            const map = L.map('map').setView([55.67960020013266, 12.56464935119663], 7);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO',
                subdomains: 'abcd',
                maxZoom: 18
            }).addTo(map);

            // var markers = L.markerClusterGroup();
            var markers = L.markerClusterGroup({
                disableClusteringAtZoom: 15,  // <- key option
                spiderfyOnMaxZoom: true,
                // showCoverageOnHover: false,
                maxClusterRadius: 100   // default is 100 pixels
            });

            const items = <?php echo json_encode($items); ?>

            items.forEach(item => {
                if( item.item_type == "villa" ){
                    var marker = L.marker([item.item_lat, item.item_lon], {
                        icon: L.divIcon({
                            className: '',
                            html: `
                                <button
                                    class="marker ${item.item_type}" onclick="mixhtml(); return false;"
                                    mix-get="api-get-item?item_pk=${item.item_pk}">
                                </button>
                            `,
                        }),
                        item_pk: item.item_pk
                    });
                }
                else if( item.item_type == "condo" ){
                    var marker = L.marker([item.item_lat, item.item_lon], {
                        icon: L.divIcon({
                            className: '',
                            html: `
                                <button
                                    class="marker ${item.item_type}" onclick="mixhtml(); return false;"
                                    mix-get="api-get-item?item_pk=${item.item_pk}">
                                </button>
                            `,
                        }),
                        item_pk: item.item_pk
                    });
                }
                else{
                    var marker = L.marker([item.item_lat, item.item_lon], {
                        icon: L.divIcon({
                            className: '',
                            html: `
                                <button
                                    class="marker ${item.item_type}" onclick="mixhtml(); return false;"
                                    mix-get="api-get-item?item_pk=${item.item_pk}">
                                </button>
                            `,
                        }),
                        item_pk: item.item_pk
                    });
                }
                markers.addLayer(marker)
            });
            map.addLayer(markers)


        </script>


        <aside></aside>
    </main>




</body>
</html>
