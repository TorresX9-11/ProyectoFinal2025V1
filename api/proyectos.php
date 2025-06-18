<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'config.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

try {
    $stmt = $conn->query("SELECT id, titulo, descripcion, descripcion_corta, imagen_principal FROM proyectos WHERE visible = 1 ORDER BY fecha_inicio DESC");
    $proyectos = $stmt->fetchAll();
    echo json_encode($proyectos);
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "mensaje" => $e->getMessage()
    ]);
}
?>