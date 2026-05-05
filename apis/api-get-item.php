<?php
require_once __DIR__."/../db.php";
$item_pk = $_GET['item_pk'];
// 1. Check if ID exists
if( ! isset($item_pk) ){
    echo "System error: Missing ID";
    exit();
}

// 2. Fetch the specific item from the DB
try {
    $q = $_db->prepare('SELECT * FROM items WHERE item_pk = :item_pk');
    $q->bindValue(':item_pk', $item_pk);
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

$address_slug = urlencode(
    $item['item_road_name'] . "-" .
    $item['item_house_number'] . "-" .
    $item['item_zip_code'] . "-" .
    $item['item_city_name']
);
?>

<browser mix-update="aside">
    <section>
        <a href="/house/<?= $address_slug ?>">Open house page</a>
        <h2>Type: <?= htmlspecialchars($item['item_type']) ?></h2>
        <p>Price: kr. <?= number_format($item['item_price'], 0, ',', '.') ?></p>
        <p>Rooms: <?= number_format($item['item_number_of_rooms']) ?></p>
        <p>Address: <?= htmlspecialchars($item['item_road_name']) ?> <?= htmlspecialchars($item['item_house_number'])?>, <?= htmlspecialchars($item['item_zip_code']) ?>, <?= htmlspecialchars($item['item_city_name']) ?></p>
        <p>Energy label: <?= htmlspecialchars($item['item_energy_label']) ?></p>
        <img src="<?= htmlspecialchars($item['item_main_image_path']) ?>" alt="Image of a <?= htmlspecialchars($item['item_type']) ?>">
        <img src="<?= htmlspecialchars($item['item_floor_plan_path']) ?>" alt="Floor plan of a <?= htmlspecialchars($item['item_type'])?>">
        <a href="https://www.google.com/maps/place/<?= htmlspecialchars($item['item_road_name']) ?>+<?= htmlspecialchars($item['item_house_number']) ?>,+<?= htmlspecialchars($item['item_zip_code']) ?>+<?= htmlspecialchars($item['item_city_name']) ?>" target="_blank">Google maps</a>
        <button mix-put="api-mark-as-sold?item_pk=<?= $item_pk ?>">Mark as sold</button>
    </section>
</browser>