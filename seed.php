<?php

ini_set('memory_limit', '512M');

require_once __DIR__."/db.php";

$_db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

$sql = "TRUNCATE TABLE items";
$stmt = $_db->prepare( $sql);
$stmt->execute();


$sql = "SELECT * FROM documents";
$stmt = $_db->prepare( $sql);
$stmt->execute();
$rows = $stmt->fetchAll();

foreach($rows as $row){

    $document = json_decode($row["document_json"], true);
    $cases = $document["cases"] ?? [];
    $total_hits = $document["totalHits"] ?? 0;

    if ($cases){
        foreach($cases as $item){
            $item_pk = bin2hex(random_bytes(25));
            $coordinates = $item["coordinates"];
            $item_lat = $coordinates["lat"];
            $item_lon = $coordinates["lon"];
            $item_price = $item["priceCash"];
            $item_type = $item["addressType"];
            $city_name = $item["address"]["city"]["name"] ?? "";

            $sql = "INSERT INTO items VALUES(:item_pk, :item_lat, :item_lon, :item_price, :item_type, :item_city_name)";
            $stmt = $_db->prepare( $sql);
            $stmt->bindValue(":item_pk", $item_pk );
            $stmt->bindValue(":item_lat", $item_lat );
            $stmt->bindValue(":item_lon", $item_lon);
            $stmt->bindValue(":item_price", $item_price);
            $stmt->bindValue(":item_type", $item_type);
            $stmt->bindValue(":item_city_name", $city_name );
            $stmt->execute();
        }
    }
}

/*
##############################
@app.get("/seed")
def seed():
    try:
        db, cursor = x.db()
        q = "SELECT * FROM documents"
        cursor.execute(q)
        rows = cursor.fetchall()

        for row in rows:
            document = json.loads(row["document_json"])
            cases = document["cases"]
            total_hits = document["totalHits"]
            # # db = x.db()
            if cases:
                for item in cases:
                    # item_pk = item["address"]["addressID"]
                    pk = uuid.uuid4().hex
                    coordinates = item["coordinates"]
                    lat = coordinates["lat"]
                    lon = coordinates["lon"]
                    price = item["priceCash"]
                    _type = item["addressType"]
                    city_name = item["address"]["cityName"]
                    house_number = item.get("address", {}).get("houseNumber", "")
                    road_name = item["address"]["road"]["name"]
                    zip_code = item["address"]["zip"]["zipCode"]
                    days_listed = item["daysListed"]["days"]
                    energy_label = item.get("energyLabel", "")
                    floor_square_meters = item.get("housingArea", 0)
                    lot_square_meters = item.get("lotArea", 0)
                    monthly_expenses = item["monthlyExpense"]
                    number_of_rooms = item.get("numberOfRooms", 0)
                    price_per_meter = item.get("perAreaPrice", 0)
                    main_image_path = item.get("image", {}).get("imageSources", [{}])[0].get("url", "")
                    floor_plan_path = item.get("floorPlanImage", {}).get("imageSources", [{}])[0].get("url", "")

                    q = "INSERT INTO items VALUES(%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    cursor.execute(q, (pk, lat, lon, price, _type, city_name, house_number, road_name, zip_code,
                    days_listed, energy_label, floor_square_meters, lot_square_meters, monthly_expenses,
                    number_of_rooms, price_per_meter, main_image_path, floor_plan_path))
                    db.commit()
                    ic("done with document")
        return "seed"
    except Exception as ex:
        ic(ex)
        return "ups..."
    finally:
        if "cursor" in locals(): cursor.close()
        if "db" in locals(): db.close()
*/