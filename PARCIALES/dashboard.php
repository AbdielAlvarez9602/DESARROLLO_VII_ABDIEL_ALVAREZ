<?php
session_start();
require 'funciones.php';

if (!isset($_SESSION['usuario'])) {
    header('Location: sesion.php');
    exit();
}

$tareas = $_SESSION['tareas'] ?? [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tareas</title>
</head>
<body>
    <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
    
    <h3>Lista de Tareas</h3>
    <?php if (empty($tareas)): ?>
        <p>No hay tareas por hacer.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($tareas as $tarea): ?>
                <li><?php echo htmlspecialchars($tarea['titulo']) . " - " . htmlspecialchars($tarea['fecha']); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <h3>Agregar Nueva Tarea</h3>
    <form method="POST" action="agregar_tarea.php">
        <label for="titulo">Título:</label>
        <input type="text" name="titulo" id="titulo" required><br><br>
        <label for="fecha">Fecha Límite:</label>
        <input type="date" name="fecha" id="fecha" required><br><br>
        <input type="submit" value="Agregar Tarea">
    </form>

    <br><a href="logout.php">Cerrar Sesión</a>
</body>
</html>