<?php
session_start();
require_once '../api/config.php';

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
    $estado = $_POST['estado'] ?? 'en_desarrollo';
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $visible = isset($_POST['visible']) ? 1 : 0;
    $imagen_principal = null;
    $usuario_id = $_SESSION['user_id'];

    // Manejo de imagen
    if (isset($_FILES['imagen_principal']) && $_FILES['imagen_principal']['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . '_' . basename($_FILES['imagen_principal']['name']);
        $rutaDestino = '../uploads/' . $nombreArchivo;
        if (move_uploaded_file($_FILES['imagen_principal']['tmp_name'], $rutaDestino)) {
            $imagen_principal = $nombreArchivo;
        }
    }

    if ($titulo && $descripcion && $categoria_id && $tecnologias) {
        // Validar que el campo tecnologias sea un JSON válido
        json_decode($tecnologias);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'El campo Tecnologías debe ser un JSON válido, por ejemplo: ["PHP","MySQL"]';
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO proyectos (titulo, descripcion, descripcion_corta, tecnologias, url_demo, url_repositorio, fecha_inicio, fecha_fin, estado, categoria_id, usuario_id, destacado, visible, imagen_principal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
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
                    $usuario_id,
                    $destacado,
                    $visible,
                    $imagen_principal
                ]);
                $success = 'Proyecto agregado correctamente.';
            } catch (PDOException $e) {
                $error = 'Error al agregar el proyecto: ' . $e->getMessage();
            }
        }
    } else {
        $error = 'Título, descripción, categoría y tecnologías son obligatorios.';
    }
}

// Obtener categorías para el select
$categorias = $conn->query('SELECT * FROM categorias ORDER BY nombre')->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Proyecto</title>
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
        <h2>Agregar Proyecto</h2>
        <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="titulo">Título</label>
                <input type="text" name="titulo" id="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <textarea name="descripcion" id="descripcion" required></textarea>
            </div>
            <div class="form-group">
                <label for="descripcion_corta">Descripción corta</label>
                <input type="text" name="descripcion_corta" id="descripcion_corta">
            </div>
            <div class="form-group">
                <label for="tecnologias_input">Tecnologías (separadas por coma)</label>
                <input type="text" id="tecnologias_input" placeholder="Ej: PHP, JavaScript, MySQL" required>
                <input type="hidden" name="tecnologias" id="tecnologias">
            </div>
            <div class="form-group">
                <label for="url_demo">URL Demo</label>
                <input type="url" name="url_demo" id="url_demo">
            </div>
            <div class="form-group">
                <label for="url_repositorio">URL Repositorio</label>
                <input type="url" name="url_repositorio" id="url_repositorio">
            </div>
            <div class="form-group">
                <label for="fecha_inicio">Fecha de inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio">
            </div>
            <div class="form-group">
                <label for="fecha_fin">Fecha de fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin">
            </div>
            <div class="form-group">
                <label for="categoria_id">Categoría</label>
                <select name="categoria_id" id="categoria_id" required>
                    <option value="" disabled selected>Selecciona una categoría</option>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="estado">Estado</label>
                <select name="estado" id="estado">
                    <option value="en_desarrollo">En desarrollo</option>
                    <option value="completado">Completado</option>
                    <option value="pausado">Pausado</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="destacado" value="1"> Destacado</label>
                <label><input type="checkbox" name="visible" value="1" checked> Visible</label>
            </div>
            <div class="form-group">
                <label for="imagen_principal">Imagen principal</label>
                <input type="file" name="imagen_principal" id="imagen_principal" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Agregar</button>
        </form>
    </div>
    <script>
    // Al enviar el formulario, transforma las tecnologías a JSON
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
    </script>
</body>
</html>
