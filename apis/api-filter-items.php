<?php

$params = [];
$sql = "SELECT * FROM items WHERE 1=1";

if (!empty($_GET['item_type'])) {
    $sql .= " AND item_type = :item_type";
    $params[':item_type'] = $_GET['item_type'];
}

if (!empty($_GET['min_price'])) {
    $sql .= " AND item_price >= :min_price";
    $params[':min_price'] = $_GET['min_price'];
}

if (!empty($_GET['max_price'])) {
    $sql .= " AND item_price <= :max_price";
    $params[':max_price'] = $_GET['max_price'];
}

if (!empty($_GET['cities'])) {
    $cities = is_array($_GET['cities']) ? $_GET['cities'] : explode(',', $_GET['cities']);
    $placeholders = [];
    foreach ($cities as $i => $city) {
        $placeholder = ":city_$i";
        $placeholders[] = $placeholder;
        $params[$placeholder] = $city;
    }
    $sql .= " AND item_city_name IN (" . implode(',', $placeholders) . ")";
}

if (!empty($_GET['min_area'])) {
    $sql .= " AND CAST(item_floor_square_meters AS UNSIGNED) >= :min_area";
    $params[':min_area'] = $_GET['min_area'];
}

if (!empty($_GET['max_area'])) {
    $sql .= " AND CAST(item_floor_square_meters AS UNSIGNED) <= :max_area";
    $params[':max_area'] = $_GET['max_area'];
}

if (!empty($_GET['min_rooms'])) {
    $sql .= " AND CAST(item_number_of_rooms AS UNSIGNED) >= :min_rooms";
    $params[':min_rooms'] = $_GET['min_rooms'];
}

if (!empty($_GET['max_rooms'])) {
    $sql .= " AND CAST(item_number_of_rooms AS UNSIGNED) <= :max_rooms";
    $params[':max_rooms'] = $_GET['max_rooms'];
}

$stmt = $_db->prepare($sql);
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->execute();
$items = $stmt->fetchAll();
