<?php
// Permite solicitudes desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Incluye la configuración y conexión a la base de datos
require_once 'config.php';

try {
    // Obtiene todas las categorías de la base de datos
    $stmt = $conn->query('SELECT id, nombre FROM categorias ORDER BY nombre');
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Devuelve las categorías en formato JSON
    echo json_encode(['success' => true, 'categorias' => $categorias]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener categorías', 'detalle' => $e->getMessage()]);
}
