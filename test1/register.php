<?php
session_start();

// Si ya está autenticado, redirigir al panel de administración
if (isset($_SESSION['user_id'])) {
    header('Location: crud/index.php');
    exit;
}

require_once 'api/config.php';
require_once 'classes/Auth.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');

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
        $error = "La contraseña debe tener al menos 6 caracteres";    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $error = "El nombre de usuario debe tener entre 3 y 50 caracteres";
    } else {
        $auth = new Auth($conn);
        if ($auth->register($username, $password, $email)) {
            $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
        } else {
            $error = "El nombre de usuario o correo electrónico ya existe";
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
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 206, 209, 0.1);
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid var(--primary-color);
            border-radius: 5px;
            background-color: var(--primary-color);
            color: white;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn:hover {
            background-color: transparent;
            color: var(--primary-color);
        }
        
        .error {
            background-color: #ffebee;
            color: #c62828;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .success {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            text-align: center;
        }
        
        .links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .links a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: opacity 0.3s ease;
        }
        
        .links a:hover {
            opacity: 0.8;
        }

        .links .separator {
            margin: 0 0.5rem;
            color: #666;
        }

        .password-requirements {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Cuenta</h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" action="" novalidate>
            <div class="form-group">
                <label for="username">Nombre de usuario</label>
                <input 
                    type="text" 
                    id="username" 
                    name="username" 
                    required 
                    value="<?php echo htmlspecialchars($username ?? ''); ?>"
                    minlength="3"
                    pattern="[A-Za-z0-9_-]+"
                >
                <div class="password-requirements">Mínimo 3 caracteres, solo letras, números, guiones y guiones bajos</div>
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    required
                    value="<?php echo htmlspecialchars($email ?? ''); ?>"
                >
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required
                    minlength="6"
                >
                <div class="password-requirements">Mínimo 6 caracteres</div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña</label>
                <input 
                    type="password" 
                    id="confirm_password" 
                    name="confirm_password" 
                    required
                >
            </div>

            <button type="submit" class="btn">Registrarse</button>
        </form>

        <div class="links">
            <a href="login.php">¿Ya tienes una cuenta? Inicia sesión</a>
            <span class="separator">|</span>
            <a href="index.html">Volver al inicio</a>
        </div>
    </div>
</body>
</html>
