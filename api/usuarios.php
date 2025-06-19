<?php
require_once 'config.php';
header('Content-Type: application/json');

try {
    // Intentar obtener username y email
    $stmt = $conn->query('SELECT id, username, email FROM usuarios ORDER BY username');
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    // Si no existe username, usar email como nombre
    foreach ($usuarios as &$u) {
        if (empty($u['username'])) {
            $u['nombre'] = $u['email'];
        } else {
            $u['nombre'] = $u['username'];
        }
    }
    echo json_encode(['success' => true, 'usuarios' => $usuarios]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error al obtener usuarios', 'detalle' => $e->getMessage()]);
}
