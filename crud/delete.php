<?php
// Inicia la sesión para acceder a variables de sesión
session_start();
// Incluye la configuración y conexión a la base de datos
require_once '../api/config.php';

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Obtiene el ID del proyecto a eliminar desde la URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de proyecto no proporcionado.');
}

try {
    // Prepara y ejecuta la eliminación del proyecto en la base de datos
    $stmt = $conn->prepare('DELETE FROM proyectos WHERE id = ?');
    $stmt->execute([$id]);
    // Redirige al index con mensaje de éxito
    header('Location: index.php?msg=deleted');
    exit;
} catch (PDOException $e) {
    die('Error al eliminar el proyecto.');
}
?>
