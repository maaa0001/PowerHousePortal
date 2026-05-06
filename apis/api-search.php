<?php

require_once __DIR__."/../db.php";

require_once __DIR__."/api-filter-items.php";

// Construct the URL parameters for clear_markers to update the browser history
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

<browser mix-function="clear_markers">
<?= json_encode($data) ?>
</browser>