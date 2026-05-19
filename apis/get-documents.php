<?php

require_once __DIR__."/db.php";


$sql = "SELECT * FROM zips";
$stmt = $_db->prepare( $sql);
$stmt->execute();
$zips = $stmt->fetchAll();

echo json_encode($zips);

foreach($zips as $zip) {
    $zip_pk = $zip["zip_pk"];
    $data = file_get_contents("https://api.boligsiden.dk/search/map/cases?zipCodes=$zip_pk");
    $document_pk = bin2hex(random_bytes(25));
    $sql = "INSERT INTO documents VALUES(:document_pk, :zip_fk, :document_json)";
    $stmt = $_db->prepare( $sql);
    $stmt -> bindValue(":document_pk", $document_pk);
    $stmt -> bindValue(":zip_fk", $zip_pk);
    $stmt -> bindValue(":document_json", $data);
    $stmt->execute();
}

echo "ok";