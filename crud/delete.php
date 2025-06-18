<?php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de proyecto no proporcionado.');
}

try {
    $stmt = $conn->prepare('DELETE FROM proyectos WHERE id = ?');
    $stmt->execute([$id]);
    header('Location: index.php?msg=deleted');
    exit;
} catch (PDOException $e) {
    die('Error al eliminar el proyecto.');
}
