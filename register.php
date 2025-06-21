<?php
// Inicia la sesión para acceder a variables de sesión
session_start();
// Incluye la configuración y conexión a la base de datos
require_once __DIR__ . '/api/config.php';

// Inicializa variables para mensajes de error y éxito
$error = '';
$success = '';
// Si el formulario fue enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario
    $username = trim(filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING));
    $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    $keyaccess = $_POST['keyaccess'] ?? '';

    // Validaciones de campos obligatorios y formato
    if (!$username || !$email || !$password || !$confirm || !$keyaccess) {
        $error = 'Todos los campos son obligatorios';
    } elseif ($keyaccess !== 'keyacces528') {
        $error = 'La llave de acceso es incorrecta';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo electrónico no es válido';
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = 'El usuario debe tener entre 3 y 50 caracteres';
    } elseif (strlen($email) > 100) {
        $error = 'El correo electrónico no puede exceder los 100 caracteres';
    } elseif (strlen($password) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres';
    } elseif ($password !== $confirm) {
        $error = 'Las contraseñas no coinciden';
    } else {
        // Verifica que el usuario o email no existan ya en la base de datos
        $stmt = $conn->prepare('SELECT id FROM usuarios WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $error = 'El usuario o email ya existe';
        } else {
            // Si todo es válido, crea el usuario
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO usuarios (username, password_hash, email) VALUES (?, ?, ?)');
            $stmt->execute([$username, $hash, $email]);
            $success = '¡Registro exitoso! Ahora puedes iniciar sesión.';
            // Muestra alerta y redirige a login
            echo '<script>alert("¡Registro exitoso! Ahora puedes iniciar sesión.");window.location.href="login.php";</script>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="assets/css/styleauth.css">
    <link rel="shortcut icon" href="assets/img/imgLogoSinfondo.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Registro</h1>
        <?php if ($error): ?><div class="error-message"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="success-message"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="keyaccess">Llave de acceso</label>
                <input type="password" name="keyaccess" id="keyaccess" required placeholder="Ingresa la llave de acceso">
            </div>
            <button type="submit">Registrarse</button>
        </form>
    </div>
</body>
</html>
