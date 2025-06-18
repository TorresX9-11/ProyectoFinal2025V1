<?php
session_start();
require_once '../api/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de proyecto no proporcionado.');
}

// Obtener proyecto actual
$stmt = $conn->prepare('SELECT * FROM proyectos WHERE id = ?');
$stmt->execute([$id]);
$proyecto = $stmt->fetch();
if (!$proyecto) {
    die('Proyecto no encontrado.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
    $categoria_id = $_POST['categoria_id'] ?? null;
    $estado = $_POST['estado'] ?? 'activo';
    $imagen_principal = $proyecto['imagen_principal'];

    // Manejo de imagen
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen_principal']['name']);
        $rutaDestino = '../uploads/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $rutaDestino)) {
            $imagen_principal = $nombreArchivo;
        }
    }

    if ($titulo && $descripcion) {
        try {
            $stmt = $conn->prepare("UPDATE proyectos SET titulo=?, descripcion=?, descripcion_corta=?, imagen_principal=?, categoria_id=?, estado=? WHERE id=?");
            $stmt->execute([$titulo, $descripcion, $descripcion_corta, $imagen_principal, $categoria_id, $estado, $id]);
            $success = 'Proyecto actualizado correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al actualizar el proyecto.';
        }
    } else {
        $error = 'Título y descripción son obligatorios.';
    }
}

$categorias = $conn->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="../assets/css/styleadmin.css">
</head>
<body>
    <nav class="admin-nav">
        <div class="nav-brand">Panel de Administración</div>
        <div class="nav-links">
            <a href="index.php">Volver</a>
        </div>
    </nav>
    <div class="container">
        <h2>Editar Proyecto</h2>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" value="<?= htmlspecialchars($proyecto['titulo']) ?>" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" required><?= htmlspecialchars($proyecto['descripcion']) ?></textarea>
            </div>
            <div class="form-group">
                <label for="descripcion_corta">Descripción corta</label>
                <input type="text" name="descripcion_corta" id="descripcion_corta" value="<?= htmlspecialchars($proyecto['descripcion_corta']) ?>">
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select name="categoria_id" id="categoria_id">
                    <option value="">Sin categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $proyecto['categoria_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="activo" <?= $proyecto['estado'] == 'activo' ? 'selected' : '' ?>>Activo</option>
                    <option value="inactivo" <?= $proyecto['estado'] == 'inactivo' ? 'selected' : '' ?>>Inactivo</option>
                    <option value="borrador" <?= $proyecto['estado'] == 'borrador' ? 'selected' : '' ?>>Borrador</option>
                </select>
            </div>
            <div class="form-group">
                <label for="imagen_principal">Imagen principal</label>
                <?php if ($proyecto['imagen_principal']): ?>
                    <img src="../uploads/<?= htmlspecialchars($proyecto['imagen_principal']) ?>" alt="Imagen actual" style="max-width:150px;display:block;margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
    </div>
</body>
</html>
