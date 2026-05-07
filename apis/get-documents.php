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

// $data = file_get_contents("https://api.boligsiden.dk/search/map/cases?zipCodes=2100");
// echo $data;


/*
##############################
@app.get("/get-documents")
def get_documents():
    try:
        db, cursor = x.db()
        q = "SELECT * FROM zips"
        cursor.execute(q)
        zips = cursor.fetchall()
        for zip in zips:
            # ic(zip["zip_pk"])
            url = f"https://api.boligsiden.dk/search/map/cases?zipCodes={zip['zip_pk']}"
            # ic(url)
            document = requests.get(url).text
            # document = json.loads(data)
            document_pk = uuid.uuid4().hex
            zip_fk = zip['zip_pk']
            # ic(document)
            if document:
                q = "INSERT INTO documents VALUES(%s,%s,%s)"
                cursor.execute(q, (document_pk, zip_fk, document))
                db.commit()
                ic(f"done with zip {zip_fk}")
        return "documents retrieved"
    except Exception as ex:
        ic(ex)
        return "ups..."
    finally:
        if "cursor" in locals(): cursor.close()
        if "db" in locals(): db.close()
*/
echo "ok";