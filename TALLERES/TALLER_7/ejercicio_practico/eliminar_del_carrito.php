<?php
include 'config_sesion.php';

// Validar y sanitizar datos recibidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);

    if ($producto_id && isset($_SESSION['carrito'][$producto_id])) {
        // Eliminar el producto del carrito
        unset($_SESSION['carrito'][$producto_id]);
        header('Location: ver_carrito.php');
        exit();
    } else {
        die('Producto no válido.');
    }
} else {
    die('Acceso no permitido.');
}
?>