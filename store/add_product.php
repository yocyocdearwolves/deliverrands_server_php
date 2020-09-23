<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$store_id = '';
$cat_id = '';
$name = '';
$description = '';
$photo = '';
$available = false;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$store_id = $data->store_id;
$cat_id = $data->category_id;
$name = $data->name;
$description = $data->description;
$photo = $data->photo;

//check if store exists
$table_name = 'partner_users';
$query = "SELECT * FROM " . $table_name . " WHERE id = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $store_id);
$stmt->execute();
$num = $stmt->rowCount();

if ($num == 0) {
    echo json_encode(array("message" => "Store does not exists."));
    exit();
}

//check if catergory exists
$table_name = 'store_category';
$query = "SELECT * FROM " . $table_name . " WHERE store_id = ? AND id = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $store_id);
$stmt->bindParam(2, $cat_id);
$stmt->execute();
$num = $stmt->rowCount();

if ($num == 0) {
    echo json_encode(array("message" => "Category does not exists."));
    exit();
}

//insert store products
$table_name = 'store_products';
$query = "INSERT INTO " . $table_name . "
                SET store_id = :store_id,
                    cat_id = :cat_id,
                    name = :name,
                    description = :description,
                    photo = :photo,
                    available = :available";

$stmt = $conn->prepare($query);

$stmt->bindParam(':store_id', $store_id);
$stmt->bindParam(':cat_id', $cat_id);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':description', $description);
$stmt->bindParam(':photo', $photo);
$stmt->bindParam(':available', $available);

if ($stmt->execute()) {
    echo json_encode(array("message" => "Product Successfully updated."));
} else {
    echo json_encode(array("message" => "Unable to add product."));
}
