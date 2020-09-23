<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = '';
$name = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;
$name = $data->name;

$table_name = 'store_category';

//check if store exists
$query = "SELECT * FROM " . $table_name . " WHERE id = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $id);
$stmt->execute();
$num = $stmt->rowCount();

if($num == 0){
    echo json_encode(array("message" => "Store does not exists."));
    exit();
}


$query = "UPDATE " . $table_name . "
                SET name = :name WHERE id = :id";

$stmt = $conn->prepare($query);

$stmt->bindParam(':name', $name);
$stmt->bindParam(':id', $id);


if($stmt->execute()){

    http_response_code(200);
    echo json_encode(array("message" => "Category Successfully updated."));
}
else{
    http_response_code(400);

    echo json_encode(array("message" => "Unable to update category."));
}
?>