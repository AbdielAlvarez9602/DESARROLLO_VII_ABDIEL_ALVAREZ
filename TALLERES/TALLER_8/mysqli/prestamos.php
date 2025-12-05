<?php
require_once "config.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_begin_transaction($conn);
    try {
        if ($_POST['action'] == 'prestar') {
            $sql = "SELECT disponible FROM libros WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $_POST['libro_id']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $libro = mysqli_fetch_assoc($res);

            if ($libro && $libro['disponible'] > 0) {
                $sql2 = "INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo) VALUES (?, ?, NOW())";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "ii", $_POST['usuario_id'], $_POST['libro_id']);
                mysqli_stmt_execute($stmt2);

                $sql3 = "UPDATE libros SET disponible = disponible - 1 WHERE id = ?";
                $stmt3 = mysqli_prepare($conn, $sql3);
                mysqli_stmt_bind_param($stmt3, "i", $_POST['libro_id']);
                mysqli_stmt_execute($stmt3);

                mysqli_commit($conn);
                $msg = "Préstamo registrado.";
            } else {
                throw new Exception("Sin stock.");
            }
        } elseif ($_POST['action'] == 'devolver') {
            $sql = "SELECT libro_id FROM prestamos WHERE id = ? AND estado = 'activo'";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "i", $_POST['prestamo_id']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            $prestamo = mysqli_fetch_assoc($res);

            if ($prestamo) {
                $sql2 = "UPDATE prestamos SET estado='devuelto', fecha_devolucion=NOW() WHERE id=?";
                $stmt2 = mysqli_prepare($conn, $sql2);
                mysqli_stmt_bind_param($stmt2, "i", $_POST['prestamo_id']);
                mysqli_stmt_execute($stmt2);

                $sql3 = "UPDATE libros SET disponible = disponible + 1 WHERE id = ?";
                $stmt3 = mysqli_prepare($conn, $sql3);
                mysqli_stmt_bind_param($stmt3, "i", $prestamo['libro_id']);
                mysqli_stmt_execute($stmt3);

                mysqli_commit($conn);
                $msg = "Libro devuelto.";
            } else {
                throw new Exception("Préstamo no válido.");
            }
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $msg = "Error: " . $e->getMessage();
    }
}

$sql = "SELECT p.id, u.nombre, l.titulo, p.fecha_prestamo, p.estado 
        FROM prestamos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        JOIN libros l ON p.libro_id = l.id 
        ORDER BY p.id DESC";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html>
<body>
    <a href="index.php">Volver</a>
    <h2>Préstamos (MySQLi)</h2>
    <p><?= $msg ?></p>
    <form method="POST">
        <input type="hidden" name="action" value="prestar">
        ID Usuario: <input type="number" name="usuario_id" required>
        ID Libro: <input type="number" name="libro_id" required>
        <button type="submit">Prestar</button>
    </form>
    <br>
    <table border="1">
        <tr><th>ID</th><th>Usuario</th><th>Libro</th><th>Fecha</th><th>Estado</th><th>Acción</th></tr>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
        <tr>
            <td><?= $row['id'] ?></td>
            <td><?= htmlspecialchars($row['nombre']) ?></td>
            <td><?= htmlspecialchars($row['titulo']) ?></td>
            <td><?= $row['fecha_prestamo'] ?></td>
            <td><?= $row['estado'] ?></td>
            <td>
                <?php if($row['estado'] == 'activo'): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="devolver">
                    <input type="hidden" name="prestamo_id" value="<?= $row['id'] ?>">
                    <button>Devolver</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>