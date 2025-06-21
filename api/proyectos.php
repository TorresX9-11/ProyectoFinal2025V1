<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

// Permite solicitudes desde cualquier origen (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");

$method = $_SERVER['REQUEST_METHOD'];

// Obtener ID si viene en la URL tipo /proyectos.php/1
$id = null;
if (isset($_SERVER['PATH_INFO'])) {
    $parts = explode('/', trim($_SERVER['PATH_INFO'], '/'));
    if (isset($parts[0]) && is_numeric($parts[0])) {
        $id = intval($parts[0]);
    }
}

function getInput() {
    return json_decode(file_get_contents('php://input'), true);
}

// GET: público
if ($method === 'GET') {
    try {
        // Filtros por categoría y usuario
        $categoria_id = isset($_GET['categoria_id']) ? intval($_GET['categoria_id']) : null;
        $usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : null;

        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM proyectos WHERE id = ? AND visible = 1");
            $stmt->execute([$id]);
            $proyecto = $stmt->fetch();
            if ($proyecto) {
                echo json_encode($proyecto);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "Proyecto no encontrado"]);
            }
        } else {
            $query = "SELECT p.id, p.titulo, p.descripcion, p.descripcion_corta, p.imagen_principal, p.visible, p.destacado, p.tecnologias, p.url_demo, p.url_repositorio, p.fecha_inicio, p.fecha_fin, p.estado, p.categoria_id, u.username as autor FROM proyectos p JOIN usuarios u ON p.usuario_id = u.id WHERE p.visible = 1";
            $params = [];
            if ($categoria_id) {
                $query .= " AND p.categoria_id = ?";
                $params[] = $categoria_id;
            }
            if ($usuario_id) {
                $query .= " AND p.usuario_id = ?";
                $params[] = $usuario_id;
            }
            $query .= " ORDER BY p.destacado DESC, p.fecha_inicio DESC";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $proyectos = $stmt->fetchAll();
            echo json_encode($proyectos);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// Métodos protegidos: POST, PUT, DELETE
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

if ($method === 'POST') {
    $data = $_POST;
    // Si viene JSON puro
    if (empty($data)) {
        $data = getInput();
    }
    if (!isset($data['titulo'], $data['descripcion'])) {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        exit;
    }
    try {
        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, descripcion_corta, imagen_principal, fecha_inicio, visible, usuario_id) VALUES (?, ?, ?, ?, ?, 1, ?)");
        $stmt->execute([
            $data['titulo'],
            $data['descripcion'],
            $data['descripcion_corta'] ?? null,
            $data['imagen_principal'] ?? null,
            $data['fecha_inicio'] ?? date('Y-m-d'),
            $_SESSION['user_id']
        ]);
        echo json_encode(["message" => "Proyecto creado", "id" => $conn->lastInsertId()]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

if ($method === 'PUT') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "ID no proporcionado"]);
        exit;
    }
    $data = getInput();
    $campos = [];
    $valores = [];
    foreach (["titulo", "descripcion", "descripcion_corta", "imagen_principal", "fecha_inicio", "visible"] as $campo) {
        if (isset($data[$campo])) {
            $campos[] = "$campo = ?";
            $valores[] = $data[$campo];
        }
    }
    if (empty($campos)) {
        http_response_code(400);
        echo json_encode(["error" => "No hay datos para actualizar"]);
        exit;
    }
    $valores[] = $id;
    try {
        $sql = "UPDATE proyectos SET ".implode(", ", $campos)." WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute($valores);
        echo json_encode(["message" => "Proyecto actualizado"]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

if ($method === 'DELETE') {
    if (!$id) {
        http_response_code(400);
        echo json_encode(["error" => "ID no proporcionado"]);
        exit;
    }
    try {
        $stmt = $conn->prepare("DELETE FROM proyectos WHERE id = ?");
        $stmt->execute([$id]);
        if ($stmt->rowCount() > 0) {
            echo json_encode(["message" => "Proyecto eliminado"]);
        } else {
            http_response_code(404);
            echo json_encode(["error" => "Proyecto no encontrado"]);
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error" => $e->getMessage()]);
    }
    exit;
}

// Si llega aquí, método no permitido
http_response_code(405);
echo json_encode(["error" => "Método no permitido"]);
exit;
?>