<?php
$json = file_get_contents('../api/proyectos.php');
$proyectos = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>…</head>
<body>
  <h2>Proyectos</h2>
  <a href="add.php">+ Agregar</a>
  <?php foreach ($proyectos as $p): ?>
    <div>
      <h3><?=htmlspecialchars($p['titulo'])?></h3>
      <p><?=htmlspecialchars($p['descripcion'])?></p>
      <img src="uploads/<?=htmlspecialchars($p['imagen'])?>" width="150">
      <p>
        <a href="<?=htmlspecialchars($p['url_github'])?>" target="_blank">GitHub</a>
        <a href="<?=htmlspecialchars($p['url_produccion'])?>" target="_blank">En producción</a>
      </p>
      <a href="edit.php?id=<?=$p['id']?>>Editar</a>
      <a href="delete.php?id=<?=$p['id']?>" onclick="return confirm('¿Seguro?')">Eliminar</a>
    </div><hr>
  <?php endforeach; ?>
</body>
</html>