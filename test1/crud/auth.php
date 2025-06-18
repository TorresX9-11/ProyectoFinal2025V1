<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: /test1/public/login');
    exit;
}

require_once __DIR__ . '/../api/config.php';
