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

$title = "Home | PHP";
$head_extras = '

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css" />
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>
';

require_once __DIR__.'/_header.php';
?>

<header>
    <form method="GET" class="filter-form" id="filter-form" onsubmit="return false;">
        <div class="filter-group city-search-group">
            <label for="city-input">Cities</label>
            <div class="city-input-wrapper">
                <input type="text" id="city-input" autocomplete="off" placeholder="Add city..." oninput="getSuggestions(this.value)">
                <div id="city-pills" class="city-pills"></div>
            </div>
            <div id="city-suggestions" class="suggestions-list"></div>
        </div>

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
            <label>
                Price:
                <span id="min_price_val"><?= number_format($_GET['min_price'] ?? 0) ?></span>
                -
                <span id="max_price_val"><?= number_format($_GET['max_price'] ?? $max_db_price) ?></span>
            </label>
            <div class="dual-range-container">
                <input type="range" name="min_price" id="min_price" min="0" max="<?= $max_db_price ?>" step="100000" value="<?= $_GET['min_price'] ?? 0 ?>" oninput="updateRange(this, 'min', 'price')">
                <input type="range" name="max_price" id="max_price" min="0" max="<?= $max_db_price ?>" step="100000" value="<?= $_GET['max_price'] ?? $max_db_price ?>" oninput="updateRange(this, 'max', 'price')">
            </div>
        </div>

        <div class="filter-group">
            <label>
                Area (m²):
                <span id="min_area_val"><?= $_GET['min_area'] ?? 0 ?></span>
                -
                <span id="max_area_val"><?= $_GET['max_area'] ?? $max_db_area ?></span>
            </label>
            <div class="dual-range-container">
                <input type="range" name="min_area" id="min_area" min="0" max="<?= $max_db_area ?>" step="1" value="<?= $_GET['min_area'] ?? 0 ?>" oninput="updateRange(this, 'min', 'area')">
                <input type="range" name="max_area" id="max_area" min="0" max="<?= $max_db_area ?>" step="1" value="<?= $_GET['max_area'] ?? $max_db_area ?>" oninput="updateRange(this, 'max', 'area')">
            </div>
        </div>

        <div class="filter-group">
            <label>
                Rooms:
                <span id="min_rooms_val"><?= $_GET['min_rooms'] ?? 0 ?></span>
                -
                <span id="max_rooms_val"><?= $_GET['max_rooms'] ?? $max_db_rooms ?></span>
            </label>
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

<main>
    <div id="map"></div>
    <aside id="visible-items-list"></aside>
</main>

<script>
    window.INITIAL_CITIES = <?= json_encode(is_array($_GET['cities'] ?? []) ? ($_GET['cities'] ?? []) : explode(',', $_GET['cities'] ?? '')) ?>.filter(c => c !== '');
    window.INITIAL_ITEMS = <?= json_encode($items); ?>;
</script>
<script src="/static/aside.js" defer></script>


<?php require_once __DIR__.'/_footer.php'; ?>
