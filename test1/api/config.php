<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");

// //conexion a servidor MySQL
// $host = "localhost";
// $db = "emanuel_torres_db2";
// $user = "emanuel_torres";
// $pass = "emanuel_torres2025";

$host = "localhost";
$db = "emanuel_torres_db2"; 
$user = "root";
$pass = "";


$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die(json_encode(["error" => "Conexión fallida"]));
}
?>