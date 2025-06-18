<?php
// Manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-error.log');

// Headers para API
if (strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
}

// Zona horaria
date_default_timezone_set('America/Santiago');

// Configuración de la base de datos
$host = "localhost";
$db = "emanuel_torres_db2";

// Credenciales para el servidor de producción
// $user = "emanuel_torres";
// $pass = "emanuel_torres2025";

// Credenciales para desarrollo local (XAMPP)
$user = "root";
$pass = "";

// Crear conexión
try {
    $conn = new mysqli($host, $user, $pass, $db);

    // Verificar conexión
    if ($conn->connect_error) {
        $error_message = "Error de conexión a la base de datos: " . $conn->connect_error;
        error_log($error_message);
        
        if (strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false) {
            die(json_encode(['error' => $error_message]));
        } else {
            die("Error de conexión. Por favor, contacte al administrador.");
        }
    }

    // Establecer charset
    if (!$conn->set_charset("utf8mb4")) {
        error_log("Error cargando el conjunto de caracteres utf8mb4: " . $conn->error);
    }

} catch (Exception $e) {
    $error_message = "Error crítico en la base de datos: " . $e->getMessage();
    error_log($error_message);
    
    if (strpos($_SERVER['SCRIPT_NAME'], '/api/') !== false) {
        die(json_encode(['error' => $error_message]));
    } else {
        die("Error del sistema. Por favor, contacte al administrador.");
    }
}
?>