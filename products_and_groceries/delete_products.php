<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = null;
$id ='';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$id=$data->id;
$table_name = 'products';

$query = "DELETE FROM ".$table_name." WHERE id = :id";

$stmt = $conn->prepare($query);

$stmt->bindParam(':id', $id);

if($stmt->execute()){
    echo json_encode(array("message" => "Product Successfully Deleted"));
}
else{
    echo json_encode(array("message" => "Unable to delete product."));
}
?>