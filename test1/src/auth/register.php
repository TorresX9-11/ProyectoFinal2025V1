<?php
session_start();

// Generar token CSRF si no existe
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Si ya está autenticado, redirigir al panel de administración
if (isset($_SESSION['user_id'])) {
    header('Location: /test1/public/admin');
    exit;
}

require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../Auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Error de validación del formulario";
    } else {
        $username = trim(filter_var($_POST['username'] ?? '', FILTER_SANITIZE_STRING));
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));

        // Validaciones
        if (empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
            $error = "Todos los campos son obligatorios";
        } elseif ($password !== $confirm_password) {
            $error = "Las contraseñas no coinciden";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "El correo electrónico no es válido";
        } elseif (strlen($email) > 100) {
            $error = "El correo electrónico no puede exceder los 100 caracteres";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres";
        } elseif (strlen($username) < 3 || strlen($username) > 50) {
            $error = "El nombre de usuario debe tener entre 3 y 50 caracteres";
        } else {
            $auth = new Auth($conn);
            if ($auth->register($username, $password, $email)) {
                $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
                // Limpiar los campos después del registro exitoso
                $username = $email = '';
            } else {
                $error = "El nombre de usuario o correo electrónico ya existe";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Portafolio Emanuel Torres</title>
    <style>
        :root {
            --primary-color: #00CED1;
            --secondary-color: #ffffff;
            --error-color: #ff4444;
            --success-color: #00C851;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, var(--primary-color), #008B8B);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .container {
            background: var(--secondary-color);
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        
        h1 {
            color: var(--primary-color);
            text-align: center;
            margin-bottom: 2rem;
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        button {
            width: 100%;
            padding: 1rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        button:hover {
            background-color: #008B8B;
        }
        
        .error-message {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background-color: rgba(255, 68, 68, 0.1);
        }
        
        .success-message {
            color: var(--success-color);
            text-align: center;
            margin-bottom: 1rem;
            padding: 0.5rem;
            border-radius: 5px;
            background-color: rgba(0, 200, 81, 0.1);
        }
        
        .login-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Registro</h1>
        <?php if ($error): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="form-group">
                <label for="username">Nombre de usuario:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Correo electrónico:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Registrarse</button>
        </form>
        <div class="login-link">
            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar sesión</a></p>
        </div>
    </div>
</body>
</html>
