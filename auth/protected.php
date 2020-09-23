<?php

ini_set("display_errors", 1);

include_once '../database.php';
require "../vendor/autoload.php";
use \Firebase\JWT\JWT;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$secret_key = "YOUR_SECRET_KEY";
$databaseService = new DatabaseService();
$conn = $databaseService->getConnection();

$data = json_decode(file_get_contents("php://input"));


//$authHeader = $_SERVER['HTTP_AUTHORIZATION'];

//$arr = explode(" ", $authHeader);


/*echo json_encode(array(
    "message" => "sd" .$arr[1]
));*/

$jwt = $data->jwt;

echo $jwt;

if($jwt){

    try {

        $decoded = JWT::decode($jwt, $secret_key, array('HS512'));

        // Access is granted. Add code of the operation here 

	
        echo json_encode(array(
            "message" => "Access granted:",
            //"error" => $e->getMessage()
        ));

    }catch (Exception $e){

    http_response_code(500);
    echo json_encode(array(
        "message" => "Access denied.",
        "error" => $e->getMessage()
    ));
}

}
?>