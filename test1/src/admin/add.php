<?php
require_once 'auth.php';
if ($_SERVER['REQUEST_METHOD']=='POST') {
  // Manejar la imagen principal si se subió
  $imagen_principal = null;
  if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
    $extension = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));
    $imagen_principal = uniqid() . '.' . $extension;
    move_uploaded_file($_FILES['imagen_principal']['tmp_name'], "../uploads/$imagen_principal");
  }

  // Procesar tecnologías como array JSON
  $tecnologias = !empty($_POST['tecnologias']) ? explode(',', $_POST['tecnologias']) : [];
  
  $data = [
    'titulo' => $_POST['titulo'],
    'descripcion' => $_POST['descripcion'],
    'descripcion_corta' => $_POST['descripcion_corta'],
    'tecnologias' => json_encode(array_map('trim', $tecnologias)),
    'url_demo' => $_POST['url_demo'],
    'url_repositorio' => $_POST['url_repositorio'],
    'fecha_inicio' => $_POST['fecha_inicio'],
    'fecha_fin' => $_POST['fecha_fin'],
    'estado' => $_POST['estado'],
    'categoria_id' => $_POST['categoria_id'],
    'destacado' => isset($_POST['destacado']) ? 1 : 0,
    'visible' => isset($_POST['visible']) ? 1 : 0,
    'imagen_principal' => $imagen_principal
  ];
  $ch=curl_init('../api/proyectos.php');
  curl_setopt_array($ch,[
    CURLOPT_CUSTOMREQUEST=>'POST',
    CURLOPT_HTTPHEADER=>['Content-Type: application/json'],
    CURLOPT_RETURNTRANSFER=>true,
    CURLOPT_POSTFIELDS=>json_encode($data)
  ]);
  curl_exec($ch); curl_close($ch);
  header("Location: index.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>…</head>
<body>
  <h2>Agregar Proyecto</h2>
  <form method="post" enctype="multipart/form-data">
    <input name="titulo" required>
    <textarea name="descripcion" maxlength="200" required></textarea>
    <input type="url" name="url_github">
    <input type="url" name="url_produccion">
    <input type="file" name="imagen" required>
    <button>Guardar</button>
  </form>
</body>
</html>