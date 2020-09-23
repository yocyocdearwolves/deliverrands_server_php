<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = null;
$name = '';
$unit = '';
$place ='';
$description='';


$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$name = $data->name;
$unit = $data->unit;
$place = $data->place;
$description= $data->description;

$table_name = 'products';

$query = "INSERT INTO " . $table_name . "
                SET name = :name,
                    unit = :unit,
                    place = :place,
		    description = :description";

$stmt = $conn->prepare($query);

$stmt->bindParam(':name', $name);
$stmt->bindParam(':unit', $unit);
$stmt->bindParam(':place', $place);
$stmt->bindParam(':description', $description);

if($stmt->execute()){
    echo json_encode(array("message" => "Product Successfully Added"));
}
else{
    echo json_encode(array("message" => "Unable to add product."));
}
?>