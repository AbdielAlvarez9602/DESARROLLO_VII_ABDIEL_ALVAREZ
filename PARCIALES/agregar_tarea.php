<?php
session_start();
require 'funciones.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: sesion.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $titulo = $_POST['titulo'];
    $fecha = $_POST['fecha'];

    if (empty($titulo) || empty($fecha)) {
        $error = 'Todos los campos son obligatorios';
    } elseif (!validar_fecha_futura($fecha)) {
        $error = 'La fecha debe ser futura';
    } else {

        $_SESSION['tareas'][] = ['titulo' => $titulo, 'fecha' => $fecha];
        header('Location: dashboard.php');
        exit();
    }
}

if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
