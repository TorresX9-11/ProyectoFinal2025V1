<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['user_id'])) {
    header('Location: /test1/public/login');
    exit;
}

require_once __DIR__ . '/../../api/config.php';
require_once __DIR__ . '/../Auth.php';

$auth = new Auth($conn);
$user = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username']
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Portafolio</title>
    <link rel="stylesheet" href="/test1/public/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <header class="admin-header">
            <h1>Panel de Administración</h1>
            <div class="user-info">
                Bienvenido, <?php echo htmlspecialchars($user['username']); ?>
                <a href="/test1/public/logout" class="logout-btn">Cerrar Sesión</a>
            </div>
        </header>
        
        <nav class="admin-nav">
            <ul>
                <li><a href="/test1/public/admin" class="active">Dashboard</a></li>
                <li><a href="/test1/public/admin/proyectos">Proyectos</a></li>
                <li><a href="/test1/public/admin/categorias">Categorías</a></li>
            </ul>
        </nav>
        
        <main class="admin-content">
            <div class="dashboard-stats">
                <!-- Aquí irán las estadísticas -->
            </div>
            
            <div class="recent-projects">
                <h2>Proyectos Recientes</h2>
                <!-- Aquí irá la lista de proyectos recientes -->
            </div>
        </main>
    </div>
    <script src="/test1/public/assets/js/admin.js"></script>
</body>
</html>
