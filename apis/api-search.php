<?php

require_once __DIR__."/../db.php";


$rooms = $_GET['rooms'];

$sql = "SELECT * FROM items WHERE number_of_rooms = :rooms"; 
$stmt = $_db->prepare( $sql);
$stmt->bindValue(':rooms', $rooms);
$stmt->execute();
$items = $stmt->fetchAll();

$data = ["url"=>
[
    ["rooms"=>$rooms],
    ["baths"=>1]
], 
"items"=>$items
];
?>

<browser mix-function="clear_markers">
<?= json_encode($data) ?>
</browser>