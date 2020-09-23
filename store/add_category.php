<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$store_id = '';
$name = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$store_id = $data->store_id;
$name = $data->name;

$table_name = 'partner_users';

//check if store exists
$query = "SELECT * FROM " . $table_name . " WHERE id = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $store_id);
$stmt->execute();
$num = $stmt->rowCount();

if($num == 0){
    echo json_encode(array("message" => "Store does not exists."));
    exit();
}

$table_name = 'store_category';

//check if name exists
$query = "SELECT * FROM " . $table_name . " WHERE store_id = ? AND name = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $store_id);
$stmt->bindParam(2, $name);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    echo json_encode(array("message" => "Category Name already exists."));
    exit();
}

$query = "INSERT INTO " . $table_name . "
                SET store_id = :store_id,
                    name = :name";

$stmt = $conn->prepare($query);

$stmt->bindParam(':store_id', $store_id);
$stmt->bindParam(':name', $name);



if($stmt->execute()){

    http_response_code(200);
    echo json_encode(array("message" => "Category Successfully added."));
}
else{
    http_response_code(400);

    echo json_encode(array("message" => "Unable to add category."));
}
?>