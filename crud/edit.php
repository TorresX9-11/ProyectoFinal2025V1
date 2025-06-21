<?php
// Inicia la sesión para acceder a variables de sesión
session_start();
// Incluye la configuración y conexión a la base de datos
require_once '../api/config.php';

// Bloquea el acceso a la edición de proyectos si el usuario es administrador
$stmtUser = $conn->prepare("SELECT is_admin FROM usuarios WHERE id = ?");
$stmtUser->execute([$_SESSION['user_id']]);
$user = $stmtUser->fetch();
if ($user && $user['is_admin'] == 1) {
    die('Acceso denegado: el usuario administrador no puede editar proyectos.');
}

// Verifica si el usuario está autenticado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Obtiene el ID del proyecto a editar desde la URL
$id = $_GET['id'] ?? null;
if (!$id) {
    die('ID de proyecto no proporcionado.');
}

// Consulta el proyecto actual en la base de datos
$stmt = $conn->prepare('SELECT * FROM proyectos WHERE id = ?');
$stmt->execute([$id]);
$proyecto = $stmt->fetch();
if (!$proyecto) {
    die('Proyecto no encontrado.');
}

// Inicializa variables para mensajes de error y éxito
$error = '';
$success = '';

// Si el formulario fue enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario
    $titulo = trim($_POST['titulo'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $descripcion_corta = trim($_POST['descripcion_corta'] ?? '');
    $tecnologias = $_POST['tecnologias'] ?? '';
    $url_demo = trim($_POST['url_demo'] ?? '');
    $url_repositorio = trim($_POST['url_repositorio'] ?? '');
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
    if ($fecha_inicio === '') $fecha_inicio = null;
    if ($fecha_fin === '') $fecha_fin = null;
    $categoria_id = $_POST['categoria_id'] ?? null;
    $estado = $_POST['estado'] ?? 'en_dearrollo'; // OJO: posible typo en 'en_dearrollo'
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;
    $imagen_principal = $proyecto['imagen_principal'];

    // Si se subió una nueva imagen, la procesa y guarda
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen_principal']['name']);
        $rutaDestino = '../uploads/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $rutaDestino)) {
            $imagen_principal = $nombreArchivo;
        }
    }

    // Valida que los campos obligatorios estén completos
    if ($titulo && $descripcion && $categoria_id && $tecnologias) {
        // Valida que el campo tecnologías sea un JSON válido
        json_decode($tecnologias);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'El campo Tecnologías debe ser un JSON válido, por ejemplo: ["PHP","MySQL"]';
        } else {
            try {
                // Prepara y ejecuta la actualización del proyecto en la base de datos
                $stmt = $conn->prepare("UPDATE proyectos SET titulo=?, descripcion=?, descripcion_corta=?, tecnologias=?, url_demo=?, url_repositorio=?, fecha_inicio=?, fecha_fin=?, estado=?, categoria_id=?, destacado=?, visible=?, imagen_principal=? WHERE id=?");
                $stmt->execute([
                    $titulo,
                    $descripcion,
                    $descripcion_corta,
                    $tecnologias,
                    $url_demo,
                    $url_repositorio,
                    $fecha_inicio,
                    $fecha_fin,
                    $estado,
                    $categoria_id,
                    $destacado,
                    $visible,
                    $imagen_principal,
                    $id
                ]);
                $success = 'Proyecto actualizado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al actualizar el proyecto: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Título, descripción, categoría y tecnologías son obligatorios.';
    }
}

// Obtiene todas las categorías para el select del formulario
$categorias = $conn->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proyecto</title>
    <link rel="stylesheet" href="../assets/css/indexPhp.css">
    <link rel="shortcut icon" href="assets/img/imgLogoSinfondo.png" type="image/x-icon">
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
        <form method="POST" enctype="multipart/form-data" class="form-admin">
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
                <label for="tecnologias_input">Tecnologías (separadas por coma)</label>
                <input type="text" id="tecnologias_input" value="<?= htmlspecialchars(implode(', ', is_array(json_decode($proyecto['tecnologias'], true)) ? json_decode($proyecto['tecnologias'], true) : []) ) ?>" required>
                <input type="hidden" name="tecnologias" id="tecnologias">
            </div>
            <div class="form-group">
                <label for="url_demo">URL Demo</label>
                <input type="url" name="url_demo" id="url_demo" value="<?= htmlspecialchars($proyecto['url_demo']) ?>">
            </div>
            <div class="form-group">
                <label for="url_repositorio">URL Repositorio</label>
                <input type="url" name="url_repositorio" id="url_repositorio" value="<?= htmlspecialchars($proyecto['url_repositorio']) ?>">
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" value="<?= htmlspecialchars($proyecto['fecha_inicio']) ?>">
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" value="<?= htmlspecialchars($proyecto['fecha_fin']) ?>">
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select name="categoria_id" id="categoria_id" required>
                    <option value="" disabled>Selecciona una categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $proyecto['categoria_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="en_desarrollo" <?= $proyecto['estado'] == 'en_desarrollo' ? 'selected' : '' ?>>En desarrollo</option>
                    <option value="completado" <?= $proyecto['estado'] == 'completado' ? 'selected' : '' ?>>Completado</option>
                    <option value="pausado" <?= $proyecto['estado'] == 'pausado' ? 'selected' : '' ?>>Pausado</option>
                    <option value="cancelado" <?= $proyecto['estado'] == 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="destacado" value="1" <?= $proyecto['destacado'] ? 'checked' : '' ?>> Destacado</label>
                <label><input type="checkbox" name="visible" value="1" <?= $proyecto['visible'] ? 'checked' : '' ?>> Visible</label>
            </div>
            <div class="form-group">
                <label for="imagen_principal">Imagen principal</label>
                <?php if ($proyecto['imagen_principal']): ?>
                    <img src="../uploads/<?= htmlspecialchars($proyecto['imagen_principal']) ?>" alt="Imagen actual" style="max-width:150px;display:block;margin-bottom:10px;">
                <?php endif; ?>
                <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*">
                <small>Tamaño máximo: 2MB</small>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </form>
<script>
// Al enviar el formulario, transforma las tecnologías a JSON para el backend
const form = document.querySelector('form');
form.addEventListener('submit', function(e) {
    const input = document.getElementById('tecnologias_input');
    const hidden = document.getElementById('tecnologias');
    const arr = input.value
        .split(',')
        .map(t => t.trim())
        .filter(t => t.length > 0);
    hidden.value = JSON.stringify(arr);
});

// Validación de tamaño de imagen (máx 2MB)
document.getElementById('imagen_principal').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.size > 2 * 1024 * 1024) {
        alert('La imagen supera el tamaño máximo permitido (2MB). Por favor, selecciona otra imagen.');
        e.target.value = '';
    }
});
</script>
    </div>
</body>
</html>
