<?php
include 'config.php';
require_once 'auth_helper.php';

// Verificar autenticación para operaciones de escritura
$user_id = requireAuth($_SERVER['REQUEST_METHOD']);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = isset($request[0]) ? intval($request[0]) : null;

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM categorias WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            echo json_encode($stmt->get_result()->fetch_assoc());
        } else {
            $result = $conn->query("SELECT * FROM categorias ORDER BY nombre");
            $categorias = [];
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
            echo json_encode($categorias);
        }
        break;
        
    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input['nombre'])) {
            http_response_code(400);
            echo json_encode(["error" => "El nombre de la categoría es requerido"]);
            break;
        }
          // Verificar si ya existe una categoría con el mismo nombre
        $checkStmt = $conn->prepare("SELECT id FROM categorias WHERE nombre = ?");
        $checkStmt->bind_param("s", $input['nombre']);
        $checkStmt->execute();
        if ($checkStmt->get_result()->num_rows > 0) {
            http_response_code(409);
            echo json_encode(["error" => "Ya existe una categoría con este nombre"]);
            break;
        }

        $stmt = $conn->prepare("INSERT INTO categorias (nombre, descripcion) VALUES (?, ?)");
        $stmt->bind_param("ss", $input['nombre'], $input['descripcion'] ?? '');
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "id" => $stmt->insert_id,
                "message" => "Categoría creada exitosamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Error al crear la categoría: " . $stmt->error
            ]);
        }
        break;

    case 'PATCH':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID no proporcionado"]);
            break;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        if (empty($input)) {
            http_response_code(400);
            echo json_encode(["error" => "No hay datos para actualizar"]);
            break;
        }
        
        $sets = [];
        $values = [];
        $types = "";
        
        if (isset($input['nombre'])) {
            $sets[] = "nombre=?";
            $values[] = $input['nombre'];
            $types .= "s";
        }
        
        if (isset($input['descripcion'])) {
            $sets[] = "descripcion=?";
            $values[] = $input['descripcion'];
            $types .= "s";
        }
        
        if (empty($sets)) {
            http_response_code(400);
            echo json_encode(["error" => "No hay campos válidos para actualizar"]);
            break;
        }
        
        $values[] = $id;
        $types .= "i";
        
        $sql = "UPDATE categorias SET " . implode(",", $sets) . " WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Categoría actualizada exitosamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Error al actualizar la categoría: " . $stmt->error
            ]);
        }
        break;

    case 'DELETE':
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "ID no proporcionado"]);
            break;
        }
        
        // Verificar si hay proyectos usando esta categoría
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM proyectos WHERE categoria_id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        if ($result['count'] > 0) {
            http_response_code(400);
            echo json_encode([
                "success" => false,
                "error" => "No se puede eliminar la categoría porque está siendo usada por proyectos"
            ]);
            break;
        }
        
        $stmt = $conn->prepare("DELETE FROM categorias WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Categoría eliminada exitosamente"
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                "success" => false,
                "error" => "Error al eliminar la categoría: " . $stmt->error
            ]);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Método no permitido"]);
        break;
}
