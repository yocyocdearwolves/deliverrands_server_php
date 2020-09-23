<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");



$data = json_decode(file_get_contents("php://input"));
$id = $_GET['id'];

echo $id;

$url = "https://endurotrail-d806b.firebaseio.com/Users/$id.json";
$val = array('verified' => 1);

$options = array(
   'http' => array(
     'method'  => 'PATCH',
     'content' => json_encode( $val ),
     'header'=>  "Content-Type: application/json\r\n" .
                 "Accept: application/json\r\n"
     )
 );
 
 $context  = stream_context_create( $options );
 $result = file_get_contents( $url, false, $context );
 $response = json_decode( $result );
?>