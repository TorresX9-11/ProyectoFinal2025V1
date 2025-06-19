<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $stmt = $conn->query('SELECT id, nombre FROM categorias ORDER BY nombre');
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'categorias' => $categorias]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener categorías', 'detalle' => $e->getMessage()]);
}
