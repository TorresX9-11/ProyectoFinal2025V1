<?php
$id=intval($_GET['id']);
$json=file_get_contents("../api/proyectos.php/$id");
$p=json_decode($json,true);

if ($_SERVER['REQUEST_METHOD']=='POST') {
  $data = [
    'titulo'=>$_POST['titulo'],
    'descripcion'=>$_POST['descripcion'],
    'url_github'=>$_POST['url_github'],
    'url_produccion'=>$_POST['url_produccion']
  ];
  if (!empty($_FILES['imagen']['name'])) {
    $img=$_FILES['imagen']['name'];
    move_uploaded_file($_FILES['imagen']['tmp_name'],"uploads/$img");
    $data['imagen']=$img;
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