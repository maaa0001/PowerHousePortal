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

$from_slug = function($value){
    return str_replace('_', ' ', (string)$value);
};

$city_name = $from_slug($city_name);
$zip_code = $from_slug($zip_code);
$house_number = $from_slug($house_number);
$road_name = $from_slug($road_name);

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

$floor_plan_image = $item['item_floor_plan_path'] ?? '';
if( ! count($listing_images) ){
    $listing_images[] = '/static/images/sofa_dummy.png';
}

if( ! empty($floor_plan_image) && $floor_plan_image !== "0" ){
    $listing_images = array_values(array_filter($listing_images, function($image) use ($floor_plan_image) {
        return $image !== $floor_plan_image;
    }));
    $listing_images[] = $floor_plan_image;
} else {
    $floor_plan_image = '/static/images/floor_plan_dummy.png';
    $listing_images[] = $floor_plan_image;
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
                <div class="house-carousel-meta">
                    <span id="carousel-count"></span>
                </div>
                <div class="house-carousel" id="house-carousel">
                    <button type="button" class="house-carousel-btn prev" id="carousel-prev" aria-label="Previous image">&#8249;</button>
                    <div class="house-carousel-track" id="carousel-track">
                        <?php foreach($listing_images as $image): ?>
                            <?php $image_fallback = ($image === $floor_plan_image) ? '/static/images/floor_plan_dummy.png' : '/static/images/sofa_dummy.png'; ?>
                            <img class="house-carousel-image" src="<?= htmlspecialchars($image) ?>" alt="Image of a <?= htmlspecialchars($item['item_type']) ?>" onerror="this.onerror=null; this.src='<?= $image_fallback ?>';">
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="house-carousel-btn next" id="carousel-next" aria-label="Next image">&#8250;</button>
                </div>
                <div class="house-carousel-thumbs" id="carousel-thumbs">
                    <?php foreach($listing_images as $index => $image): ?>
                        <?php $image_fallback = ($image === $floor_plan_image) ? '/static/images/floor_plan_dummy.png' : '/static/images/sofa_dummy.png'; ?>
                        <button type="button" class="house-carousel-thumb<?= $index === 0 ? ' active' : '' ?>" data-index="<?= $index ?>" aria-label="Show image <?= $index + 1 ?>">
                            <img src="<?= htmlspecialchars($image) ?>" alt="Thumbnail of <?= htmlspecialchars($item['item_type']) ?>" onerror="this.onerror=null; this.src='<?= $image_fallback ?>';">
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="house-info">
                <p class="house-type-label"><?= htmlspecialchars(ucfirst($item['item_type'])) ?></p>
                <h1 class="house-title"><?= htmlspecialchars($item['item_road_name']) ?> <?= htmlspecialchars($item['item_house_number']) ?></h1>
                <p class="house-address"><?= htmlspecialchars($item['item_city_name']) ?>, <?= htmlspecialchars($item['item_zip_code']) ?></p>

                <div class="house-price-row">
                    <p class="house-price"><?= number_format((float)($item['item_price'] ?: 0), 0, ',', '.') ?> kr.</p>
                    <span class="house-price-note">Listed for <?= number_format((float)($item['item_days_listed'] ?: 0), 0, ',', '.') ?> days</span>
                </div>

                <div class="house-specs">
                    <span class="house-spec-chip"><span class="house-spec-icon"><svg xmlns="http://www.w3.org/2000/svg" id="area_svg__expanded" viewBox="0 0 24 24" class="text-blue-900 w-4 h-4"><defs><style>.area_svg__cls-1{fill:#060d42}</style></defs><path d="M9 14v-2.66L5.34 15 9 18.66V16h6v2.66L18.66 15 15 11.34V14z" class="area_svg__cls-1"></path><path d="M12 1.73 2 9.51V23h20V9.51ZM20 21H4V10.49l8-6.22 8 6.22Z" class="area_svg__cls-1"></path></svg></span> Boligareal: <?= number_format((float)($item['item_floor_square_meters'] ?: 0), 0, ',', '.') ?> m²</span>
                    <span class="house-spec-chip"><span class="house-spec-icon"><svg xmlns="http://www.w3.org/2000/svg" id="settings_svg__expanded" viewBox="0 0 24 24" class="text-blue-900 w-4 h-4"><defs><style>.settings_svg__cls-1{fill:#060d42}</style></defs><path d="M1 1v22h22V1Zm20 20H3V3h18Z" class="settings_svg__cls-1"></path><path d="M12 17H8.41l2.3-2.29-1.42-1.42L7 15.59V12H5v7h7zM13.29 9.29l1.42 1.42L17 8.41V12h2V5h-7v2h3.59zM10.296 12.29l2.002-2.001 1.414 1.414-2.001 2.001z" class="settings_svg__cls-1"></path></svg></span> Grund: <?= number_format((float)($item['item_area_square_meters'] ?: 0), 0, ',', '.') ?> m²</span>
                    <span class="house-spec-chip"><span class="house-spec-icon"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="text-blue-900 w-4 h-4"><path d="M4 1v17.74l12 3.6V19h4V1Zm2 16.26V3.34l8 2.4v5.92l-3-.9v2.08l3 .9v5.92ZM18 17h-2V4.26L11.81 3H18Z" style="fill: rgb(6, 13, 66);"></path></svg></span> <?= number_format((float)($item['item_number_of_rooms'] ?: 0), 0, ',', '.') ?> værelser</span>
                    <span class="house-spec-chip"><span class="house-spec-icon"><svg fill="#000000" viewBox="0 0 32 32" version="1.1" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M18.605 2.022v0zM18.605 2.022l-2.256 11.856 8.174 0.027-11.127 16.072 2.257-13.043-8.174-0.029zM18.606 0.023c-0.054 0-0.108 0.002-0.161 0.006-0.353 0.028-0.587 0.147-0.864 0.333-0.154 0.102-0.295 0.228-0.419 0.373-0.037 0.043-0.071 0.088-0.103 0.134l-11.207 14.832c-0.442 0.607-0.508 1.407-0.168 2.076s1.026 1.093 1.779 1.099l5.773 0.042-1.815 10.694c-0.172 0.919 0.318 1.835 1.18 2.204 0.257 0.11 0.527 0.163 0.793 0.163 0.629 0 1.145-0.294 1.533-0.825l11.22-16.072c0.442-0.607 0.507-1.408 0.168-2.076-0.34-0.669-1.026-1.093-1.779-1.098l-5.773-0.010 1.796-9.402c0.038-0.151 0.057-0.308 0.057-0.47 0-1.082-0.861-1.964-1.939-1.999-0.024-0.001-0.047-0.001-0.071-0.001v0z"></path> </g></svg></span> Energy: <?= htmlspecialchars($item['item_energy_label']) ?></span>
                </div>

                <div class="house-actions">
                    <a href="https://www.google.com/maps/place/<?= htmlspecialchars($item['item_road_name']) ?>+<?= htmlspecialchars($item['item_house_number']) ?>,+<?= htmlspecialchars($item['item_zip_code']) ?>+<?= htmlspecialchars($item['item_city_name']) ?>" target="_blank" rel="noopener noreferrer" class="primary-btn">Google maps</a>
                    <a href="/" class="secondary-btn">Back to map</a>
                </div>

                <?php if( ! empty($item['item_description_body']) ): ?>
                    <div class="house-description" id="house-description">
                        <div class="house-description-text" id="house-description-text">
                            <?= nl2br(htmlspecialchars($item['item_description_body'])) ?>
                        </div>
                        <button type="button" class="house-description-toggle" id="house-description-toggle" aria-expanded="false">See more</button>
                    </div>
                <?php endif; ?>
            </div>
        </section>

<script>
(() => {
    const track = document.getElementById('carousel-track');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const thumbsContainer = document.getElementById('carousel-thumbs');
    const countElement = document.getElementById('carousel-count');
    if (!track) return;

    const images = Array.from(track.querySelectorAll('.house-carousel-image'));
    const thumbs = thumbsContainer ? Array.from(thumbsContainer.querySelectorAll('.house-carousel-thumb')) : [];
    if (!images.length) return;

    let index = 0;

    const update = () => {
        track.style.transform = `translateX(-${index * 100}%)`;
        if (countElement) {
            countElement.textContent = `${index + 1} / ${images.length}`;
        }
        thumbs.forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
    };

    prevBtn.addEventListener('click', () => {
        index = (index - 1 + images.length) % images.length;
        update();
    });

    nextBtn.addEventListener('click', () => {
        index = (index + 1) % images.length;
        update();
    });

    thumbs.forEach((thumb, i) => {
        thumb.addEventListener('click', () => {
            index = i;
            update();
        });
    });

    if (images.length === 1) {
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'none';
        if (thumbsContainer) thumbsContainer.style.display = 'none';
    }

    const description = document.getElementById('house-description');
    const descriptionText = document.getElementById('house-description-text');
    const descriptionToggle = document.getElementById('house-description-toggle');

    if (description && descriptionText && descriptionToggle) {
        let expanded = false;

        const updateDescriptionToggleVisibility = () => {
            description.classList.add('expanded');
            const expandedHeight = descriptionText.scrollHeight;
            description.classList.remove('expanded');
            const collapsedHeight = descriptionText.clientHeight;
            const hasOverflow = expandedHeight > collapsedHeight + 1;

            descriptionToggle.style.display = hasOverflow ? 'inline-flex' : 'none';

            if (!hasOverflow) {
                expanded = false;
                description.classList.remove('expanded');
                descriptionToggle.textContent = 'See more';
                descriptionToggle.setAttribute('aria-expanded', 'false');
            }
        };

        requestAnimationFrame(updateDescriptionToggleVisibility);
        window.addEventListener('resize', updateDescriptionToggleVisibility);

        descriptionToggle.addEventListener('click', () => {
            expanded = !expanded;
            description.classList.toggle('expanded', expanded);
            descriptionToggle.textContent = expanded ? 'See less' : 'See more';
            descriptionToggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
        });
    }

    update();
})();
</script>

<?php require_once __DIR__.'/_footer.php'; ?>
