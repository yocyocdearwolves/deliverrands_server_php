<?php
include_once '../database.php';

header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$fullname = '';
$email = '';
$password ='';
$type='';
$conn = null;

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$fullname = $data->fullname;
$email = $data->email;
$password = $data->password;
$type = $data->type;

$table_name = 'users';

//check if email exists
$query = "SELECT * FROM " . $table_name . " WHERE email = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $email);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    echo json_encode(array("message" => "Email already exists."));
    exit();
}



$query = "INSERT INTO " . $table_name . "
                SET fullname = :fullname,
                    email = :email,
                    password = :password,
		                type = :type";

$stmt = $conn->prepare($query);

$stmt->bindParam(':fullname', $fullname);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':type', $type);

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