<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Cambia esto en producción
define('DB_PASS', '');      // Cambia esto en producción
define('DB_NAME', 'portafolio');

// Crear conexión
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8mb4");

// Zona horaria
date_default_timezone_set('America/Santiago');

// Configuración global
ini_set('display_errors', 0);
error_reporting(E_ALL);

// En desarrollo, puedes descomentar estas líneas
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
