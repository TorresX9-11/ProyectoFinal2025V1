<?php
$json = file_get_contents('../api/proyectos.php');
$proyectos = json_decode($json, true);
?>
<!DOCTYPE html>
<html lang="es">
<head>…</head>
<body>
  <h2>Proyectos</h2>
  <a href="add.php">+ Agregar</a>  <?php foreach ($proyectos as $p): ?>
    <div class="proyecto-card <?= $p['destacado'] ? 'destacado' : '' ?>">
      <h3><?=htmlspecialchars($p['titulo'])?></h3>
      <div class="metadata">
        <span class="categoria"><?=htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría')?></span>
        <span class="estado"><?=htmlspecialchars($p['estado'])?></span>
      </div>
      <p class="descripcion"><?=htmlspecialchars($p['descripcion_corta'] ?? $p['descripcion'])?></p>
      <?php if ($p['imagen_principal']): ?>
        <img src="../uploads/<?=htmlspecialchars($p['imagen_principal'])?>" alt="<?=htmlspecialchars($p['titulo'])?>" class="imagen-principal">
      <?php endif; ?>
      <div class="tecnologias">
        <?php foreach (json_decode($p['tecnologias'], true) as $tech): ?>
          <span class="tech-tag"><?=htmlspecialchars($tech)?></span>
        <?php endforeach; ?>
      </div>
      <div class="enlaces">
        <?php if ($p['url_demo']): ?>
          <a href="<?=htmlspecialchars($p['url_demo'])?>" target="_blank" class="btn btn-demo">Ver Demo</a>
        <?php endif; ?>
        <?php if ($p['url_repositorio']): ?>
          <a href="<?=htmlspecialchars($p['url_repositorio'])?>" target="_blank" class="btn btn-repo">Repositorio</a>
        <?php endif; ?>
      </div>
      <div class="acciones">
        <a href="edit.php?id=<?=$p['id']?>" class="btn btn-editar">Editar</a>
        <a href="delete.php?id=<?=$p['id']?>" onclick="return confirm('¿Está seguro de que desea eliminar este proyecto?')" class="btn btn-eliminar">Eliminar</a>
      </div>
    </div>
    </div><hr>
  <?php endforeach; ?>
</body>
</html>