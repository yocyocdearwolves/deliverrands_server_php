<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$store_id;
$product_id = '';
$title = '';
$isrequired = '';
$ismultiple = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$store_id = $data->store_id;
$product_id = $data->product_id;
$title = $data->title;
$isrequired = $data->isrequired;
$ismultiple = $data->ismultiple;

$add_ons = json_decode(json_encode($data->add_ons), true);

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

//check if products exists
$table_name = 'store_products';
$query = "SELECT * FROM " . $table_name . " WHERE store_id = ? AND id = ? LIMIT 0,1";

$stmt = $conn->prepare($query);
$stmt->bindParam(1, $store_id);
$stmt->bindParam(2, $product_id);
$stmt->execute();
$num = $stmt->rowCount();

if ($num == 0) {
    echo json_encode(array("message" => "Product does not exists."));
    exit();
}

//insert store products
$table_name = 'addons';
$query = "INSERT INTO " . $table_name . "
                SET product_id = :product_id,
                    title = :title,
                    isrequired = :isrequired,
                    ismultiple = :ismultiple";

$stmt = $conn->prepare($query);

$stmt->bindParam(':product_id', $product_id);
$stmt->bindParam(':title', $title);
$stmt->bindParam(':isrequired', $isrequired);
$stmt->bindParam(':ismultiple', $ismultiple);

if ($stmt->execute()) {
    $addon_id = $conn->lastInsertId();

    insertAddons($add_ons, $conn, $addon_id);

} else {
    echo json_encode(array("error" => $stmt->errorInfo(), "message" => "Unable to add addon."));
}

function insertAddons($array, $connect, $addon_id)
{
    if (is_array($array)) {
        $values = array();
        foreach ($array as $row => $value) {
            $name = mysqli_real_escape_string($connect, $value['name']);
            $description = mysqli_real_escape_string($connect, $value['description']);
            $price = mysqli_real_escape_string($connect, $value['price']);
            $values[] = "('" . $addon_id . "', '" . $value['name'] . "', '" . $value['decription'] . "','" . $value['price'] . "')";
        }
        $sql = "INSERT INTO addon_details(addon_id, name, description, price) VALUES";
        $sql .= implode(', ', $values);
        // mysqli_query($connect, $sql);

        $stmt2 = $connect->prepare($sql);
        if ($stmt2->execute()) {
            echo json_encode(array("message" => "Addons Successfully Added", "addon_id" => $addon_id));
        } else {
            echo json_encode(array("message" => "Unable to add addons."));
        }
    } else {
        echo json_encode(array("message" => "An error occured."));
    }
}
