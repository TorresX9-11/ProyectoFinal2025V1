<?php
// Inicia la sesión para acceder a variables de sesión
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Incluye la configuración y conexión a la base de datos
require_once '../api/config.php';

// Intenta obtener los datos del usuario logueado
try {
    $stmtUser = $conn->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
    $stmtUser->execute([$_SESSION['user_id']]);
    $user = $stmtUser->fetch();
    $isAdmin = $user && $user['is_admin'] == 1;
} catch (PDOException $e) {
    die("Error al obtener datos del usuario");
}

// Intenta obtener los proyectos: todos si es admin, solo propios si no
try {
    if ($isAdmin) {
        $stmt = $conn->query("
            SELECT p.*, c.nombre as categoria_nombre, u.username as autor
            FROM proyectos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            LEFT JOIN usuarios u ON p.usuario_id = u.id
            ORDER BY p.created_at DESC
        ");
        $proyectos = $stmt->fetchAll();
    } else {
        $stmt = $conn->prepare("
            SELECT p.*, c.nombre as categoria_nombre
            FROM proyectos p
            LEFT JOIN categorias c ON p.categoria_id = c.id
            WHERE p.usuario_id = ?
            ORDER BY p.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $proyectos = $stmt->fetchAll();
    }
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
    <link rel="stylesheet" href="../assets/css/indexPhp.css">
    <link rel="shortcut icon" href="assets/img/imgLogoSinfondo.png" type="image/x-icon">
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-brand">
            <img src="../assets/img/imgLogoSinfondo.png" alt="Logo Emanuel Torres" class="logo-nav" style="height:32px;vertical-align:middle;margin-right:8px;">
            Panel de Administración
        </div>
        <button class="menu-toggle" id="menu-toggle" aria-label="Abrir menú">
            <span></span>
            <span></span>
            <span></span>
        </button>
        <div class="nav-links" id="nav-links">
            <a href="../index.html" target="_blank">Ver Sitio</a>
            <?php if (!$isAdmin): ?>
            <a href="add.php" class="btn btn-primary">+ Nuevo Proyecto</a>
            <?php endif; ?>
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
            <ul class="metadata-list">
                <li><b>Categoría:</b> <?= htmlspecialchars($p['categoria_nombre'] ?? 'Sin categoría') ?></li>
                <li><b>Estado:</b> <?= htmlspecialchars($p['estado']) ?></li>
                <li><b>Inicio:</b> <?= htmlspecialchars($p['fecha_inicio']) ?><?= $p['fecha_fin'] ? ' | <b>Fin:</b> ' . htmlspecialchars($p['fecha_fin']) : '' ?></li>
                <li><b>Destacado:</b> <?= $p['destacado'] ? 'Sí' : 'No' ?></li>
                <li><b>Visible:</b> <?= $p['visible'] ? 'Sí' : 'No' ?></li>
            </ul>
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
                <?php if (!$isAdmin): ?>
                <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-primary">Editar</a>
                <?php endif; ?>
                <a href="delete.php?id=<?= $p['id'] ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de que quieres eliminar este proyecto?')">Eliminar</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
