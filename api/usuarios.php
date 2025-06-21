<?php
// Permite solicitudes desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Incluye la configuración y conexión a la base de datos
require_once 'config.php';

// Si la petición es GET, obtiene todos los usuarios
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
    exit;
}

// Si la petición es POST, crea un nuevo usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $username = $data['username'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    if ($username && $email && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO usuarios (username, password_hash, email) VALUES (?, ?, ?)');
        $stmt->execute([$username, $hash, $email]);
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    }
    exit;
}

// Si la petición no es GET ni POST, devuelve error
http_response_code(405);
echo json_encode(['error' => 'Método no permitido']);
