<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$host = 'localhost';
$db = 'emanuel_torres_db2';
$user = 'emanuel_torres'; // Cambia si tu hosting te dio otro usuario
$pass = 'emanuel_torres2025'; // Cambia si tu hosting te dio otra contraseña
try {
    $conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
