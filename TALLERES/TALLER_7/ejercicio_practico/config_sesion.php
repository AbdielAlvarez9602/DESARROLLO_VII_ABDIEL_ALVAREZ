<?php
// Configurar opciones de sesión antes de iniciarla
ini_set('session.cookie_httponly', 1); // Evita que las cookies de sesión sean accesibles mediante scripts de JavaScript
ini_set('session.use_only_cookies', 1); // Fuerza el uso de cookies para el manejo de sesiones
ini_set('session.cookie_secure', 0); // Establecer en 1 si se utiliza HTTPS

session_start();

// Regenerar el ID de sesión periódicamente para prevenir ataques de fijación de sesión
if (!isset($_SESSION['ultima_regeneracion'])) {
    $_SESSION['ultima_regeneracion'] = time();
} elseif (time() - $_SESSION['ultima_regeneracion'] > 300) { // Cada 5 minutos
    session_regenerate_id(true);
    $_SESSION['ultima_regeneracion'] = time();
}
?>