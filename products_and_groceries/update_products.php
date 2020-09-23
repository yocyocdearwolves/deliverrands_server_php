<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$id = '';
$name = '';
$unit = '';
$place ='';
$description='';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$id = $data->id;
$name = $data->name;
$unit = $data->unit;
$place = $data->place;
$description= $data->description;

$table_name = 'products';

$query = "UPDATE " . $table_name . "
                SET name = :name,
                    unit = :unit,
                    place = :place,
                    description = :description
                    WHERE id =:id";

$stmt = $conn->prepare($query);

$stmt->bindParam(':id', $id);
$stmt->bindParam(':name', $name);
$stmt->bindParam(':unit', $unit);
$stmt->bindParam(':place', $place);
$stmt->bindParam(':description', $description);

if($stmt->execute()){
    http_response_code(200);
    echo json_encode(array("message" => "Product Updated Successfully"));
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Unable to Update Product."));
}
?>