<?php
// Permite solicitudes desde cualquier origen (CORS)
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Endpoint de prueba para verificar que la API responde correctamente
echo json_encode(['status' => 'ok', 'message' => 'API funcionando']);
?>
