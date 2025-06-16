<?php
include 'config.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$id = isset($request[0]) ? intval($request[0]) : null;

function getInput() {
    return json_decode(file_get_contents("php://input"), true);
}

switch ($method) {
    case 'GET':
        if ($id) {
            $res = $conn->query("SELECT * FROM proyectos WHERE id=$id");
            echo json_encode($res->fetch_assoc());
        } else {
            $res = $conn->query("SELECT * FROM proyectos ORDER BY created_at DESC");
            $out = [];
            while ($row = $res->fetch_assoc()) {
                $out[] = $row;
            }
            echo json_encode($out);
        }
        break;

    case 'POST':
        $d = getInput();
        $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, url_github, url_produccion, imagen) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss",$d['titulo'],$d['descripcion'],$d['url_github'],$d['url_produccion'],$d['imagen']);
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