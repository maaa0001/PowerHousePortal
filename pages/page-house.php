<?php
require_once __DIR__."/../db.php";

$address_slug = urldecode($_GET['address_slug'] ?? '');
$parts = explode('-', $address_slug);

if( count($parts) < 4 ){
    echo "System error: Invalid address";
    exit();
}

$city_name = array_pop($parts);
$zip_code = array_pop($parts);
$house_number = array_pop($parts);
$road_name = implode('-', $parts);

if( ! $road_name || ! $house_number || ! $zip_code || ! $city_name ){
    echo "System error: Missing ID";
    exit();
}

try {
    $q = $_db->prepare('SELECT * FROM items WHERE item_road_name = :road_name AND item_house_number = :house_number AND item_zip_code = :zip_code AND item_city_name = :city_name LIMIT 1');
    $q->bindValue(':road_name', $road_name);
    $q->bindValue(':house_number', $house_number);
    $q->bindValue(':zip_code', $zip_code);
    $q->bindValue(':city_name', $city_name);
    $q->execute();
    $item = $q->fetch();

    if( ! $item ){
        echo "Item not found";
        exit();
    }
} catch(Exception $ex) {
    echo "System error: " . $ex->getMessage();
    exit();
}

$listing_images = json_decode($item['item_images_json'] ?? '[]', true);
if( ! is_array($listing_images) || ! count($listing_images) ){
    $listing_images = [];
    if( ! empty($item['item_main_image_path']) && $item['item_main_image_path'] !== "0" ){
        $listing_images[] = $item['item_main_image_path'];
    }
}
?>

<?php
$title = "House Details";
$body_class = "house-page";
$head_extras = '';
require_once __DIR__.'/_header.php';
?>
        <section class="house-layout">
            <div class="house-images">
                <?php foreach($listing_images as $image): ?>
                    <img src="<?= htmlspecialchars($image) ?>" alt="Image of a <?= htmlspecialchars($item['item_type']) ?>">
                <?php endforeach; ?>
                <img src="<?= htmlspecialchars($item['item_floor_plan_path']) ?>" alt="Floor plan of a <?= htmlspecialchars($item['item_type']) ?>">
            </div>

            <div class="house-info">
                <h1><?= htmlspecialchars($item['item_type']) ?></h1>
                <p>Price: kr. <?= number_format($item['item_price'], 0, ',', '.') ?></p>
                <p>Rooms: <?= number_format($item['item_number_of_rooms']) ?></p>
                <p>Floor area: <?= number_format($item['item_floor_square_meters']) ?> m²</p>
                <p>Lot area: <?= number_format($item['item_area_square_meters']) ?> m²</p>
                <p>Days listed: <?= number_format($item['item_days_listed']) ?></p>
                <p>Address: <?= htmlspecialchars($item['item_road_name']) ?> <?= htmlspecialchars($item['item_house_number']) ?>, <?= htmlspecialchars($item['item_zip_code']) ?>, <?= htmlspecialchars($item['item_city_name']) ?></p>
                <p>Energy label: <?= htmlspecialchars($item['item_energy_label']) ?></p>
                <div class="house-actions">
                    <a href="https://www.google.com/maps/place/<?= htmlspecialchars($item['item_road_name']) ?>+<?= htmlspecialchars($item['item_house_number']) ?>,+<?= htmlspecialchars($item['item_zip_code']) ?>+<?= htmlspecialchars($item['item_city_name']) ?>" target="_blank" rel="noopener noreferrer" class="primary-btn">Google maps</a>
                    <a href="/" class="secondary-btn">Back to map</a>
                </div>
            </div>
        </section>

<?php require_once __DIR__.'/_footer.php'; ?>
