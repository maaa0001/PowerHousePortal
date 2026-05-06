<?php

require_once __DIR__.'/../db.php';

require_once __DIR__.'/../apis/api-filter-items.php';

$stmt = $_db->query("SELECT DISTINCT item_type FROM items ORDER BY item_type");
$all_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $_db->query("SELECT MAX(item_price) FROM items");
$max_db_price = $stmt->fetchColumn() ?: 20000000;
$max_db_price = ceil($max_db_price / 100000) * 100000;

$stmt = $_db->query("SELECT MAX(CAST(item_floor_square_meters AS UNSIGNED)) FROM items");
$max_db_area = $stmt->fetchColumn() ?: 500;
$max_db_area = ceil($max_db_area / 10) * 10;

$stmt = $_db->query("SELECT MAX(CAST(item_number_of_rooms AS UNSIGNED)) FROM items");
$max_db_rooms = $stmt->fetchColumn() ?: 10;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="/static/mixhtml.js"></script>
    <script src="/static/app.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>


    <link rel="stylesheet" href="/static/app.css">
    <title>Document</title>
</head>
<body>

    <header>
        <form method="GET" class="filter-form" id="filter-form" onsubmit="return false;">
            <div class="filter-group">
                <label for="item_type">Type</label>
                <select name="item_type" id="item_type" onchange="filter()">
                    <option value="">All</option>
                    <?php foreach ($all_types as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= isset($_GET['item_type']) && $_GET['item_type'] == $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars(ucfirst($type)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label>Price: <span id="min_price_val"><?= number_format($_GET['min_price'] ?? 0) ?></span> - <span id="max_price_val"><?= number_format($_GET['max_price'] ?? $max_db_price) ?></span></label>
                <div class="dual-range-container">
                    <input type="range" name="min_price" id="min_price" min="0" max="<?= $max_db_price ?>" step="100000" value="<?= $_GET['min_price'] ?? 0 ?>" oninput="updateRange(this, 'min', 'price')">
                    <input type="range" name="max_price" id="max_price" min="0" max="<?= $max_db_price ?>" step="100000" value="<?= $_GET['max_price'] ?? $max_db_price ?>" oninput="updateRange(this, 'max', 'price')">
                </div>
            </div>

            <div class="filter-group">
                <label>Area (m²): <span id="min_area_val"><?= $_GET['min_area'] ?? 0 ?></span> - <span id="max_area_val"><?= $_GET['max_area'] ?? $max_db_area ?></span></label>
                <div class="dual-range-container">
                    <input type="range" name="min_area" id="min_area" min="0" max="<?= $max_db_area ?>" step="1" value="<?= $_GET['min_area'] ?? 0 ?>" oninput="updateRange(this, 'min', 'area')">
                    <input type="range" name="max_area" id="max_area" min="0" max="<?= $max_db_area ?>" step="1" value="<?= $_GET['max_area'] ?? $max_db_area ?>" oninput="updateRange(this, 'max', 'area')">
                </div>
            </div>

            <div class="filter-group city-search-group">
                <label for="city">Cities</label>
                <div id="city-pills" class="city-pills"></div>
                <input type="text" id="city-input" autocomplete="off" placeholder="Add city..." oninput="getSuggestions(this.value)">
                <div id="city-suggestions" class="suggestions-list"></div>
            </div>

            <div class="filter-group">
                <label>Rooms: <span id="min_rooms_val"><?= $_GET['min_rooms'] ?? 0 ?></span> - <span id="max_rooms_val"><?= $_GET['max_rooms'] ?? $max_db_rooms ?></span></label>
                <div class="dual-range-container">
                    <input type="range" name="min_rooms" id="min_rooms" min="0" max="<?= $max_db_rooms ?>" step="1" value="<?= $_GET['min_rooms'] ?? 0 ?>" oninput="updateRange(this, 'min', 'rooms')">
                    <input type="range" name="max_rooms" id="max_rooms" min="0" max="<?= $max_db_rooms ?>" step="1" value="<?= $_GET['max_rooms'] ?? $max_db_rooms ?>" oninput="updateRange(this, 'max', 'rooms')">
                </div>
            </div>

            <div class="filter-actions">
                <a href="/" class="clear-btn">Clear</a>
            </div>
        </form>
    </header>

    <script>
        let filterTimeout = null;
        let suggestionTimeout = null;
        let selectedCities = <?= json_encode(is_array($_GET['cities'] ?? []) ? ($_GET['cities'] ?? []) : explode(',', $_GET['cities'] ?? '')) ?>.filter(c => c !== '');

        // Initialize pills on load
        window.addEventListener('DOMContentLoaded', () => {
            renderPills();
        });

        function filter() {
            console.log("Filter triggered");
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                const form = document.getElementById('filter-form');
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);
                const cleanParams = new URLSearchParams();
                for (const [key, value] of searchParams.entries()) {
                    if (value && key !== 'cities') cleanParams.set(key, value);
                }
                if (selectedCities.length > 0) {
                    cleanParams.set('cities', selectedCities.join(','));
                }
                const url = `/apis/api-search.php?${cleanParams.toString()}`;
                console.log("Fetching filtered data from:", url);
                mix_fetch(url, "GET", null, false);
            }, 300);
        }

        function updateRange(el, type, suffix) {
            const minEl = document.getElementById(`min_${suffix}`);
            const maxEl = document.getElementById(`max_${suffix}`);
            const minVal = parseInt(minEl.value);
            const maxVal = parseInt(maxEl.value);

            if (type === 'min' && minVal > maxVal) {
                minEl.value = maxVal;
            } else if (type === 'max' && maxVal < minVal) {
                maxEl.value = minVal;
            }

            if (suffix === 'price') {
                document.getElementById(`min_${suffix}_val`).innerText = parseInt(minEl.value).toLocaleString();
                document.getElementById(`max_${suffix}_val`).innerText = parseInt(maxEl.value).toLocaleString();
            } else {
                document.getElementById(`min_${suffix}_val`).innerText = minEl.value;
                document.getElementById(`max_${suffix}_val`).innerText = maxEl.value;
            }
            filter();
        }

        async function getSuggestions(query) {
            clearTimeout(suggestionTimeout);
            const suggestionsContainer = document.getElementById('city-suggestions');
            
            if (query.length < 2) {
                suggestionsContainer.innerHTML = '';
                return;
            }

            suggestionTimeout = setTimeout(async () => {
                const response = await fetch(`/apis/api-city-suggestions.php?city=${query}`);
                const cities = await response.json();
                
                suggestionsContainer.innerHTML = '';
                cities.forEach(city => {
                    if (selectedCities.includes(city)) return;
                    const div = document.createElement('div');
                    div.className = 'suggestion-item';
                    div.innerText = city;
                    div.onmousedown = (e) => {
                        e.preventDefault(); // Prevent input blur
                        selectCity(city);
                    };
                    suggestionsContainer.appendChild(div);
                });
            }, 200);
        }

        function selectCity(city) {
            if (!selectedCities.includes(city)) {
                selectedCities.push(city);
                renderPills();
                filter();
            }
            const cityInput = document.getElementById('city-input');
            cityInput.value = '';
            document.getElementById('city-suggestions').innerHTML = '';
        }

        function removeCity(city) {
            selectedCities = selectedCities.filter(c => c !== city);
            renderPills();
            filter();
        }

        function renderPills() {
            const container = document.getElementById('city-pills');
            container.innerHTML = '';
            selectedCities.forEach(city => {
                const pill = document.createElement('div');
                pill.className = 'city-pill';
                pill.innerHTML = `
                    <span>${city}</span>
                    <button type="button" onclick="removeCity('${city}')">&times;</button>
                `;
                container.appendChild(pill);
            });
        }

        // Close suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.city-search-group')) {
                document.getElementById('city-suggestions').innerHTML = '';
            }
        });
    </script>

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
                markers.addLayer(marker)
            });
            map.addLayer(markers)


        </script>


        <aside></aside>
    </main>




</body>
</html>
