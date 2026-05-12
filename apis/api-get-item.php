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
    <article class="property-card visible-item selected-property">

        <div class="property-image">
            <img 
                src="<?= !empty($item['item_main_image_path']) 
                    ? htmlspecialchars($item['item_main_image_path']) 
                    : 'sofa_dummy.png' 
                ?>" 
                alt="Image of a <?= htmlspecialchars($item['item_type'] ?? 'item') ?>"
                onerror="this.src='sofa_dummy.png'"
            >
        </div>

        <div class="property-content">

            <p class="property-type">
                <?= htmlspecialchars($item['item_type']) ?>
                <?php if (!empty($item['item_energy_label']) && $item['item_energy_label'] !== '0'): ?>
                    |
                    Energy label <?= htmlspecialchars($item['item_energy_label']) ?>
                <?php endif; ?>
            </p>

            <h3 class="property-price">
                <?= number_format($item['item_price'], 0, ',', '.') ?> kr.
            </h3>

            <h4 class="property-address">
                <?= htmlspecialchars($item['item_road_name']) ?> 
                <?= htmlspecialchars($item['item_house_number']) ?>,
                <?= htmlspecialchars($item['item_zip_code']) ?>
                <?= htmlspecialchars($item['item_city_name']) ?>
            </h4>

            <div class="property-info">
                <div>
                    <span>Rooms</span>
                    <strong><?= number_format($item['item_number_of_rooms']) ?></strong>
                </div>

                <div>
                    <span>Area</span>
                    <strong><?= htmlspecialchars($item['item_floor_square_meters']) ?> m²</strong>
                </div>
            </div>

            <div class="property-actions">
                <a 
                    class="primary-btn btn"
                    href="https://www.google.com/maps/place/<?= htmlspecialchars($item['item_road_name']) ?>+<?= htmlspecialchars($item['item_house_number']) ?>,+<?= htmlspecialchars($item['item_zip_code']) ?>+<?= htmlspecialchars($item['item_city_name']) ?>" 
                    target="_blank"
                >
                   Maps
                </a>

                <!-- <button 
                    class="secondary-btn"
                    mix-put="api-mark-as-sold?item_pk=<?= $item_pk ?>"
                >
                    Mark as sold
                </button> -->

                <a href="/house/<?= $address_slug ?>"
                    class="secondary-btn btn"
                >
                    View property
                </a>
            </div>

           <!-- <div class="floor-plan">
                <img 
                    src="<?= !empty($item['item_floor_plan_path']) 
                        ? htmlspecialchars($item['item_floor_plan_path']) 
                        : 'floor_plan_dummy.png' 
                    ?>" 
                    alt="Floor plan of a <?= htmlspecialchars($item['item_type'] ?? 'item') ?>"
                    onerror="this.onerror=null; this.src='floor_plan_dummy.png';"
                >
            </div> -->

        </div>
    </article>
</browser>
