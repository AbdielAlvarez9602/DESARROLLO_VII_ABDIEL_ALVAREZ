<?php
include 'config_sesion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;
    $cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 1;

    if ($producto_id > 0 && $cantidad > 0 && isset($productos[$producto_id])) {
        if (isset($_SESSION['carrito'][$producto_id])) {
            $_SESSION['carrito'][$producto_id]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$producto_id] = [
                'id' => $producto_id,
                'nombre' => $productos[$producto_id]['nombre'],
                'precio' => $productos[$producto_id]['precio'],
                'cantidad' => $cantidad
            ];
        }
    }
}

header("Location: productos.php");
exit();
?>