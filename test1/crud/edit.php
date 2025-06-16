<?php
$id=intval($_GET['id']);
$json=file_get_contents("../api/proyectos.php/$id");
$p=json_decode($json,true);

if ($_SERVER['REQUEST_METHOD']=='POST') {
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
    'visible' => isset($_POST['visible']) ? 1 : 0
  ];

  // Manejar la imagen principal si se subió una nueva
  if (!empty($_FILES['imagen_principal']['name'])) {
    $extension = strtolower(pathinfo($_FILES['imagen_principal']['name'], PATHINFO_EXTENSION));
    $imagen_principal = uniqid() . '.' . $extension;
    
    // Eliminar imagen anterior si existe
    if ($p['imagen_principal']) {
      $imagen_path = "../uploads/" . $p['imagen_principal'];
      if (file_exists($imagen_path)) {
        unlink($imagen_path);
      }
    }
    
    // Subir nueva imagen
    move_uploaded_file($_FILES['imagen_principal']['tmp_name'], "../uploads/$imagen_principal");
    $data['imagen_principal'] = $imagen_principal;
  }
  $ch=curl_init("../api/proyectos.php/$id");
  curl_setopt_array($ch,[
    CURLOPT_CUSTOMREQUEST=>'PATCH',
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
  <h2>Editar: <?=htmlspecialchars($p['titulo'])?></h2>
  <form method="post" enctype="multipart/form-data">
    <input name="titulo" value="<?=htmlspecialchars($p['titulo'])?>" required>
    <textarea name="descripcion"><?=htmlspecialchars($p['descripcion'])?></textarea>
    <input name="url_github" value="<?=htmlspecialchars($p['url_github'])?>">
    <input name="url_produccion" value="<?=htmlspecialchars($p['url_produccion'])?>">
    <input type="file" name="imagen">
    <button>Actualizar</button>
  </form>
</body>
</html>