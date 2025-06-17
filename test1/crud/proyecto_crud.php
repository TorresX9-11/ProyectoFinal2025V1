<?php
session_start();
require_once '../classes/Auth.php';
require_once '../api/config.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Obtener proyectos del usuario actual
function getProyectos($conn, $user_id) {
    $stmt = $conn->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM proyectos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.usuario_id = ? 
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $proyectos = [];
    while ($row = $result->fetch_assoc()) {
        $row['tecnologias'] = json_decode($row['tecnologias'], true);
        if ($row['imagenes_adicionales']) {
            $row['imagenes_adicionales'] = json_decode($row['imagenes_adicionales'], true);
        }
        $proyectos[] = $row;
    }
    return $proyectos;
}

// Obtener categorías para el formulario
function getCategorias($conn) {
    $result = $conn->query("SELECT id, nombre FROM categorias ORDER BY nombre");
    return $result->fetch_all(MYSQLI_ASSOC);
}

$proyectos = getProyectos($conn, $user_id);
$categorias = getCategorias($conn);

// Manejar errores de la base de datos
if ($conn->error) {
    $error = "Error en la base de datos: " . $conn->error;
}

?>
