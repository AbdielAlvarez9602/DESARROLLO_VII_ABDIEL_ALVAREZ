<?php
// Crear una cookie que expira en 1 hora
setcookie("usuario", "Abdiel", time() + 3600, "/");

echo "Cookie 'usuario' creada.";
?>
