<?php
session_start();
include "data.php";

if (!isset($_SESSION['usuario'])) {
    header("Location: index.php");
    exit();
}

$rol = $_SESSION['rol'];

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
</head>
<body>

<h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
<p>Rol: <?php echo $rol; ?></p>

<hr>

<?php if ($rol === "Profesor"): ?>

    <h3>Listado de Estudiantes y Calificaciones</h3>

    <table border="1" cellpadding="5">
        <tr>
            <th>Usuario</th>
            <th>Calificación</th>
        </tr>

        <?php
        foreach ($usuarios as $u) {
            if ($u["rol"] === "Estudiante") {
                echo "<tr>";
                echo "<td>" . $u["usuario"] . "</td>";
                echo "<td>" . $u["calificacion"] . "</td>";
                echo "</tr>";
            }
        }
        ?>
    </table>

<?php else: ?>

    <h3>Tu Calificación</h3>
    <p>Calificación: <strong><?php echo $_SESSION['calificacion']; ?></strong></p>

<?php endif; ?>

<hr>

<a href="logout.php">Cerrar Sesión</a>

</body>
</html>