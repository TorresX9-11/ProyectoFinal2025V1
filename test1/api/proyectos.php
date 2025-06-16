<?php
include 'config.php';

// Configuración de cabeceras CORS y JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'] ?? '', '/'));
$id = isset($request[0]) ? intval($request[0]) : null;

function getInput() {
    return json_decode(file_get_contents("php://input"), true);
}

switch ($method) {
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT p.*, c.nombre as categoria_nombre 
                           FROM proyectos p 
                           LEFT JOIN categorias c ON p.categoria_id = c.id 
                           WHERE p.id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            echo json_encode($result->fetch_assoc());
        } else {
            $sql = "SELECT p.*, c.nombre as categoria_nombre 
                    FROM proyectos p 
                    LEFT JOIN categorias c ON p.categoria_id = c.id 
                    WHERE p.visible = TRUE 
                    ORDER BY p.created_at DESC";
            $result = $conn->query($sql);
            $out = [];
            while ($row = $result->fetch_assoc()) {
                // Convertir tecnologías de JSON a array
                $row['tecnologias'] = json_decode($row['tecnologias'], true);
                // Convertir imágenes adicionales de JSON a array
                if ($row['imagenes_adicionales']) {
                    $row['imagenes_adicionales'] = json_decode($row['imagenes_adicionales'], true);
                }
                $out[] = $row;
            }
            echo json_encode($out);
        }
        break;    case 'POST':
        $d = getInput();
        $stmt = $conn->prepare("INSERT INTO proyectos (
            titulo, descripcion, descripcion_corta, tecnologias, 
            url_demo, url_repositorio, imagen_principal, fecha_inicio, 
            fecha_fin, estado, categoria_id, usuario_id, destacado, visible
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $tecnologias = json_encode($d['tecnologias'] ?? []);
        $destacado = $d['destacado'] ?? false;
        $visible = $d['visible'] ?? true;
        $usuario_id = 1; // Por ahora usando ID fijo, luego se tomará de la sesión
        
        $stmt->bind_param("ssssssssssiibb",
            $d['titulo'],
            $d['descripcion'],
            $d['descripcion_corta'],
            $tecnologias,
            $d['url_demo'],
            $d['url_repositorio'],
            $d['imagen_principal'],
            $d['fecha_inicio'],
            $d['fecha_fin'],
            $d['estado'],
            $d['categoria_id'],
            $usuario_id,
            $destacado,
            $visible);
        $stmt->execute();
        echo json_encode(["success"=>true,"id"=>$stmt->insert_id]);
        break;

    case 'PATCH':
        $d = getInput();
        $sets = [];
        foreach ($d as $k=>$v){
            $sets[] = "$k='{$conn->real_escape_string($v)}'";
        }
        $conn->query("UPDATE proyectos SET ".implode(",",$sets)." WHERE id=$id");
        echo json_encode(["success"=>true]);
        break;

    case 'DELETE':
        $conn->query("DELETE FROM proyectos WHERE id=$id");
        echo json_encode(["success"=>true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(["error"=>"Método no permitido"]);
        break;
}
?>