<?php
// Este archivo sirve para probar el hash de contraseñas en PHP
// Puedes modificar la contraseña aquí para generar su hash
$password = '12345678';
// Genera el hash usando el algoritmo por defecto de PHP
$hash = password_hash($password, PASSWORD_DEFAULT);
// Muestra el hash generado
echo "El hash de la contraseña '$password' es: $hash\n";

// Verifica si una contraseña coincide con el hash
// Cambia '$hash' por el hash que deseas verificar
if (password_verify('12345678', $hash)) {
    echo "La contraseña es válida.\n";
} else {
    echo "La contraseña no es válida.\n";
}

// Obtiene información sobre el hash, como el algoritmo y el costo
$info = password_get_info($hash);
echo "Información del hash: \n";
print_r($info);