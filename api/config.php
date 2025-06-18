<?php
// Configuración de cabeceras para API REST
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Configuración de la base de datos
$host = "localhost";
$db = "emanuel_torres_db2";
// $user = "root";         // Para desarrollo local
// $pass = "";            // Para desarrollo local

// Para producción (comentar las credenciales locales y descomentar estas)
$user = "emanuel_torres";
$pass = "emanuel_torres2025";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Error de conexión a la base de datos']));
}

// Función para verificar autenticación
function verificarAutenticacion() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        die(json_encode(['error' => 'No autorizado']));
    }
    return $_SESSION['user_id'];
}

// Función para respuesta JSON
function responderJSON($data, $codigo = 200) {
    http_response_code($codigo);
    echo json_encode($data);
    exit;
}

// Función para obtener datos del body
function obtenerDatosJSON() {
    return json_decode(file_get_contents('php://input'), true);
}
