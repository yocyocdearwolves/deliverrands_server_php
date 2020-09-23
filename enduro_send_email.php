<?php
include_once 'mailer.php';

mysqli_set_charset($conn, "utf8");
$json = file_get_contents('php://input');
$obj = json_decode($json, true);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $obj['email'];
    $id = $obj['id'];

    $mailer = new Mailer();
    $mailer->sendMail($email, $id);
    $item = array("error" => false, "message" => "Email Sent");
    $json = json_encode($item);

} else {
    die("Something went wrong");
}

echo $json;


?>