<?php
require_once "config.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if ($_POST['action'] == 'prestar') {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("SELECT disponible FROM libros WHERE id = ?");
            $stmt->execute([$_POST['libro_id']]);
            $libro = $stmt->fetch();

            if ($libro && $libro['disponible'] > 0) {
                $pdo->prepare("INSERT INTO prestamos (usuario_id, libro_id, fecha_prestamo) VALUES (?, ?, NOW())")
                    ->execute([$_POST['usuario_id'], $_POST['libro_id']]);
                
                $pdo->prepare("UPDATE libros SET disponible = disponible - 1 WHERE id = ?")
                    ->execute([$_POST['libro_id']]);
                
                $pdo->commit();
                $msg = "Préstamo registrado.";
            } else {
                throw new Exception("Sin stock.");
            }
        } elseif ($_POST['action'] == 'devolver') {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("SELECT libro_id FROM prestamos WHERE id = ? AND estado = 'activo'");
            $stmt->execute([$_POST['prestamo_id']]);
            $prestamo = $stmt->fetch();

            if ($prestamo) {
                $pdo->prepare("UPDATE prestamos SET estado='devuelto', fecha_devolucion=NOW() WHERE id=?")
                    ->execute([$_POST['prestamo_id']]);
                
                $pdo->prepare("UPDATE libros SET disponible = disponible + 1 WHERE id = ?")
                    ->execute([$prestamo['libro_id']]);
                
                $pdo->commit();
                $msg = "Libro devuelto.";
            } else {
                throw new Exception("Préstamo inválido.");
            }
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        $msg = "Error: " . $e->getMessage();
    }
}

$sql = "SELECT p.id, u.nombre, l.titulo, p.fecha_prestamo, p.estado 
        FROM prestamos p 
        JOIN usuarios u ON p.usuario_id = u.id 
        JOIN libros l ON p.libro_id = l.id 
        ORDER BY p.id DESC";
$prestamos = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html>
<body>
    <a href="index.php">Volver</a>
    <h2>Préstamos (PDO)</h2>
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
        <?php foreach ($prestamos as $p): ?>
        <tr>
            <td><?= $p['id'] ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['titulo']) ?></td>
            <td><?= $p['fecha_prestamo'] ?></td>
            <td><?= $p['estado'] ?></td>
            <td>
                <?php if($p['estado'] == 'activo'): ?>
                <form method="POST">
                    <input type="hidden" name="action" value="devolver">
                    <input type="hidden" name="prestamo_id" value="<?= $p['id'] ?>">
                    <button>Devolver</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>