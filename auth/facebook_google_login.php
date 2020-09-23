<?php
include_once '../database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$email = '';
$fullname = '';
$type = '';

$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));

$email = $data->email;
$fullname = $data->fullname;
$type = $data->type;

$table_name = 'users';

if (empty($email) || empty($fullname) || empty($type)) {
    echo json_encode(array("message" => "All fields are required"));
} else {
    $query = "SELECT * FROM " . $table_name . " WHERE email = ? AND type = ? LIMIT 0,1";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(1, $email);
    $stmt->bindParam(2, $type);
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $id = $row['id'];
        $fullname = $row['fullname'];
        $address = $row['address'];
        $contact = $row['contact'];
        $type = $row['type'];
        generateToken($id, $email, $fullname, $address, $contact, $type);
    } else {
        $query = "INSERT INTO " . $table_name . "
    SET fullname = :fullname,
        email = :email,
            type = :type";

        $stmt = $conn->prepare($query);

        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':type', $type);
        if ($stmt->execute()) {
            http_response_code(200);
            $query = "SELECT * FROM " . $table_name . " WHERE email = ? AND type = ? LIMIT 0,1";

            $stmt = $conn->prepare($query);
            $stmt->bindParam(1, $email);
            $stmt->bindParam(2, $type);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $id = $row['id'];
            $fullname = $row['fullname'];
            $address = $row['address'];
            $contact = $row['contact'];
            $type = $row['type'];

            generateToken($id, $email, $fullname, $address, $contact, $type);
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Unable to login the user."));
            exit(1);
        }
    }
}

function generateToken($id, $email, $fullname, $address, $contact, $type)
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
            "fullname" => $fullname,
            "email" => $email,
        ));

    http_response_code(200);

    $jwt = JWT::encode($token, $secret_key, 'HS512');
    echo json_encode(
        array(
            "message" => "Successful login.",
            "jwt" => $jwt,
            "id" => $id,
            "email" => $email,
            "fullname" => $fullname,
            "address" => $address,
            "contact" => $contact,
            "type" => $type,
            "expireAt" => $expire_claim,
        ));
}
