<?php
include 'config_sesion.php';

if (isset($_GET['id'])) {
    $producto_id = (int)$_GET['id'];

    if (isset($_SESSION['carrito'][$producto_id])) {
        unset($_SESSION['carrito'][$producto_id]);
    }
}

header("Location: ver_carrito.php");
exit();
?>