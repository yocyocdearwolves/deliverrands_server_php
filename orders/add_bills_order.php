<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$conn = null;
$customer_id = '';
$address = '';
$contact = '';
$notes = '';
$order_type = '';
$status = 'pending';
$pay_at = '';
$account_number = ''; 

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$customer_id = $data->customer_id;
$address = $data->address;
$contact = $data->contact;
$notes = $data->notes;
$order_type = $data->order_type;
$pay_at =$data->pay_at;
$account_number = $data->account_number;

$table_name = 'orders';

$query = "INSERT INTO " . $table_name . " SET customer_id = :customer_id, address = :address,contact= :contact, notes = :notes, status= :status, order_type = :order_type";

$stmt = $conn->prepare($query);

$stmt->bindParam(':customer_id', $customer_id);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':contact', $contact);
$stmt->bindParam(':notes', $notes);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':order_type', $order_type);

if ($stmt->execute()) {
    $order_id = $conn->lastInsertId();

    insertOrders($conn,$order_id,$pay_at,$account_number);
} else {
    echo json_encode(array("message" => "Unable to process errands order."));
}

function insertOrders($connect,$order_id,$pay_at,$account_number)
{
        $sql = "INSERT INTO bill_orders SET order_id = :order_id, pay_at= :pay_at, account_number= :account_number";
        $stmt = $connect->prepare($sql);
        
        $stmt->bindParam(':order_id', $order_id);
        $stmt->bindParam(':pay_at', $pay_at);
        $stmt->bindParam(':account_number', $account_number);

        
        if ($stmt->execute()) {
            echo json_encode(array("message" => "Errand order Successfully Added", "order_id" => $order_id));
        } else {
            echo json_encode(array("message" => "Unable to process errand orders."));
            die('execute() failed: ' . htmlspecialchars($stmt2->error));
        }
}
