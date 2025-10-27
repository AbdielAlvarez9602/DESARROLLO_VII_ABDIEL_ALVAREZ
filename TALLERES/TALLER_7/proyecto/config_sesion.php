<?php
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

session_start();

if (!isset($_SESSION['ultima_actividad']) || (time() - $_SESSION['ultima_actividad'] > 300)) {
    session_regenerate_id(true);
    $_SESSION['ultima_actividad'] = time();
}

if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

$productos = [
    1 => ['nombre' => 'Laptop Gamer', 'precio' => 1200.00],
    2 => ['nombre' => 'Monitor 4K', 'precio' => 450.00],
    3 => ['nombre' => 'Teclado Mecánico', 'precio' => 90.00],
    4 => ['nombre' => 'Mouse Inalámbrico', 'precio' => 35.00],
    5 => ['nombre' => 'Auriculares Pro', 'precio' => 150.00]
];
?>