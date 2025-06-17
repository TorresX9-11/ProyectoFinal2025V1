<?php
function checkAuth() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(["error" => "No autorizado"]);
        exit;
    }
    return $_SESSION['user_id'];
}

function requireAuth($method) {
    // Solo verificar autenticación para métodos que modifican datos
    if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
        return checkAuth();
    }
    return null;
}
