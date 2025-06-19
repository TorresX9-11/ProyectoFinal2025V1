<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

require_once '../api/config.php';

// Obtener solo los proyectos del usuario logueado
try {
    $stmt = $conn->prepare("
        SELECT p.*, c.nombre as categoria_nombre 
        FROM proyectos p 
        LEFT JOIN categorias c ON p.categoria_id = c.id 
        WHERE p.usuario_id = ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $proyectos = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error al obtener los proyectos");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Proyectos</title>
    <link rel="stylesheet" href="../assets/css/styleadmin.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-brand">Panel de Administración</div>
        <div class="nav-links">
            <a href="../index.html" target="_blank">Ver Sitio</a>
            <a href="add.php" class="btn btn-primary">+ Nuevo Proyecto</a>
            <a href="logout.php" class="btn btn-danger">Cerrar Sesión</a>
        </div>
    </nav>

    <div class="container">
        <h2>Gestión de Proyectos</h2>
        <?php if (empty($proyectos)): ?>
            <div class="alert alert-info">No tienes proyectos aún. ¡Agrega tu primer proyecto!</div>
        <?php endif; ?>
        <?php foreach ($proyectos as $p): ?>
        <div class="proyecto-card <?= $p['destacado'] ? 'destacado' : '' ?>">
            <h3><?= htmlspecialchars($p['titulo']) ?></h3>
            <div class="metadata">
                <span class="categoria">Categoría: <?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></span>
                <span class="estado">Estado: <?= htmlspecialchars($p['estado']) ?></span>
                <span class="fechas">Inicio: <?= htmlspecialchars($p['fecha_inicio']) ?><?= $p['fecha_fin'] ? ' | Fin: ' . htmlspecialchars($p['fecha_fin']) : '' ?></span>
                <span class="destacado">Destacado: <?= $p['destacado'] ? 'Sí' : 'No' ?></span>
                <span class="visible">Visible: <?= $p['visible'] ? 'Sí' : 'No' ?></span>
            </div>
            <p class="descripcion"><b>Descripción:</b> <?= htmlspecialchars($p['descripcion']) ?></p>
            <?php if ($p['descripcion_corta']): ?>
                <p class="descripcion-corta"><b>Descripción corta:</b> <?= htmlspecialchars($p['descripcion_corta']) ?></p>
            <?php endif; ?>
            <?php if ($p['tecnologias']): ?>
                <p class="tecnologias"><b>Tecnologías:</b> <?= htmlspecialchars($p['tecnologias']) ?></p>
            <?php endif; ?>
            <?php if ($p['url_demo']): ?>
                <p class="url-demo"><a href="<?= htmlspecialchars($p['url_demo']) ?>" target="_blank">Ver Demo</a></p>
            <?php endif; ?>
            <?php if ($p['url_repositorio']): ?>
                <p class="url-repo"><a href="<?= htmlspecialchars($p['url_repositorio']) ?>" target="_blank">Repositorio</a></p>
            <?php endif; ?>
            <?php if ($p['imagen_principal']): ?>
                <img src="../uploads/<?= htmlspecialchars($p['imagen_principal']) ?>" alt="<?= htmlspecialchars($p['titulo']) ?>" class="imagen-principal">
            <?php endif; ?>
            <div class="acciones">
                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-primary">Editar</a>
                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este proyecto?')">Eliminar</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
