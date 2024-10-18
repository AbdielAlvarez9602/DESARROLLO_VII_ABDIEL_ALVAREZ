<?php
include 'config_sesion.php';

// Validar y sanitizar datos recibidos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = filter_input(INPUT_POST, 'producto_id', FILTER_VALIDATE_INT);
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);

    if ($producto_id && $cantidad && $cantidad > 0) {
        // Verificar que el producto existe
        if (isset($_SESSION['productos'][$producto_id])) {
            // Añadir o actualizar el producto en el carrito
            if (isset($_SESSION['carrito'][$producto_id])) {
                $_SESSION['carrito'][$producto_id] += $cantidad;
            } else {
                $_SESSION['carrito'][$producto_id] = $cantidad;
            }
            header('Location: ver_carrito.php');
            exit();
        } else {
            die('Producto no válido.');
        }
    } else {
        die('Datos inválidos.');
    }
} else {
    die('Acceso no permitido.');
}
?>