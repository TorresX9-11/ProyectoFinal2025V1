<?php
// Inicia la sesión para acceder a variables de sesión
session_start();
// Incluye la configuración y conexión a la base de datos
require_once __DIR__ . '/api/config.php';

// Inicializa variable para mensajes de error
$error = '';
// Si el formulario fue enviado (método POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtiene y limpia los datos del formulario
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Prepara y ejecuta la consulta para buscar el usuario
    $stmt = $conn->prepare('SELECT id, username, password_hash FROM usuarios WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    // Verifica si el usuario existe y la contraseña es correcta
    if ($user && password_verify($password, $user['password_hash'])) {
        // Guarda datos en la sesión y redirige al panel
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: crud/index.php');
        exit;
    } else {
        // Si no coincide, muestra mensaje de error
        $error = 'Usuario o contraseña incorrectos';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/styleauth.css">
    <link rel="shortcut icon" href="assets/img/imgLogoSinfondo.png" type="image/x-icon">
</head>
<body>
    <div class="container">
        <h1>Iniciar Sesión</h1>
        <?php if ($error): ?><div class="error-message"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="username">Usuario</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" name="password" id="password" required>
            </div>
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
