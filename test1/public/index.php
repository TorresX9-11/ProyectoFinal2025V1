<?php
// Punto de entrada principal de la aplicación
require_once __DIR__ . '/../src/config/database.php';
require_once __DIR__ . '/../src/classes/Auth.php';
require_once __DIR__ . '/../src/classes/Utils.php';

// Iniciar sesión
session_start();

// Obtener la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$basePath = '/test1/public'; // Ajusta esto según tu configuración

// Eliminar el basePath de la solicitud
$request = str_replace($basePath, '', $request);

// Enrutamiento básico
switch ($request) {
    case '':
    case '/':
        require __DIR__ . '/index.html';
        break;
    case '/login':
        require __DIR__ . '/../src/auth/login.php';
        break;
    case '/register':
        require __DIR__ . '/../src/auth/register.php';
        break;
    case '/logout':
        require __DIR__ . '/../src/auth/logout.php';
        break;
    case '/admin':
        // Verificar autenticación
        if (!isset($_SESSION['user_id'])) {
            header('Location: /test1/public/login');
            exit;
        }
        require __DIR__ . '/../src/admin/index.php';
        break;
    default:
        // Manejar rutas de API
        if (strpos($request, '/api/') === 0) {
            header('Content-Type: application/json');
            $apiPath = __DIR__ . '/../src' . $request . '.php';
            if (file_exists($apiPath)) {
                require $apiPath;
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'API endpoint not found']);
            }
        } else {
            http_response_code(404);
            echo '404 - Página no encontrada';
        }
        break;
}
