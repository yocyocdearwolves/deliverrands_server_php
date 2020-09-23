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

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$customer_id = $data->customer_id;
$address = $data->address;
$contact = $data->contact;
$notes = $data->notes;
$order_type = $data->order_type;

$orders = json_decode(json_encode($data->orders),true);
//print_r($orders);


$table_name = 'orders';

$query = "INSERT INTO " . $table_name . " SET customer_id =:customer_id, address =:address,contact=:contact, notes =:notes, status=:status, order_type =:order_type";

$stmt = $conn->prepare($query);

$stmt->bindParam(':customer_id', $customer_id);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':contact', $contact);
$stmt->bindParam(':notes', $notes);
$stmt->bindParam(':status', $status);
$stmt->bindParam(':order_type', $order_type);

if ($stmt->execute()) {
    $order_id = $conn->lastInsertId();

    insertOrders($orders, $conn, $order_id);
} else {
    echo json_encode(array("message" => "Unable to process order."));
}

function insertOrders($array, $connect, $order_id)
{
    if (is_array($array)) {
        $values = array();
        foreach ($array as $row => $value) {
            $product_name = mysqli_real_escape_string($connect, $value['product_name']);
            $description = mysqli_real_escape_string($connect, $value['description']);
            $unit = mysqli_real_escape_string($connect, $value['unit']);
            $amount = mysqli_real_escape_string($connect, $value['amount']);
            $values[] = "('".$order_id."', '".$value['product_name']."', '".$value['description']."','".$value['unit']."','".$value['amount']."')";
        }
        $sql = "INSERT INTO palengke_grocery_orders(order_id, product_name, description, unit, amount) VALUES";
        $sql .= implode(', ', $values);
        // mysqli_query($connect, $sql);

        $stmt2 = $connect->prepare($sql);
        if ($stmt2->execute()) {
            echo json_encode(array("message" => "Order Successfully Added", "order_id" => $order_id));
        } else {
            echo json_encode(array("message" => "Unable to process orders."));
            die('execute() failed: ' . htmlspecialchars($stmt2->error));
        }
    }else{
        echo json_encode(array("message" => "An error occured."));
    }
}
