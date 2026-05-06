<?php
require_once __DIR__.'/../db.php';

$city = $_GET['city'] ?? '';

if (strlen($city) < 2) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT DISTINCT item_city_name FROM items WHERE item_city_name LIKE :city LIMIT 10";
$stmt = $_db->prepare($sql);
$stmt->bindValue(':city', "%$city%");
$stmt->execute();
$cities = $stmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($cities);
