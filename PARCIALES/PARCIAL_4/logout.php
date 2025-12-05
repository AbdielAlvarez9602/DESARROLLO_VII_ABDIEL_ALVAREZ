<?php
session_start(); // Necesitamos iniciarla para poder destruirla.

// Paso 1: Vaciar el array $_SESSION
$_SESSION = array();

// Paso 2: Borrar la cookie del navegador.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, // Ponemos tiempo en el pasado para que expire ya
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Paso 3: Destruir la sesión completamente en el servidor.
session_destroy();

// Paso 4: Mandar al usuario al inicio
header("Location: index.php");
exit;
?>
