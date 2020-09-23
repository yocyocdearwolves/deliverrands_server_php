<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = null;
$text = '';
$place = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$text = $data->text;
$place = $data->place;

$table_name = 'products';

$query = "SELECT * FROM " . $table_name . " WHERE name like '%" . $text . "%' AND place = '".$place."'";

$stmt = $conn->prepare($query);

if ($stmt->execute()) {
    http_response_code(200);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);

} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to search product"));
}
