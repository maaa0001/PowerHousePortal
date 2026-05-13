<?php

require_once __DIR__."/../db.php";
require_once __DIR__."/api-filter-items.php";

$url_props = [];

foreach ($_GET as $key => $value) {
    if (!empty($value)) {
        $url_props[] = [$key => $value];
    }
}

$data = [
    "url" => $url_props,
    "items" => $items
];

?>

<browser mix-function="update_map_items">
<?= json_encode($data) ?>
</browser>