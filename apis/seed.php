<?php

ini_set('memory_limit', '512M');
ini_set('max_execution_time', '600'); // 10 minutes
set_time_limit(600);

require_once __DIR__."/../db.php";

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
            $address = $item["address"];
            $item_house_number = $address["houseNumber"] ?? "";
            $item_road_name = $address["road"]["name"];
            $item_zip_code = $address["zipCode"];
            $item_city_name = $address["cityName"];
            $item_days_listed = $item["daysListed"]["days"];
            $item_main_image_path = $item["image"]["imageSources"][0]["url"] ?? "";
            $item_floor_plan_path = $item["floorPlanImage"]["imageSources"][0]["url"] ?? "";
            $item_energy_label = $item["energyLabel"] ?? "0";
            $item_price = $item["priceCash"];
            $item_type = $item["addressType"];
            $item_number_of_rooms = $item["numberOfRooms"] ?? "";
            $item_floor_square_meters = $item["housingArea"] ?? 0;
            $item_area_square_meters = $item["lotArea"] ?? 0;
            $item_monthly_expenses = $item["monthlyExpense"] ?? "";
            $item_price_per_meter = $item["perAreaPrice"] ?? 0;
            $item_year_built = $item["yearBuilt"] ?? 0;

            // Fetch extra images from case detail API
            $item_images_json = json_encode([]);
            if (isset($item["caseID"])) {
                $case_id = $item["caseID"];
                $case_detail_url = "https://api.boligsiden.dk/cases/" . $case_id;
                $case_detail_json = @file_get_contents($case_detail_url);
                if ($case_detail_json) {
                    $case_detail = json_decode($case_detail_json, true);
                    $images = [];
                    if (isset($case_detail["images"])) {
                        foreach ($case_detail["images"] as $img_obj) {
                            if (isset($img_obj["imageSources"])) {
                                // Prefer 600x600 or 600x400
                                $found_url = "";
                                foreach ($img_obj["imageSources"] as $source) {
                                    if ($source["size"]["width"] == 600) {
                                        $found_url = $source["url"];
                                        break;
                                    }
                                }
                                if (!$found_url && !empty($img_obj["imageSources"])) {
                                    $found_url = $img_obj["imageSources"][0]["url"];
                                }
                                if ($found_url) {
                                    $images[] = $found_url;
                                }
                            }
                        }
                    }
                    $item_images_json = json_encode($images);
                }
            }


            $sql = "INSERT INTO items (item_pk, item_lat, item_lon, item_price, item_type, item_city_name, item_house_number, item_road_name, item_zip_code, item_days_listed, item_energy_label, item_floor_square_meters, item_area_square_meters, item_number_of_rooms, item_floor_plan_path, item_main_image_path, item_monthly_expenses, item_price_per_meter, item_year_built, item_images_json) VALUES(:item_pk, :item_lat, :item_lon, :item_price, :item_type, :item_city_name, :item_house_number, :item_road_name, :item_zip_code, :item_days_listed, :item_energy_label, :item_floor_square_meters, :item_area_square_meters, :item_number_of_rooms, :item_floor_plan_path, :item_main_image_path, :item_monthly_expenses, :item_price_per_meter, :item_year_built, :item_images_json)";
            $stmt = $_db->prepare( $sql);
            $stmt->bindValue(":item_pk", $item_pk );
            $stmt->bindValue(":item_lat", $item_lat );
            $stmt->bindValue(":item_lon", $item_lon);
            $stmt->bindValue(":item_price", $item_price);
            $stmt->bindValue(":item_type", $item_type);
            $stmt->bindValue(":item_city_name", $item_city_name );
            $stmt->bindValue(":item_road_name", $item_road_name);
            $stmt->bindValue(":item_house_number", $item_house_number);
            $stmt->bindValue(":item_zip_code", $item_zip_code);
            $stmt->bindValue(":item_days_listed", $item_days_listed);
            $stmt->bindValue(":item_energy_label", $item_energy_label);
            $stmt->bindValue(":item_floor_plan_path", $item_floor_plan_path);
            $stmt->bindValue(":item_main_image_path", $item_main_image_path);
            $stmt->bindValue(":item_number_of_rooms", $item_number_of_rooms);
            $stmt->bindValue(":item_floor_square_meters", $item_floor_square_meters);
            $stmt->bindValue(":item_area_square_meters", $item_area_square_meters);
            $stmt->bindValue(":item_monthly_expenses", $item_monthly_expenses);
            $stmt->bindValue(":item_price_per_meter", $item_price_per_meter);
            $stmt->bindValue(":item_year_built", $item_year_built);
            $stmt->bindValue(":item_images_json", $item_images_json);
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