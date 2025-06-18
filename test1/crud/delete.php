<?php
$id = intval($_GET['id']);

// Primero obtener la información del proyecto para eliminar las imágenes
$ch = curl_init("../api/proyectos.php/$id");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true
]);
$proyecto = json_decode(curl_exec($ch), true);
curl_close($ch);

// Eliminar imágenes si existen
if ($proyecto && $proyecto['imagen_principal']) {
    $imagen_path = "../uploads/" . $proyecto['imagen_principal'];
    if (file_exists($imagen_path)) {
        unlink($imagen_path);
    }
}

// Eliminar el proyecto
$ch = curl_init("../api/proyectos.php/$id");
curl_setopt_array($ch, [
    CURLOPT_CUSTOMREQUEST => 'DELETE',
    CURLOPT_RETURNTRANSFER => true
]);
curl_exec($ch); curl_close($ch);
header("Location: index.php"); exit;
?>
