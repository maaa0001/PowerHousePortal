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
                                    mix-get="api-get-item?item_pk=${item.itme_pk}">
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
                                    mix-get="api-get-item?item_pk=${item.itme_pk}">
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
                                    mix-get="api-get-item?item_pk=${item.itme_pk}">
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


        <div id="info"></div>
    </main>




</body>
</html>


/*
$userController = new UserController(); // Instantiate the user class
$userController->set_name("Malthe");
// echo $user->name;

echo $userController->save();
*/




/*
ini_set("display_errors", 1);

require_once __DIR__.'/../User.php';
require_once __DIR__.'/../Item.php';
require_once __DIR__.'/../Pet.php';

// Instantiate the object
// Create an object form the code
// Instantiation

$me = new User("Bro", "12");
$dad = new User("Dad ", "2");
$mom = new User("mommy", "3");

$mom->pet = new Pet("Garfield");

$me->dad = $dad;
$me->mom = $mom;

echo $me->dad->get_cpr();
echo $me->mom->get_cpr();
echo $me->mom->pet->name;
*/


/*
$me = new User("Malthe", "123456-7890");
echo $me->pet;
*/



/*
$user = new User("Malthe", "123456-7890");

// echo $user->name;
// echo $user->get_cpr();

$item = new Item("XXX", "890");
// echo $item->title;
// echo $item->price;

$cat = new Pet("Garfield");
// echo $cat->name;

$user->pet = $cat;
echo $user->pet->name;
*/