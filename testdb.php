<?php
// Este archivo sirve para probar la conexión a la base de datos
// Incluye la configuración y conexión a la base de datos
require_once 'api/config.php';
// Realiza una consulta simple para verificar la conexión
$stmt = $conn->query('SELECT 1');
// Muestra mensaje si la conexión y consulta son exitosas
echo 'Conexión exitosa';
?>
