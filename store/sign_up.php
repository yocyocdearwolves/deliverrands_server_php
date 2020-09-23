<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$username = '';
$password = '';
$store_name ='';
$store_desc='';
$address='';
$number='';
$photo='';
$open_time='';
$close_time='';
$verified=false;
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$username = $data->username;
$password = $data->password;
$store_name =$data->store_name;
$store_desc=$data->store_desc;
$address=$data->address;
$number=$data->number;
$photo=$data->photo;
$open_time=$data->open_time;
$close_time=$data->close_time;

$table_name = 'partner_users';

//check if email exists
$query = "SELECT * FROM " . $table_name . " WHERE number = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    echo json_encode(array("message" => "Phone number already exists."));
    exit();
}



$query = "INSERT INTO " . $table_name . "
                SET username = :username,
                    password = :password,
                    store_name = :store_name,
                    store_desc = :store_desc,
                    address = :address,
                    number = :number,
                    photo = :photo,
                    open_time = :open_time,
                    close_time = :close_time,
                    verified= :verified";

$stmt = $conn->prepare($query);

$stmt->bindParam(':username', $username);
$stmt->bindParam(':password', $password);
$stmt->bindParam(':store_name', $store_name);
$stmt->bindParam(':store_desc', $store_desc);
$stmt->bindParam(':address', $address);
$stmt->bindParam(':number', $number);
$stmt->bindParam(':photo', $photo);
$stmt->bindParam(':open_time', $open_time);
$stmt->bindParam(':close_time', $close_time);
$stmt->bindParam(':verified', $verified);

$password_hash = password_hash($password, PASSWORD_BCRYPT);

$stmt->bindParam(':password', $password_hash);

if($stmt->execute()){

    http_response_code(200);
    echo json_encode(array("message" => "User was successfully registered."));
}
else{
    http_response_code(400);

    echo json_encode(array("message" => "Unable to register the user."));
}
?>