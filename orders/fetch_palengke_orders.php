<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$status = 'pending';
$order_type = 'palengke';

$table_name = 'orders';

$query = "SELECT * FROM " . $table_name . " WHERE status = '".$status."' AND order_type = '".$order_type."'";

$stmt = $conn->prepare($query);

if ($stmt->execute()) {
    http_response_code(200);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);

} else {
    http_response_code(400);
    echo json_encode(array("message" => "Unable to fetch palengke orders"));
}
?>
