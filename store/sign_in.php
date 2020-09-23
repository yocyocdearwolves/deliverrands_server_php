<?php
include_once '../database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$username = '';
$password = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();



$data = json_decode(file_get_contents("php://input"));

$username = $data->username;
$password = $data->password;

$table_name = 'partner_users';

$query = "SELECT * FROM " . $table_name . " WHERE username = ? LIMIT 0,1";

$stmt = $conn->prepare( $query );
$stmt->bindParam(1, $username);
$stmt->execute();
$num = $stmt->rowCount();

if($num > 0){
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $id = $row['id'];
    $password2 = $row['password'];
    $store_name = $row['store_name'];
    $store_desc = $row['store_desc'];
    $address = $row['address'];
    $number = $row['number'];
    $photo = $row['photo'];
    $open_time = $row['open_time'];
    $close_time = $row['close_time'];
    $verified = $row['verified'];

    if(password_verify($password, $password2))
    {
        $secret_key = "YOUR_SECRET_KEY";
        $issuer_claim = "18.216.125.206"; // this can be the servername
        $audience_claim = "THE_AUDIENCE";
        $issuedat_claim = time(); // issued at
        $notbefore_claim = $issuedat_claim + 10; //not before in seconds
        $expire_claim = $issuedat_claim + 3600; // expire time in seconds
        $token = array(
            "iss" => $issuer_claim,
            "aud" => $audience_claim,
            "iat" => $issuedat_claim,
            "nbf" => $notbefore_claim,
            "exp" => $expire_claim,
            "data" => array(
                "id" => $id,
                "username" => $username
        ));

        http_response_code(200);

        $jwt = JWT::encode($token, $secret_key, 'HS512');
        echo json_encode(
            array(
                "message" => "Successful login.",
                "jwt" => $jwt,
                "username" => $username,
                "id" => $id,
                "store_name" => $store_name,
                "store_desc" => $store_desc,
                "address" => $address,
                "number" => $number,
                "photo" => $photo,
                "open_time" => $open_time,
                "close_time" => $close_time,
                "expireAt" => $expire_claim
            ));
    }
    else{
        echo json_encode(array("message" => "Login failed."));
    }
}
?>