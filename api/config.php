<?php
// Configuración de cabeceras para API REST solo si es acceso directo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
}

// Configuración de la conexión a la base de datos
$host = 'localhost'; // Host de la base de datos
$db   = 'emanuel_torres_db2'; // Nombre de la base de datos
// $user = 'root'; // Usuario de la base de datos (para desarrollo local)
// $pass = ''; // Contraseña de la base de datos (para desarrollo local)

// Usuario y contraseña para producción (comentar las credenciales locales y descomentar estas líneas)
$user = 'emanuel_torres';
$pass = 'emanuel_torres2025';

$charset = 'utf8mb4'; // Codificación de caracteres

// DSN (Data Source Name) para PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Modo de errores: excepciones
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Modo de obtención por defecto: array asociativo
    PDO::ATTR_EMULATE_PREPARES   => false,                 // Desactiva la emulación de sentencias preparadas
];

try {
    // Crea la conexión PDO
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Si hay error, muestra mensaje y detiene la ejecución
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Habilitar logs de errores
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../php-error.log');

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
