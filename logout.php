<?php
// Inicia la sesión para poder destruirla
session_start();
// Destruye la sesión actual (logout)
session_destroy();
// Redirige al usuario al index público
header('Location: /~emanuel.torres/index.html');
exit;
