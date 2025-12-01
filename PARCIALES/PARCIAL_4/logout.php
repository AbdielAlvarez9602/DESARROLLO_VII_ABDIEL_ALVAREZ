<?php
session_start();

// Destruye todas las variables de sesión
$_SESSION = array();

// Si se desea destruir la cookie de sesión, se debe borrar
// antes de destruir la sesión.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finalmente, destruye la sesión.
session_destroy();

// Redirige al usuario a la página de inicio (index.php)
header("Location: index.php");
exit;
?>